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
        private readonly ?int    $journalId = null,
        private readonly ?string $type = null,   // 'sale' | 'credit_note' | 'cancelled' | null (all)
        private readonly ?string $from = null,
        private readonly ?string $to = null,
        private readonly ?int    $pointOfSaleId = null,
        private readonly ?int    $deviceId = null,
        private readonly ?string $paymentMode = null,
        private readonly ?string $mcfFilter = null, // 'with_error' | 'without_error' | null
    ) {}

    public function title(): string
    {
        return match ($this->type) {
            'sale'        => 'Ventes',
            'credit_note' => 'Avoirs',
            'cancelled'   => 'Annulations',
            default       => 'Transactions',
        };
    }

    public function query()
    {
        $q = Invoice::with(['journal.device.pointOfSale'])
            ->orderBy('date_time');

        if ($this->journalId) {
            $q->where('journal_id', $this->journalId);
        } elseif ($this->pointOfSaleId) {
            $q->whereHas('journal.device', function ($dq) {
                $dq->where('point_of_sale_id', $this->pointOfSaleId);
            });
        }

        if ($this->deviceId) {
            $q->whereHas('journal', function ($jq) {
                $jq->where('device_id', $this->deviceId);
            });
        }

        if ($this->type && $this->type !== 'all') {
            $q->where('type', $this->type);
        }

        if ($this->from) {
            $fromStr = strlen($this->from) === 10 ? $this->from . ' 00:00:00' : $this->from;
            $q->where('date_time', '>=', $fromStr);
        }
        if ($this->to) {
            $toStr = strlen($this->to) === 10 ? $this->to . ' 23:59:59' : $this->to;
            $q->where('date_time', '<=', $toStr);
        }

        if ($this->paymentMode && $this->paymentMode !== 'all') {
            $q->where('payment_mode', 'LIKE', '%' . $this->paymentMode . '%');
        }

        if ($this->mcfFilter === 'with_error') {
            $q->where('has_mcf_error', true);
        } elseif ($this->mcfFilter === 'without_error') {
            $q->where('has_mcf_error', false);
        }

        return $q;
    }

    public function headings(): array
    {
        return [
            'Point de Vente',
            'Dispositif DEF (NID)',
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
            $invoice->journal?->device?->pointOfSale?->name ?? '—',
            $invoice->journal?->device?->nid ?? '—',
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
            $invoice->buyer_name ?? 'Client Anonyme',
            $invoice->buyer_id ?? '—',
            number_format($invoice->total_ht, 2, ',', ' '),
            number_format($invoice->total_tva, 2, ',', ' '),
            number_format($invoice->total_ttc, 2, ',', ' '),
            $invoice->payment_mode ?? '—',
            $invoice->code_def ?? '—',
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
