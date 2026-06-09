<?php

namespace App\Livewire;

use App\Models\Anomaly;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Journal;
use Livewire\Component;

class Dashboard extends Component
{
    public ?int $selectedJournalId = null;
    public string $period = '30'; // days

    public function mount(): void
    {
        $this->selectedJournalId = Journal::latest()->first()?->id;
    }

    public function selectJournal(int $id): void
    {
        $this->selectedJournalId = $id;
    }

    public function render()
    {
        $journal = $this->selectedJournalId
            ? Journal::with(['device.pointOfSale.company', 'anomalies'])->find($this->selectedJournalId)
            : null;

        // ── KPIs ─────────────────────────────────────────────────────────
        $totalTtc      = $journal?->total_ttc      ?? 0;
        $totalTva      = $journal?->total_tva      ?? 0;
        $totalInvoices = $journal?->total_invoices ?? 0;
        $totalCancelled= $journal?->total_cancelled ?? 0;
        $anomalyCount  = $journal ? $journal->anomalies->where('is_resolved', false)->count() : 0;

        // ── Daily CA chart data ───────────────────────────────────────────
        $dailySales = [];
        if ($journal) {
            $dailySales = Invoice::where('journal_id', $journal->id)
                ->where('type', 'sale')
                ->selectRaw('DATE(date_time) as day, SUM(total_ttc) as total, COUNT(*) as count')
                ->groupBy('day')
                ->orderBy('day')
                ->get()
                ->map(fn($r) => [
                    'date'  => $r->day,
                    'total' => (float) $r->total,
                    'count' => (int)   $r->count,
                ])
                ->toArray();
        }

        // ── Top 8 products ────────────────────────────────────────────────
        $topArticles = [];
        if ($journal) {
            $topArticles = InvoiceItem::join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                ->where('invoices.journal_id', $journal->id)
                ->where('invoices.type', 'sale')
                ->selectRaw('invoice_items.name, SUM(invoice_items.total) as revenue, SUM(invoice_items.qty) as qty')
                ->groupBy('invoice_items.name')
                ->orderByDesc('revenue')
                ->limit(8)
                ->get()
                ->toArray();
        }

        // ── Hourly heatmap data ───────────────────────────────────────────
        $hourlySales = [];
        if ($journal) {
            $isSqlite = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite';
            $hourField = $isSqlite ? "strftime('%H', date_time)" : "HOUR(date_time)";
            
            $results = Invoice::where('journal_id', $journal->id)
                ->where('type', 'sale')
                ->selectRaw("{$hourField} as hour_num, COUNT(*) as count")
                ->groupByRaw($hourField)
                ->get();

            foreach ($results as $r) {
                $hourlySales[(int)$r->hour_num] = (int)$r->count;
            }
        }

        // ── Recent journals list ──────────────────────────────────────────
        $journals = Journal::with('device.pointOfSale')
            ->latest()
            ->limit(10)
            ->get();

        // ── Recent anomalies ──────────────────────────────────────────────
        $recentAnomalies = $journal
            ? Anomaly::with('invoice')
                ->where('journal_id', $journal->id)
                ->where('is_resolved', false)
                ->orderByRaw("FIELD(severity,'critical','warning','info')")
                ->limit(5)
                ->get()
            : collect();

        return view('livewire.dashboard', compact(
            'journal',
            'totalTtc', 'totalTva', 'totalInvoices', 'totalCancelled', 'anomalyCount',
            'dailySales', 'topArticles', 'hourlySales',
            'journals', 'recentAnomalies',
        ))->layout('layouts.app');
    }
}
