<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\Journal;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionsList extends Component
{
    use WithPagination;

    public string $search    = '';
    public string $type      = '';
    public ?int   $journalId = null;
    public string $sortBy    = 'date_time';
    public string $sortDir   = 'desc';

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingFilter(): void  { $this->resetPage(); }

    public function sort(string $column): void
    {
        $this->sortDir = ($this->sortBy === $column && $this->sortDir === 'desc') ? 'asc' : 'desc';
        $this->sortBy  = $column;
        $this->resetPage();
    }

    public function render()
    {
        $invoices = Invoice::with('journal.device.pointOfSale')
            ->when($this->journalId, fn($q) => $q->where('journal_id', $this->journalId))
            ->when($this->type,      fn($q) => $q->where('type', $this->type))
            ->when($this->search,    fn($q) => $q->where(function ($q) {
                $q->where('invoice_no', 'like', "%{$this->search}%")
                  ->orWhere('buyer_name', 'like', "%{$this->search}%");
            }))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(25);

        $journals = Journal::latest()->limit(30)->get();

        return view('livewire.transactions-list', compact('invoices', 'journals'))
            ->layout('layouts.app', ['title' => 'Transactions']);
    }
}
