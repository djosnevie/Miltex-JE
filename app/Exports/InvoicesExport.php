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

class InvoicesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly int    $journalId,
        private readonly ?string $type   = null,   // 'sale' | 'credit_note' | 'cancelled' | null (all)
        private readonly ?string $from   = null,
        private readonly ?string $to     = null,
    ) {}

    public function title(): string
    {
        return 'Transactions';
    }

    public function query()
    {
        $q = Invoice::with('journal')
            ->where('journal_id', $this->journalId)
            ->orderBy('date_time');

        if ($this->type) {
            $q->where('type', $this->type);
        }
        if ($this->from && $this->to) {
            $q->whereBetween('date_time', [$this->from, $this->to]);
        }

        return $q;
    }

    public function headings(): array
    {
        return [
            'N° Facture',
            'Type',
            'Date & Heure',
            'Rapport Z',
            'N° Séquence',
            'Vendeur',
            'Acheteur',
            'ID Acheteur',
            'Montant HT (CDF)',
            'TVA 16% (CDF)',
            'Montant TTC (CDF)',
            'Mode Paiement',
            'Code DEF/DGI',
            'Compteur',
            'Erreur MCF',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_no,
            match ($invoice->type) {
                'sale'        => 'Vente',
                'credit_note' => 'Avoir',
                'cancelled'   => 'Annulée',
                default       => $invoice->type,
            },
            $invoice->date_time?->format('d/m/Y H:i:s'),
            $invoice->z_number,
            $invoice->serial_number,
            $invoice->vendeur,
            $invoice->buyer_name,
            $invoice->buyer_id,
            number_format($invoice->total_ht, 2, ',', ' '),
            number_format($invoice->total_tva, 2, ',', ' '),
            number_format($invoice->total_ttc, 2, ',', ' '),
            $invoice->payment_mode ?? '—',
            $invoice->code_def,
            $invoice->compteur_brut,
            $invoice->has_mcf_error ? 'Oui' : 'Non',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
