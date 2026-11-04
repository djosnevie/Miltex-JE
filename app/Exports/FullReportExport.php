<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FullReportExport implements WithMultipleSheets
{
    public function __construct(
        private readonly ?int    $journalId = null,
        private readonly ?int    $pointOfSaleId = null,
        private readonly ?string $from = null,
        private readonly ?string $to = null,
        private readonly ?int    $deviceId = null,
    ) {}

    public function sheets(): array
    {
        return [
            new InvoicesExport($this->journalId, 'sale', $this->from, $this->to, $this->pointOfSaleId, $this->deviceId),
            new InvoicesExport($this->journalId, 'credit_note', $this->from, $this->to, $this->pointOfSaleId, $this->deviceId),
            new InvoicesExport($this->journalId, 'cancelled', $this->from, $this->to, $this->pointOfSaleId, $this->deviceId),
            new ArticlesExport($this->journalId, $this->pointOfSaleId, $this->from, $this->to, $this->deviceId),
            new AnomaliesExport($this->journalId, $this->pointOfSaleId, $this->from, $this->to, $this->deviceId),
        ];
    }
}
