<?php

namespace App\Http\Controllers;

use App\Exports\ArticlesExport;
use App\Exports\FullReportExport;
use App\Exports\InvoicesExport;
use App\Exports\TvaDetailedExport;
use App\Models\Anomaly;
use App\Models\Company;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\PointOfSale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    // EXPORTS PAR JOURNAL SPÉCIFIQUE
    // ══════════════════════════════════════════════════════════════════════════

    /** Export Excel de toutes les transactions d'un journal */
    public function invoicesExcel(Request $request, int $journalId, string $type = 'all')
    {
        $journal = Journal::findOrFail($journalId);
        $typeFilter = $request->query('type', $type);
        if ($typeFilter === 'all') $typeFilter = null;
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;
        $paymentMode = $request->query('payment_mode');
        $mcfFilter = $request->query('mcf_filter');

        $suffix = $this->makeSuffix($from, $to, $typeFilter);
        $filename = "transactions_{$journal->filename}{$suffix}.xlsx";

        return Excel::download(
            new InvoicesExport($journalId, $typeFilter, $from, $to, null, $deviceId, $paymentMode, $mcfFilter),
            $filename
        );
    }

    /** Export Excel du palmarès des articles */
    public function articlesExcel(Request $request, int $journalId)
    {
        $journal = Journal::findOrFail($journalId);
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $suffix = $this->makeSuffix($from, $to);
        $filename = "articles_{$journal->filename}{$suffix}.xlsx";

        return Excel::download(new ArticlesExport($journalId, null, $from, $to, $deviceId), $filename);
    }

    /** Export Excel complet multi-feuilles */
    public function fullReportExcel(Request $request, int $journalId)
    {
        $journal = Journal::findOrFail($journalId);
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $suffix = $this->makeSuffix($from, $to);
        $filename = "rapport_complet_{$journal->filename}{$suffix}.xlsx";

        return Excel::download(new FullReportExport($journalId, null, $from, $to, $deviceId), $filename);
    }

    /** Export Excel des détails de TVA */
    public function tvaDetailedExcel(Request $request, int $journalId)
    {
        $journal = Journal::findOrFail($journalId);
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $suffix = $this->makeSuffix($from, $to);
        $filename = "tva_detaillee_{$journal->filename}{$suffix}.xlsx";

        return Excel::download(new TvaDetailedExport($journalId, null, $from, $to, $deviceId), $filename);
    }

    /** PDF : Rapport de conformité DGI */
    public function compliancePdf(Request $request, int $journalId)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $journal = $this->buildPdfSummaryContext($journalId, null, $from, $to, $deviceId);

        $pdf = Pdf::loadView('reports.compliance', compact('journal'))
            ->setPaper('a4', 'portrait');

        $suffix = $this->makeSuffix($from, $to);
        return $pdf->download("conformite_dgi_{$journal->filename}{$suffix}.pdf");
    }

    /** PDF : Synthèse mensuelle TVA */
    public function tvaSummaryPdf(Request $request, int $journalId)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $journal = $this->buildPdfSummaryContext($journalId, null, $from, $to, $deviceId);

        $pdf = Pdf::loadView('reports.tva_summary', compact('journal'))
            ->setPaper('a4', 'portrait');

        $suffix = $this->makeSuffix($from, $to);
        return $pdf->download("synthese_tva_{$journal->filename}{$suffix}.pdf");
    }

    /** PDF : Rapport TVA jour par jour */
    public function tvaDailyPdf(Request $request, int $journalId)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $journal = $this->buildPdfSummaryContext($journalId, null, $from, $to, $deviceId);
        $dailyData = $this->buildDailyData($journalId, null, $from, $to, $deviceId);

        $pdf = Pdf::loadView('reports.tva_daily', compact('journal', 'dailyData'))
            ->setPaper('a4', 'portrait');

        $suffix = $this->makeSuffix($from, $to);
        return $pdf->download("tva_journaliere_{$journal->filename}{$suffix}.pdf");
    }

    // ══════════════════════════════════════════════════════════════════════════
    // EXPORTS PAR POINT DE VENTE (CONSOLIDÉ PDV)
    // ══════════════════════════════════════════════════════════════════════════

    /** Export Excel de toutes les transactions d'un Point de Vente */
    public function posInvoicesExcel(Request $request, int $posId, string $type = 'all')
    {
        $pos = PointOfSale::findOrFail($posId);
        $typeFilter = $request->query('type', $type);
        if ($typeFilter === 'all') $typeFilter = null;
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;
        $paymentMode = $request->query('payment_mode');
        $mcfFilter = $request->query('mcf_filter');

        $cleanName = Str::slug($pos->name);
        $suffix = $this->makeSuffix($from, $to, $typeFilter);
        $filename  = "transactions_pdv_{$cleanName}{$suffix}.xlsx";

        return Excel::download(
            new InvoicesExport(null, $typeFilter, $from, $to, $posId, $deviceId, $paymentMode, $mcfFilter),
            $filename
        );
    }

    /** Export Excel du palmarès des articles d'un Point de Vente */
    public function posArticlesExcel(Request $request, int $posId)
    {
        $pos = PointOfSale::findOrFail($posId);
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $cleanName = Str::slug($pos->name);
        $suffix = $this->makeSuffix($from, $to);
        $filename  = "articles_pdv_{$cleanName}{$suffix}.xlsx";

        return Excel::download(new ArticlesExport(null, $posId, $from, $to, $deviceId), $filename);
    }

    /** Export Excel complet multi-feuilles d'un Point de Vente */
    public function posFullReportExcel(Request $request, int $posId)
    {
        $pos = PointOfSale::findOrFail($posId);
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $cleanName = Str::slug($pos->name);
        $suffix = $this->makeSuffix($from, $to);
        $filename  = "rapport_complet_pdv_{$cleanName}{$suffix}.xlsx";

        return Excel::download(new FullReportExport(null, $posId, $from, $to, $deviceId), $filename);
    }

    /** Export Excel des détails de TVA d'un Point de Vente */
    public function posTvaDetailedExcel(Request $request, int $posId)
    {
        $pos = PointOfSale::findOrFail($posId);
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $cleanName = Str::slug($pos->name);
        $suffix = $this->makeSuffix($from, $to);
        $filename  = "tva_detaillee_pdv_{$cleanName}{$suffix}.xlsx";

        return Excel::download(new TvaDetailedExport(null, $posId, $from, $to, $deviceId), $filename);
    }

    /** PDF : Rapport de conformité DGI d'un Point de Vente */
    public function posCompliancePdf(Request $request, int $posId)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $journal = $this->buildPdfSummaryContext(null, $posId, $from, $to, $deviceId);

        $pdf = Pdf::loadView('reports.compliance', compact('journal'))
            ->setPaper('a4', 'portrait');

        $suffix = $this->makeSuffix($from, $to);
        return $pdf->download("conformite_dgi_pdv_{$journal->filename}{$suffix}.pdf");
    }

    /** PDF : Synthèse mensuelle TVA d'un Point de Vente */
    public function posTvaSummaryPdf(Request $request, int $posId)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $journal = $this->buildPdfSummaryContext(null, $posId, $from, $to, $deviceId);

        $pdf = Pdf::loadView('reports.tva_summary', compact('journal'))
            ->setPaper('a4', 'portrait');

        $suffix = $this->makeSuffix($from, $to);
        return $pdf->download("synthese_tva_pdv_{$journal->filename}{$suffix}.pdf");
    }

    /** PDF : Rapport TVA jour par jour d'un Point de Vente */
    public function posTvaDailyPdf(Request $request, int $posId)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $journal = $this->buildPdfSummaryContext(null, $posId, $from, $to, $deviceId);
        $dailyData = $this->buildDailyData(null, $posId, $from, $to, $deviceId);

        $pdf = Pdf::loadView('reports.tva_daily', compact('journal', 'dailyData'))
            ->setPaper('a4', 'portrait');

        $suffix = $this->makeSuffix($from, $to);
        return $pdf->download("tva_journaliere_pdv_{$journal->filename}{$suffix}.pdf");
    }

    // ══════════════════════════════════════════════════════════════════════════
    // EXPORTS CONSOLIDÉS GLOBAUX (TOUS POINTS DE VENTE)
    // ══════════════════════════════════════════════════════════════════════════

    /** Export Excel de toutes les transactions (Consolidé Global) */
    public function globalInvoicesExcel(Request $request, string $type = 'all')
    {
        $typeFilter = $request->query('type', $type);
        if ($typeFilter === 'all') $typeFilter = null;
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;
        $paymentMode = $request->query('payment_mode');
        $mcfFilter = $request->query('mcf_filter');

        $suffix = $this->makeSuffix($from, $to, $typeFilter);
        $filename  = "transactions_globales{$suffix}.xlsx";

        return Excel::download(
            new InvoicesExport(null, $typeFilter, $from, $to, null, $deviceId, $paymentMode, $mcfFilter),
            $filename
        );
    }

    /** Export Excel du palmarès des articles (Consolidé Global) */
    public function globalArticlesExcel(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $suffix = $this->makeSuffix($from, $to);
        $filename  = "articles_global{$suffix}.xlsx";

        return Excel::download(new ArticlesExport(null, null, $from, $to, $deviceId), $filename);
    }

    /** Export Excel complet multi-feuilles (Consolidé Global) */
    public function globalFullReportExcel(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $suffix = $this->makeSuffix($from, $to);
        $filename  = "rapport_complet_global{$suffix}.xlsx";

        return Excel::download(new FullReportExport(null, null, $from, $to, $deviceId), $filename);
    }

    /** Export Excel des détails de TVA (Consolidé Global) */
    public function globalTvaDetailedExcel(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $suffix = $this->makeSuffix($from, $to);
        $filename  = "tva_detaillee_global{$suffix}.xlsx";

        return Excel::download(new TvaDetailedExport(null, null, $from, $to, $deviceId), $filename);
    }

    /** PDF : Rapport de conformité DGI (Consolidé Global) */
    public function globalCompliancePdf(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $journal = $this->buildPdfSummaryContext(null, null, $from, $to, $deviceId);

        $pdf = Pdf::loadView('reports.compliance', compact('journal'))
            ->setPaper('a4', 'portrait');

        $suffix = $this->makeSuffix($from, $to);
        return $pdf->download("conformite_dgi_global{$suffix}.pdf");
    }

    /** PDF : Synthèse mensuelle TVA (Consolidé Global) */
    public function globalTvaSummaryPdf(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $journal = $this->buildPdfSummaryContext(null, null, $from, $to, $deviceId);

        $pdf = Pdf::loadView('reports.tva_summary', compact('journal'))
            ->setPaper('a4', 'portrait');

        $suffix = $this->makeSuffix($from, $to);
        return $pdf->download("synthese_tva_global{$suffix}.pdf");
    }

    /** PDF : Rapport TVA jour par jour (Consolidé Global) */
    public function globalTvaDailyPdf(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $deviceId = $request->query('device_id') ? (int) $request->query('device_id') : null;

        $journal = $this->buildPdfSummaryContext(null, null, $from, $to, $deviceId);
        $dailyData = $this->buildDailyData(null, null, $from, $to, $deviceId);

        $pdf = Pdf::loadView('reports.tva_daily', compact('journal', 'dailyData'))
            ->setPaper('a4', 'portrait');

        $suffix = $this->makeSuffix($from, $to);
        return $pdf->download("tva_journaliere_global{$suffix}.pdf");
    }

    // ══════════════════════════════════════════════════════════════════════════
    // HELPERS PRIVÉS
    // ══════════════════════════════════════════════════════════════════════════

    private function makeSuffix(?string $from, ?string $to, ?string $type = null): string
    {
        $parts = [];
        if ($type && $type !== 'all') {
            $parts[] = $type;
        }
        if ($from && $to) {
            $parts[] = "{$from}_au_{$to}";
        } elseif ($from) {
            $parts[] = "depuis_{$from}";
        } elseif ($to) {
            $parts[] = "jusquau_{$to}";
        }
        return !empty($parts) ? '_' . implode('_', $parts) : '';
    }

    private function buildPdfSummaryContext(?int $journalId, ?int $posId, ?string $from, ?string $to, ?int $deviceId): object
    {
        if ($journalId) {
            $journal = Journal::with(['device.pointOfSale.company', 'anomalies'])->findOrFail($journalId);
            if ($from || $to) {
                $invQ = Invoice::where('journal_id', $journalId);
                if ($from) $invQ->where('date_time', '>=', strlen($from) === 10 ? $from . ' 00:00:00' : $from);
                if ($to) $invQ->where('date_time', '<=', strlen($to) === 10 ? $to . ' 23:59:59' : $to);

                $journal->total_invoices = (clone $invQ)->where('type', 'sale')->count();
                $journal->total_credits  = (clone $invQ)->where('type', 'credit_note')->count();
                $journal->total_cancelled= (clone $invQ)->where('type', 'cancelled')->count();
                $journal->total_ttc      = (clone $invQ)->where('type', 'sale')->sum('total_ttc');
                $journal->total_tva      = (clone $invQ)->where('type', 'sale')->sum('total_tva');
                $journal->total_ht       = $journal->total_ttc - $journal->total_tva;
                if ($from) $journal->start_date = Carbon::parse($from);
                if ($to) $journal->end_date = Carbon::parse($to);
            } else {
                $journal->total_ht = ($journal->total_ttc ?? 0) - ($journal->total_tva ?? 0);
            }
            return $journal;
        }

        $company = Company::first() ?? (object)['name' => 'MILTEX SARL', 'nif' => 'A2609384F'];
        $pos = $posId ? PointOfSale::with('company')->find($posId) : null;
        $device = $deviceId ? Device::find($deviceId) : null;

        $invQ = Invoice::query();
        if ($posId) {
            $invQ->whereHas('journal.device', fn($q) => $q->where('point_of_sale_id', $posId));
        }
        if ($deviceId) {
            $invQ->whereHas('journal', fn($q) => $q->where('device_id', $deviceId));
        }
        if ($from) {
            $invQ->where('date_time', '>=', strlen($from) === 10 ? $from . ' 00:00:00' : $from);
        }
        if ($to) {
            $invQ->where('date_time', '<=', strlen($to) === 10 ? $to . ' 23:59:59' : $to);
        }

        $totalInvoices = (clone $invQ)->where('type', 'sale')->count();
        $totalCredits  = (clone $invQ)->where('type', 'credit_note')->count();
        $totalCancelled= (clone $invQ)->where('type', 'cancelled')->count();
        $totalTtc      = (clone $invQ)->where('type', 'sale')->sum('total_ttc');
        $totalTva      = (clone $invQ)->where('type', 'sale')->sum('total_tva');
        $totalHt       = $totalTtc - $totalTva;

        $anomQ = Anomaly::query();
        if ($posId) {
            $anomQ->whereHas('journal.device', fn($q) => $q->where('point_of_sale_id', $posId));
        }
        if ($deviceId) {
            $anomQ->whereHas('journal', fn($q) => $q->where('device_id', $deviceId));
        }
        if ($from) {
            $anomQ->where('created_at', '>=', strlen($from) === 10 ? $from . ' 00:00:00' : $from);
        }
        if ($to) {
            $anomQ->where('created_at', '<=', strlen($to) === 10 ? $to . ' 23:59:59' : $to);
        }
        $anomalies = $anomQ->get();

        $startDate = $from ? Carbon::parse($from) : ($invQ->min('date_time') ? Carbon::parse($invQ->min('date_time')) : now()->startOfMonth());
        $endDate   = $to   ? Carbon::parse($to)   : ($invQ->max('date_time') ? Carbon::parse($invQ->max('date_time')) : now());

        return (object)[
            'filename' => $pos ? Str::slug($pos->name) : 'consolide_global',
            'original_name' => $pos ? "Point de Vente : {$pos->name}" : "Rapport Consolidé Global Entreprise",
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_invoices' => $totalInvoices,
            'total_credits' => $totalCredits,
            'total_cancelled' => $totalCancelled,
            'total_ttc' => $totalTtc,
            'total_ht' => $totalHt,
            'total_tva' => $totalTva,
            'currency' => 'CDF',
            'anomalies' => $anomalies,
            'device' => (object)[
                'nid' => $device ? $device->nid : ($pos ? 'Tous les DEF du PDV' : 'Tous les matériels DEF'),
                'isf' => $device?->isf ?? '—',
                'pointOfSale' => (object)[
                    'name' => $pos?->name ?? 'Tous les points de vente',
                    'company' => $pos?->company ?? $company,
                ],
            ],
        ];
    }

    private function buildDailyData(?int $journalId, ?int $posId, ?string $from, ?string $to, ?int $deviceId): array
    {
        $q = Invoice::whereIn('type', ['sale', 'credit_note']);
        if ($journalId) {
            $q->where('journal_id', $journalId);
        } elseif ($posId) {
            $q->whereHas('journal.device', fn($dq) => $dq->where('point_of_sale_id', $posId));
        }
        if ($deviceId) {
            $q->whereHas('journal', fn($jq) => $jq->where('device_id', $deviceId));
        }
        if ($from) {
            $q->where('date_time', '>=', strlen($from) === 10 ? $from . ' 00:00:00' : $from);
        }
        if ($to) {
            $q->where('date_time', '<=', strlen($to) === 10 ? $to . ' 23:59:59' : $to);
        }

        $dailyRaw = $q->selectRaw("DATE(date_time) as day, 
                     SUM(CASE WHEN type = 'credit_note' THEN -total_ht ELSE total_ht END) as ht,
                     SUM(CASE WHEN type = 'credit_note' THEN -total_tva ELSE total_tva END) as tva,
                     SUM(CASE WHEN type = 'credit_note' THEN -total_ttc ELSE total_ttc END) as ttc,
                     COUNT(*) as count")
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return $dailyRaw->map(fn($r) => [
            'date'  => $r->day,
            'ht'    => (float) $r->ht,
            'tva'   => (float) $r->tva,
            'ttc'   => (float) $r->ttc,
            'count' => (int)   $r->count,
        ])->toArray();
    }
}
