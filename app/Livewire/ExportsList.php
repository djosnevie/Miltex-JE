<?php

namespace App\Livewire;

use App\Models\Journal;
use Livewire\Component;

class ExportsList extends Component
{
    public ?int $selectedJournalId = null;

    public function mount(): void
    {
        $this->selectedJournalId = Journal::latest()->first()?->id;
    }

    public function selectJournal(int $id): void
    {
        $this->selectedJournalId = $id;
    }

    public function render()
    {
        $journals = Journal::with('device.pointOfSale.company')
            ->latest()
            ->get();

        $selectedJournal = $this->selectedJournalId
            ? Journal::with('device.pointOfSale.company')->find($this->selectedJournalId)
            : null;

        return view('livewire.exports-list', compact('journals', 'selectedJournal'))
            ->layout('layouts.app', ['title' => 'Rapports & Exports']);
    }
}
