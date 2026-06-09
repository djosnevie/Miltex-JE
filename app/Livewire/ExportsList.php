<?php

namespace App\Livewire;

use App\Models\Journal;
use App\Models\PointOfSale;
use Livewire\Component;

class ExportsList extends Component
{
    public ?int $posId = null;
    public ?int $selectedJournalId = null;

    public function mount(): void
    {
        $this->selectedJournalId = Journal::latest()->first()?->id;
    }

    public function updatedPosId(): void
    {
        // Reset selected journal when POS filter changes
        $this->selectedJournalId = null;
        
        // Optionally auto-select the first journal of this POS
        $firstJournal = Journal::whereHas('device', function ($q) {
            $q->where('point_of_sale_id', $this->posId);
        })->latest()->first();
        
        if ($firstJournal) {
            $this->selectedJournalId = $firstJournal->id;
        }
    }

    public function selectJournal(int $id): void
    {
        $this->selectedJournalId = $id;
    }

    public function render()
    {
        $pointsOfSale = PointOfSale::orderBy('name')->get();

        $journals = Journal::with('device.pointOfSale.company')
            ->when($this->posId, function ($q) {
                $q->whereHas('device', function ($dq) {
                    $dq->where('point_of_sale_id', $this->posId);
                });
            })
            ->latest()
            ->get();

        $selectedJournal = $this->selectedJournalId
            ? Journal::with('device.pointOfSale.company')->find($this->selectedJournalId)
            : null;

        return view('livewire.exports-list', compact('pointsOfSale', 'journals', 'selectedJournal'))
            ->layout('layouts.app', ['title' => 'Rapports & Exports']);
    }
}
