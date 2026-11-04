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
        private readonly ?int    $journalId = null,
        private readonly ?int    $pointOfSaleId = null,
        private readonly ?string $from = null,
        private readonly ?string $to = null,
        private readonly ?int    $deviceId = null,
    ) {}

    public function title(): string
    {
        return 'Anomalies';
    }

    public function query()
    {
        $q = Anomaly::with(['invoice', 'journal.device.pointOfSale'])
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

        if ($this->deviceId) {
            $q->whereIn('journal_id', function ($query) {
                $query->select('id')
                    ->from('journals')
                    ->where('device_id', $this->deviceId);
            });
        }

        if ($this->from) {
            $fromStr = strlen($this->from) === 10 ? $this->from . ' 00:00:00' : $this->from;
            $q->where('created_at', '>=', $fromStr);
        }
        if ($this->to) {
            $toStr = strlen($this->to) === 10 ? $this->to . ' 23:59:59' : $this->to;
            $q->where('created_at', '<=', $toStr);
        }

        return $q;
    }

    public function headings(): array
    {
        return [
            'Point de Vente',
            'Dispositif DEF',
            'Sévérité',
            'Type d\'Anomalie',
            'N° Facture',
            'Description',
            'Statut Résolu',
            'Date Détection',
        ];
    }

    public function map($row): array
    {
        return [
            $row->journal?->device?->pointOfSale?->name ?? '—',
            $row->journal?->device?->nid ?? '—',
            match ($row->severity) {
                'critical' => 'Critique',
                'warning'  => 'Avertissement',
                default    => 'Info',
            },
            $row->type,
            $row->invoice?->invoice_no ?? '—',
            $row->description,
            $row->is_resolved ? 'Oui' : 'Non',
            $row->created_at?->format('d/m/Y H:i') ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDC2626']],
            ],
        ];
    }
}
