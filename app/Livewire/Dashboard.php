<?php

namespace App\Livewire;

use App\Models\Anomaly;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Journal;
use App\Models\PointOfSale;
use App\Models\Device;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    // ── Filters ──────────────────────────────────────────────────────────
    public ?int $posId = null;
    public ?int $deviceId = null;
    public ?int $selectedJournalId = null;

    // ── Component Lifecycle ──────────────────────────────────────────────
    public function mount(): void
    {
        // Default: No filters selected, showing consolidated database-wide metrics
    }

    public function updatedPosId(): void
    {
        $this->deviceId = null;
        $this->selectedJournalId = null;
        $this->dispatch('charts-updated');
    }

    public function updatedDeviceId(): void
    {
        $this->selectedJournalId = null;
        $this->dispatch('charts-updated');
    }

    public function selectJournal(int $id): void
    {
        $this->selectedJournalId = $id;
        
        // Back-populate device and pos filters based on selected journal for UI consistency
        $journal = Journal::with('device.pointOfSale')->find($id);
        if ($journal && $journal->device) {
            $this->deviceId = $journal->device_id;
            $this->posId = $journal->device->point_of_sale_id;
        }
        $this->dispatch('charts-updated');
    }

    public function updated(): void
    {
        $this->dispatch('charts-updated');
    }

    public function clearFilters(): void
    {
        $this->posId = null;
        $this->deviceId = null;
        $this->selectedJournalId = null;
        $this->dispatch('charts-updated');
    }

    public function render()
    {
        $hasFilters = $this->selectedJournalId || $this->deviceId || $this->posId;
        $filteredJournalIds = [];

        if ($hasFilters) {
            if ($this->selectedJournalId) {
                $filteredJournalIds = [$this->selectedJournalId];
            } else {
                $journalQuery = Journal::query();
                if ($this->deviceId) {
                    $journalQuery->where('device_id', $this->deviceId);
                } elseif ($this->posId) {
                    $journalQuery->whereHas('device', function ($q) {
                        $q->where('point_of_sale_id', $this->posId);
                    });
                }
                $filteredJournalIds = $journalQuery->pluck('id')->toArray();
            }
        }

        // Helper function to apply journal filtration
        $applyJournalFilter = function ($query) use ($filteredJournalIds, $hasFilters) {
            if ($hasFilters) {
                if (empty($filteredJournalIds)) {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->whereIn('journal_id', $filteredJournalIds);
                }
            }
            return $query;
        };

        // ── KPIs ─────────────────────────────────────────────────────────────
        if ($hasFilters) {
            if (empty($filteredJournalIds)) {
                $totalTtc       = 0;
                $totalTva       = 0;
                $totalInvoices  = 0;
                $totalCancelled = 0;
            } else {
                $stats = Journal::whereIn('id', $filteredJournalIds)
                    ->selectRaw('SUM(total_ttc) as ttc, SUM(total_tva) as tva, SUM(total_invoices) as invoices, SUM(total_cancelled) as cancelled')
                    ->first();
                $totalTtc       = $stats->ttc ?? 0;
                $totalTva       = $stats->tva ?? 0;
                $totalInvoices  = $stats->invoices ?? 0;
                $totalCancelled = $stats->cancelled ?? 0;
            }
        } else {
            // Consolidated stats for the entire application
            $globalStats = Journal::selectRaw('SUM(total_ttc) as ttc, SUM(total_tva) as tva, SUM(total_invoices) as invoices, SUM(total_cancelled) as cancelled')
                ->first();
            $totalTtc       = $globalStats->ttc ?? 0;
            $totalTva       = $globalStats->tva ?? 0;
            $totalInvoices  = $globalStats->invoices ?? 0;
            $totalCancelled = $globalStats->cancelled ?? 0;
        }

        // Unresolved anomalies count
        $anomalyQuery = Anomaly::where('is_resolved', false);
        $anomalyQuery = $applyJournalFilter($anomalyQuery);
        $anomalyCount = $anomalyQuery->count();

        // ── Daily Sales evolution ────────────────────────────────────────────
        $dailySalesQuery = Invoice::where('type', 'sale')
            ->selectRaw('DATE(date_time) as day, SUM(total_ttc) as total, COUNT(*) as count')
            ->groupBy('day')
            ->orderBy('day');
        $dailySalesQuery = $applyJournalFilter($dailySalesQuery);
        
        $dailySales = $dailySalesQuery->get()
            ->map(fn($r) => [
                'date'  => $r->day,
                'total' => (float) $r->total,
                'count' => (int)   $r->count,
            ])
            ->toArray();

        // ── Top 8 Products ───────────────────────────────────────────────────
        $topArticlesQuery = InvoiceItem::join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoices.type', 'sale')
            ->selectRaw('invoice_items.name, SUM(invoice_items.total) as revenue, SUM(invoice_items.qty) as qty')
            ->groupBy('invoice_items.name')
            ->orderByDesc('revenue')
            ->limit(8);

        if ($hasFilters) {
            if (empty($filteredJournalIds)) {
                $topArticlesQuery->whereRaw('1 = 0');
            } else {
                $topArticlesQuery->whereIn('invoices.journal_id', $filteredJournalIds);
            }
        }
        $topArticles = $topArticlesQuery->get()->toArray();

        // ── Hourly Sales heatmap ─────────────────────────────────────────────
        $hourlySales = [];
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $hourField = $isSqlite ? "strftime('%H', date_time)" : "HOUR(date_time)";
        
        $hourlySalesQuery = Invoice::where('type', 'sale')
            ->selectRaw("{$hourField} as hour_num, COUNT(*) as count")
            ->groupByRaw($hourField);
        $hourlySalesQuery = $applyJournalFilter($hourlySalesQuery);
        
        $hourlyResults = $hourlySalesQuery->get();
        foreach ($hourlyResults as $r) {
            if ($r->hour_num !== null) {
                $hourlySales[(int)$r->hour_num] = (int)$r->count;
            }
        }

        // ── Dropdowns and selectors ──────────────────────────────────────────
        $pointsOfSale = PointOfSale::orderBy('name')->get();
        
        $devices = Device::when($this->posId, fn($q) => $q->where('point_of_sale_id', $this->posId))
            ->orderBy('nid')
            ->get();

        $journalsQuery = Journal::with('device.pointOfSale');
        if ($this->deviceId) {
            $journalsQuery->where('device_id', $this->deviceId);
        } elseif ($this->posId) {
            $journalsQuery->whereHas('device', fn($q) => $q->where('point_of_sale_id', $this->posId));
        }
        $journalsList = $journalsQuery->latest()->limit(10)->get();

        // ── Recent Anomalies ─────────────────────────────────────────────────
        $recentAnomaliesQuery = Anomaly::with('invoice')
            ->where('is_resolved', false);

        if ($isSqlite) {
            $recentAnomaliesQuery->orderByRaw("CASE severity WHEN 'critical' THEN 1 WHEN 'warning' THEN 2 WHEN 'info' THEN 3 ELSE 4 END");
        } else {
            $recentAnomaliesQuery->orderByRaw("FIELD(severity,'critical','warning','info')");
        }
        
        $recentAnomaliesQuery = $applyJournalFilter($recentAnomaliesQuery);
        $recentAnomalies = $recentAnomaliesQuery->limit(5)->get();

        // Single active journal (or first matched) context for exports block
        $journal = null;
        if ($this->selectedJournalId) {
            $journal = Journal::find($this->selectedJournalId);
        } elseif (count($filteredJournalIds) === 1) {
            $journal = Journal::find($filteredJournalIds[0]);
        }

        return view('livewire.dashboard', [
            'totalTtc' => $totalTtc,
            'totalTva' => $totalTva,
            'totalInvoices' => $totalInvoices,
            'totalCancelled' => $totalCancelled,
            'anomalyCount' => $anomalyCount,
            'dailySales' => $dailySales,
            'topArticles' => $topArticles,
            'hourlySales' => $hourlySales,
            'pointsOfSale' => $pointsOfSale,
            'devices' => $devices,
            'journals' => $journalsList,
            'recentAnomalies' => $recentAnomalies,
            'journal' => $journal,
            'hasFilters' => $hasFilters
        ])->layout('layouts.app');
    }
}
