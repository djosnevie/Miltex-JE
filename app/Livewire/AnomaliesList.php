<?php

namespace App\Livewire;

use App\Models\Anomaly;
use App\Models\Journal;
use Livewire\Component;
use Livewire\WithPagination;

class AnomaliesList extends Component
{
    use WithPagination;

    public string $severity   = '';
    public string $type       = '';
    public string $resolved   = '0';
    public ?int   $journalId  = null;

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function resolve(int $id, string $notes = ''): void
    {
        Anomaly::findOrFail($id)->update(['is_resolved' => true, 'notes' => $notes]);
        $this->dispatch('notify', message: 'Anomalie marquée comme résolue.');
    }

    public function render()
    {
        $query = Anomaly::with(['invoice', 'journal.device.pointOfSale'])
            ->when($this->journalId, fn($q) => $q->where('journal_id', $this->journalId))
            ->when($this->severity,  fn($q) => $q->where('severity', $this->severity))
            ->when($this->type,      fn($q) => $q->where('type', $this->type))
            ->where('is_resolved', $this->resolved === '1')
            ->orderByRaw("FIELD(severity,'critical','warning','info')")
            ->orderByDesc('created_at');

        $anomalies = $query->paginate(20);
        $journals  = Journal::latest()->limit(30)->get();

        return view('livewire.anomalies-list', compact('anomalies', 'journals'))
            ->layout('layouts.app', ['title' => 'Gestion des Anomalies']);
    }
}
