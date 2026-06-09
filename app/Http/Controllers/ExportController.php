<?php

namespace App\Http\Controllers;

use App\Exports\ArticlesExport;
use App\Exports\FullReportExport;
use App\Exports\InvoicesExport;
use App\Exports\TvaDetailedExport;
use App\Models\Journal;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /** Export Excel de toutes les transactions d'un journal */
    public function invoicesExcel(int $journalId, string $type = 'all')
    {
        $journal = Journal::findOrFail($journalId);
        $typeFilter = $type === 'all' ? null : $type;
        $filename   = "transactions_{$journal->filename}_{$type}.xlsx";

        return Excel::download(new InvoicesExport($journalId, $typeFilter), $filename);
    }

    /** Export Excel du palmarès des articles */
    public function articlesExcel(int $journalId)
    {
        $journal  = Journal::findOrFail($journalId);
        $filename = "articles_{$journal->filename}.xlsx";

        return Excel::download(new ArticlesExport($journalId), $filename);
    }

    /** Export Excel complet multi-feuilles */
    public function fullReportExcel(int $journalId)
    {
        $journal  = Journal::findOrFail($journalId);
        $filename = "rapport_complet_{$journal->filename}.xlsx";

        return Excel::download(new FullReportExport($journalId), $filename);
    }

    /** PDF : Rapport de conformité DGI */
    public function compliancePdf(int $journalId)
    {
        $journal = Journal::with([
            'device.pointOfSale.company',
            'anomalies',
        ])->findOrFail($journalId);

        $pdf = Pdf::loadView('reports.compliance', compact('journal'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("conformite_dgi_{$journal->filename}.pdf");
    }

    /** PDF : Synthèse mensuelle TVA */
    public function tvaSummaryPdf(int $journalId)
    {
        $journal = Journal::with('device.pointOfSale.company')
            ->findOrFail($journalId);

        $pdf = Pdf::loadView('reports.tva_summary', compact('journal'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("synthese_tva_{$journal->filename}.pdf");
    }

    /** PDF : Rapport TVA jour par jour */
    public function tvaDailyPdf(int $journalId)
    {
        $journal = Journal::with('device.pointOfSale.company')
            ->findOrFail($journalId);

        $dailyRaw = \App\Models\Invoice::where('journal_id', $journalId)
            ->whereIn('type', ['sale', 'credit_note'])
            ->selectRaw("DATE(date_time) as day, 
                         SUM(CASE WHEN type = 'credit_note' THEN -total_ht ELSE total_ht END) as ht,
                         SUM(CASE WHEN type = 'credit_note' THEN -total_tva ELSE total_tva END) as tva,
                         SUM(CASE WHEN type = 'credit_note' THEN -total_ttc ELSE total_ttc END) as ttc,
                         COUNT(*) as count")
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $dailyData = $dailyRaw->map(fn($r) => [
            'date'  => $r->day,
            'ht'    => (float) $r->ht,
            'tva'   => (float) $r->tva,
            'ttc'   => (float) $r->ttc,
            'count' => (int)   $r->count,
        ])->toArray();

        $pdf = Pdf::loadView('reports.tva_daily', compact('journal', 'dailyData'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("tva_journaliere_{$journal->filename}.pdf");
    }

    /** Export Excel des détails de TVA */
    public function tvaDetailedExcel(int $journalId)
    {
        $journal  = Journal::findOrFail($journalId);
        $filename = "tva_detaillee_{$journal->filename}.xlsx";

        return Excel::download(new TvaDetailedExport($journalId), $filename);
    }
}
