<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TvaDetailedExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly int $journalId
    ) {}

    public function title(): string
    {
        return 'Détail TVA';
    }

    public function query()
    {
        // For TVA declaration, we only include sales and credit notes
        // as cancelled invoices do not carry tax liability.
        return Invoice::with('journal')
            ->where('journal_id', $this->journalId)
            ->whereIn('type', ['sale', 'credit_note'])
            ->orderBy('date_time');
    }

    public function headings(): array
    {
        return [
            'N° Facture',
            'Type Transaction',
            'Date & Heure',
            'Client',
            'NIF Client',
            'Base Imposable (HT)',
            'Taux TVA',
            'TVA Collectée / Déduite',
            'Montant TTC',
            'Signature DGI (Code DEF)',
        ];
    }

    public function map($invoice): array
    {
        $coefficient = $invoice->type === 'credit_note' ? -1 : 1;

        return [
            $invoice->invoice_no,
            $invoice->type === 'sale' ? 'Vente (Collectée)' : 'Avoir (Déduite)',
            $invoice->date_time?->format('d/m/Y H:i:s'),
            $invoice->buyer_name ?? 'Client Anonyme',
            $invoice->buyer_id ?? '—',
            number_format($invoice->total_ht * $coefficient, 2, ',', ' '),
            '16%',
            number_format($invoice->total_tva * $coefficient, 2, ',', ' '),
            number_format($invoice->total_ttc * $coefficient, 2, ',', ' '),
            $invoice->code_def ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'], // Navy Blue
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
