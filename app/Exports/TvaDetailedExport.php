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
        private readonly ?int    $journalId = null,
        private readonly ?int    $pointOfSaleId = null,
        private readonly ?string $from = null,
        private readonly ?string $to = null,
        private readonly ?int    $deviceId = null,
    ) {}

    public function title(): string
    {
        return 'Détail TVA';
    }

    public function query()
    {
        $q = Invoice::with(['journal.device.pointOfSale'])
            ->whereIn('type', ['sale', 'credit_note'])
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

        if ($this->from) {
            $fromStr = strlen($this->from) === 10 ? $this->from . ' 00:00:00' : $this->from;
            $q->where('date_time', '>=', $fromStr);
        }
        if ($this->to) {
            $toStr = strlen($this->to) === 10 ? $this->to . ' 23:59:59' : $this->to;
            $q->where('date_time', '<=', $toStr);
        }

        return $q;
    }

    public function headings(): array
    {
        return [
            'Point de Vente',
            'Dispositif DEF (NID)',
            'N° Facture',
            'Type Transaction',
            'Date & Heure',
            'Client',
            'NIF Client',
            'Base Imposable HT (CDF)',
            'Taux TVA',
            'TVA Collectée / Déduite (CDF)',
            'Montant TTC (CDF)',
            'Signature DGI (Code DEF)',
        ];
    }

    public function map($invoice): array
    {
        $coefficient = $invoice->type === 'credit_note' ? -1 : 1;

        return [
            $invoice->journal?->device?->pointOfSale?->name ?? '—',
            $invoice->journal?->device?->nid ?? '—',
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
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
