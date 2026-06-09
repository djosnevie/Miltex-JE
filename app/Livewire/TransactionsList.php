<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\Journal;
use App\Models\PointOfSale;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionsList extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $type      = '';
    public ?int   $journalId = null;
    public ?int   $posId     = null;    // ← NEW: filter by Point of Sale
    public string $sortBy    = 'date_time';
    public string $sortDir   = 'desc';

    public function updatingSearch():    void { $this->resetPage(); }
    public function updatingType():      void { $this->resetPage(); }
    public function updatingJournalId(): void { $this->resetPage(); }
    public function updatingPosId():     void {
        $this->journalId = null; // Reset journal filter when POS changes
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        $this->sortDir = ($this->sortBy === $column && $this->sortDir === 'desc') ? 'asc' : 'desc';
        $this->sortBy  = $column;
        $this->resetPage();
    }

    public function render()
    {
        $invoices = Invoice::with('journal.device.pointOfSale')
            // Filter by POS: join through journals → devices → points_of_sale
            ->when($this->posId, function ($q) {
                $q->whereHas('journal.device', fn($q2) =>
                    $q2->where('point_of_sale_id', $this->posId)
                );
            })
            ->when($this->journalId, fn($q) => $q->where('journal_id', $this->journalId))
            ->when($this->type,      fn($q) => $q->where('type', $this->type))
            ->when($this->search,    fn($q) => $q->where(function ($q) {
                $q->where('invoice_no', 'like', "%{$this->search}%")
                  ->orWhere('buyer_name', 'like', "%{$this->search}%");
            }))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(25);

        // Journals filtered by selected POS
        $journals = Journal::with('device.pointOfSale')
            ->when($this->posId, fn($q) =>
                $q->whereHas('device', fn($q2) => $q2->where('point_of_sale_id', $this->posId))
            )
            ->latest()
            ->limit(50)
            ->get();

        $pointsOfSale = PointOfSale::orderBy('name')->get();

        return view('livewire.transactions-list', compact('invoices', 'journals', 'pointsOfSale'))
            ->layout('layouts.app', ['title' => 'Transactions']);
    }
}
