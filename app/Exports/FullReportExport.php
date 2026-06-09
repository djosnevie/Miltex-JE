<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\Journal;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FullReportExport implements WithMultipleSheets
{
    public function __construct(
        private readonly int $journalId,
    ) {}

    public function sheets(): array
    {
        return [
            new InvoicesExport($this->journalId, 'sale'),
            new InvoicesExport($this->journalId, 'credit_note'),
            new InvoicesExport($this->journalId, 'cancelled'),
            new ArticlesExport($this->journalId),
            new AnomaliesExport($this->journalId),
        ];
    }
}
