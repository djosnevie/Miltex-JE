<?php

namespace App\Livewire;

use App\Models\Device;
use App\Models\Journal;
use App\Services\JournalParserService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportJournal extends Component
{
    use WithFileUploads;

    public $files = [];
    public ?int $deviceId = null;
    public array $results  = [];
    public bool  $importing = false;

    protected function rules(): array
    {
        return [
            'files.*' => ['required', 'file', 'mimes:txt', 'max:51200'], // 50 MB max
            'deviceId' => ['required', 'exists:devices,id'],
        ];
    }

    public function updatedFiles(): void
    {
        $this->results = [];
    }

    public function import(JournalParserService $parser): void
    {
        $this->validate();
        $this->importing = true;
        $this->results   = [];

        foreach ($this->files as $file) {
            $originalName = $file->getClientOriginalName();
            try {
                // Store the file permanently
                $storedPath = $file->storeAs(
                    'journals',
                    $originalName,
                    'local'
                );
                $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($storedPath);

                $journal = $parser->parseFile($fullPath, $originalName, $this->deviceId);

                $this->results[] = [
                    'status'  => 'success',
                    'name'    => $originalName,
                    'journal' => $journal,
                    'message' => sprintf(
                        '%d ventes | %d avoirs | %d annulées — CA : %s %s',
                        $journal->total_invoices,
                        $journal->total_credits,
                        $journal->total_cancelled,
                        number_format($journal->total_ttc, 2, ',', ' '),
                        $journal->currency
                    ),
                ];
            } catch (\RuntimeException $e) {
                $this->results[] = [
                    'status'  => 'warning',
                    'name'    => $originalName,
                    'message' => $e->getMessage(),
                ];
            } catch (\Exception $e) {
                $this->results[] = [
                    'status'  => 'error',
                    'name'    => $originalName,
                    'message' => 'Erreur inattendue : ' . $e->getMessage(),
                ];
            }
        }

        $this->files    = [];
        $this->importing = false;
    }

    public function render()
    {
        $devices = Device::with('pointOfSale.company')
            ->orderBy('nid')
            ->get();

        $recentJournals = Journal::with('device.pointOfSale')
            ->latest()
            ->limit(8)
            ->get();

        return view('livewire.import-journal', compact('devices', 'recentJournals'))
            ->layout('layouts.app', ['title' => 'Importer un Journal']);
    }
}
