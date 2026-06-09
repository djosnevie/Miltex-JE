<?php

namespace App\Exports;

use App\Models\Anomaly;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AnomaliesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly ?int $journalId = null,
        private readonly ?int $pointOfSaleId = null,
    ) {}

    public function title(): string { return 'Anomalies'; }

    public function query()
    {
        $q = Anomaly::with('invoice')
            ->orderBy('severity')
            ->orderBy('created_at');

        if ($this->journalId) {
            $q->where('journal_id', $this->journalId);
        } elseif ($this->pointOfSaleId) {
            $q->whereIn('journal_id', function ($query) {
                $query->select('id')
                    ->from('journals')
                    ->whereIn('device_id', function ($deviceQuery) {
                        $deviceQuery->select('id')
                            ->from('devices')
                            ->where('point_of_sale_id', $this->pointOfSaleId);
                    });
            });
        }

        return $q;
    }

    public function headings(): array
    {
        return ['Sévérité', 'Type', 'Facture', 'Description', 'Résolu'];
    }

    public function map($row): array
    {
        return [
            match ($row->severity) {
                'critical' => '🔴 Critique',
                'warning'  => '🟡 Avertissement',
                default    => '🔵 Info',
            },
            $row->type,
            $row->invoice?->invoice_no ?? '—',
            $row->description,
            $row->is_resolved ? 'Oui' : 'Non',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF8B0000']],
            ],
        ];
    }
}
