<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\Journal;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FullReportExport implements WithMultipleSheets
{
    public function __construct(
        private readonly ?int $journalId = null,
        private readonly ?int $pointOfSaleId = null,
    ) {}

    public function sheets(): array
    {
        return [
            new InvoicesExport($this->journalId, 'sale', null, null, $this->pointOfSaleId),
            new InvoicesExport($this->journalId, 'credit_note', null, null, $this->pointOfSaleId),
            new InvoicesExport($this->journalId, 'cancelled', null, null, $this->pointOfSaleId),
            new ArticlesExport($this->journalId, $this->pointOfSaleId),
            new AnomaliesExport($this->journalId, $this->pointOfSaleId),
        ];
    }
}
