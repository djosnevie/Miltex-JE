<?php

namespace App\Exports;

use App\Models\InvoiceItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ArticlesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly ?int $journalId = null,
        private readonly ?int $pointOfSaleId = null,
    ) {}

    public function title(): string
    {
        return 'Ventes par Article';
    }

    public function query()
    {
        $q = InvoiceItem::selectRaw('
                invoice_items.name,
                invoice_items.tax_group,
                SUM(invoice_items.qty)   AS total_qty,
                AVG(invoice_items.pu)    AS avg_pu,
                SUM(invoice_items.total) AS total_revenue
            ')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoices.type', 'sale');

        if ($this->journalId) {
            $q->where('invoices.journal_id', $this->journalId);
        } elseif ($this->pointOfSaleId) {
            $q->whereIn('invoices.journal_id', function ($query) {
                $query->select('id')
                    ->from('journals')
                    ->whereIn('device_id', function ($deviceQuery) {
                        $deviceQuery->select('id')
                            ->from('devices')
                            ->where('point_of_sale_id', $this->pointOfSaleId);
                    });
            });
        }

        return $q->groupBy('invoice_items.name', 'invoice_items.tax_group')
            ->orderByDesc('total_revenue');
    }

    public function headings(): array
    {
        return [
            'Article',
            'Groupe TVA',
            'Quantité Totale',
            'Prix Unitaire Moyen (CDF)',
            'Chiffre d\'Affaires TTC (CDF)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->tax_group ?? '—',
            $row->total_qty,
            number_format($row->avg_pu, 2, ',', ' '),
            number_format($row->total_revenue, 2, ',', ' '),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0D6E3C'],
                ],
            ],
        ];
    }
}
