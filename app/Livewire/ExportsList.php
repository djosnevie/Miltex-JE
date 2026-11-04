<?php

namespace App\Livewire;

use App\Models\Anomaly;
use App\Models\Device;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\PointOfSale;
use Carbon\Carbon;
use Livewire\Component;

class ExportsList extends Component
{
    // ── Périmètre organisationnel ──────────────────────────────────────────
    public ?int $posId = null;
    public ?int $deviceId = null;
    public ?int $selectedJournalId = null;

    // ── Filtres temporels (Date & Période) ──────────────────────────────────
    public string $period = 'all'; // all, today, yesterday, this_week, this_month, last_month, this_quarter, this_year, custom
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    // ── Filtres de transaction & conformité ─────────────────────────────────
    public string $type = 'all'; // all, sale, credit_note, cancelled
    public string $paymentMode = 'all'; // all, cash, card, mobile_money, bank_transfer, credit
    public string $mcfFilter = 'all'; // all, with_error, without_error

    public function mount(): void
    {
        // Default: auto-select most recent journal if available
        $this->selectedJournalId = Journal::latest()->first()?->id;
    }

    // ── Synchronisation des Périodes prédéfinies ────────────────────────────
    public function updatedPeriod(string $value): void
    {
        $now = now();

        switch ($value) {
            case 'today':
                $this->dateFrom = $now->copy()->startOfDay()->format('Y-m-d');
                $this->dateTo   = $now->copy()->endOfDay()->format('Y-m-d');
                break;

            case 'yesterday':
                $yesterday = $now->copy()->subDay();
                $this->dateFrom = $yesterday->startOfDay()->format('Y-m-d');
                $this->dateTo   = $yesterday->endOfDay()->format('Y-m-d');
                break;

            case 'this_week':
                $this->dateFrom = $now->copy()->startOfWeek()->format('Y-m-d');
                $this->dateTo   = $now->copy()->endOfWeek()->format('Y-m-d');
                break;

            case 'this_month':
                $this->dateFrom = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->dateTo   = $now->copy()->endOfMonth()->format('Y-m-d');
                break;

            case 'last_month':
                $lastMonth = $now->copy()->subMonth();
                $this->dateFrom = $lastMonth->startOfMonth()->format('Y-m-d');
                $this->dateTo   = $lastMonth->endOfMonth()->format('Y-m-d');
                break;

            case 'this_quarter':
                $this->dateFrom = $now->copy()->firstOfQuarter()->format('Y-m-d');
                $this->dateTo   = $now->copy()->lastOfQuarter()->format('Y-m-d');
                break;

            case 'this_year':
                $this->dateFrom = $now->copy()->startOfYear()->format('Y-m-d');
                $this->dateTo   = $now->copy()->endOfYear()->format('Y-m-d');
                break;

            case 'all':
                $this->dateFrom = null;
                $this->dateTo   = null;
                break;

            case 'custom':
                // User specifies manually
                break;
        }
    }

    public function updatedDateFrom(): void
    {
        if ($this->period !== 'custom' && $this->period !== 'all') {
            $this->period = 'custom';
        }
    }

    public function updatedDateTo(): void
    {
        if ($this->period !== 'custom' && $this->period !== 'all') {
            $this->period = 'custom';
        }
    }

    public function updatedPosId(): void
    {
        $this->deviceId = null;
        $this->selectedJournalId = null;

        // Auto-select first journal of selected POS if available
        if ($this->posId) {
            $firstJournal = Journal::whereHas('device', function ($q) {
                $q->where('point_of_sale_id', $this->posId);
            })->latest()->first();

            if ($firstJournal) {
                $this->selectedJournalId = $firstJournal->id;
            }
        }
    }

    public function updatedDeviceId(): void
    {
        $this->selectedJournalId = null;

        if ($this->deviceId) {
            $firstJournal = Journal::where('device_id', $this->deviceId)->latest()->first();
            if ($firstJournal) {
                $this->selectedJournalId = $firstJournal->id;
            }
        }
    }

    public function resetFilters(): void
    {
        $this->posId = null;
        $this->deviceId = null;
        $this->selectedJournalId = null;
        $this->period = 'all';
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->type = 'all';
        $this->paymentMode = 'all';
        $this->mcfFilter = 'all';
    }

    public function selectJournal(int $id): void
    {
        $this->selectedJournalId = $id;
    }

    /** Paramètres d'URL pour les boutons d'exportation */
    public function getExportQueryParams(): array
    {
        $params = [];
        if ($this->dateFrom) $params['from'] = $this->dateFrom;
        if ($this->dateTo) $params['to'] = $this->dateTo;
        if ($this->deviceId) $params['device_id'] = $this->deviceId;
        if ($this->type && $this->type !== 'all') $params['type'] = $this->type;
        if ($this->paymentMode && $this->paymentMode !== 'all') $params['payment_mode'] = $this->paymentMode;
        if ($this->mcfFilter && $this->mcfFilter !== 'all') $params['mcf_filter'] = $this->mcfFilter;

        return $params;
    }

    public function render()
    {
        $pointsOfSale = PointOfSale::orderBy('name')->get();

        // Dispositifs DEF filtrés selon le PDV sélectionné
        $devices = Device::when($this->posId, fn($q) => $q->where('point_of_sale_id', $this->posId))
            ->orderBy('nid')
            ->get();

        // Journaux filtrés selon PDV / DEF
        $journals = Journal::with(['device.pointOfSale.company'])
            ->when($this->posId, function ($q) {
                $q->whereHas('device', fn($dq) => $dq->where('point_of_sale_id', $this->posId));
            })
            ->when($this->deviceId, fn($q) => $q->where('device_id', $this->deviceId))
            ->latest()
            ->get();

        $selectedJournal = $this->selectedJournalId
            ? Journal::with(['device.pointOfSale.company'])->find($this->selectedJournalId)
            : null;

        // ── Calcul des statistiques dynamiques du périmètre filtré ───────────
        $invQuery = Invoice::query();

        if ($this->selectedJournalId) {
            $invQuery->where('journal_id', $this->selectedJournalId);
        } elseif ($this->posId) {
            $invQuery->whereHas('journal.device', fn($q) => $q->where('point_of_sale_id', $this->posId));
        }

        if ($this->deviceId) {
            $invQuery->whereHas('journal', fn($q) => $q->where('device_id', $this->deviceId));
        }

        if ($this->dateFrom) {
            $fromStr = strlen($this->dateFrom) === 10 ? $this->dateFrom . ' 00:00:00' : $this->dateFrom;
            $invQuery->where('date_time', '>=', $fromStr);
        }
        if ($this->dateTo) {
            $toStr = strlen($this->dateTo) === 10 ? $this->dateTo . ' 23:59:59' : $this->dateTo;
            $invQuery->where('date_time', '<=', $toStr);
        }

        if ($this->type && $this->type !== 'all') {
            $invQuery->where('type', $this->type);
        }

        if ($this->paymentMode && $this->paymentMode !== 'all') {
            $invQuery->where('payment_mode', 'LIKE', '%' . $this->paymentMode . '%');
        }

        if ($this->mcfFilter === 'with_error') {
            $invQuery->where('has_mcf_error', true);
        } elseif ($this->mcfFilter === 'without_error') {
            $invQuery->where('has_mcf_error', false);
        }

        $filteredCount      = (clone $invQuery)->count();
        $filteredSalesCount = (clone $invQuery)->where('type', 'sale')->count();
        $filteredCreditCount= (clone $invQuery)->where('type', 'credit_note')->count();
        $filteredTtc        = (clone $invQuery)->where('type', 'sale')->sum('total_ttc');
        $filteredTva        = (clone $invQuery)->where('type', 'sale')->sum('total_tva');

        // Anomalies du périmètre
        $anomQuery = Anomaly::query();
        if ($this->selectedJournalId) {
            $anomQuery->where('journal_id', $this->selectedJournalId);
        } elseif ($this->posId) {
            $anomQuery->whereHas('journal.device', fn($q) => $q->where('point_of_sale_id', $this->posId));
        }
        if ($this->deviceId) {
            $anomQuery->whereHas('journal', fn($q) => $q->where('device_id', $this->deviceId));
        }
        if ($this->dateFrom) {
            $anomQuery->where('created_at', '>=', strlen($this->dateFrom) === 10 ? $this->dateFrom . ' 00:00:00' : $this->dateFrom);
        }
        if ($this->dateTo) {
            $anomQuery->where('created_at', '<=', strlen($this->dateTo) === 10 ? $this->dateTo . ' 23:59:59' : $this->dateTo);
        }
        $filteredAnomaliesCount = $anomQuery->count();

        $hasActiveFilters = $this->posId !== null 
            || $this->deviceId !== null 
            || $this->period !== 'all' 
            || $this->dateFrom !== null 
            || $this->dateTo !== null 
            || $this->type !== 'all' 
            || $this->paymentMode !== 'all' 
            || $this->mcfFilter !== 'all';

        $queryParams = $this->getExportQueryParams();

        return view('livewire.exports-list', [
            'posId'                  => $this->posId,
            'deviceId'               => $this->deviceId,
            'selectedJournalId'      => $this->selectedJournalId,
            'period'                 => $this->period,
            'dateFrom'               => $this->dateFrom,
            'dateTo'                 => $this->dateTo,
            'type'                   => $this->type,
            'paymentMode'            => $this->paymentMode,
            'mcfFilter'              => $this->mcfFilter,
            'queryParams'            => $queryParams,
            'pointsOfSale'           => $pointsOfSale,
            'devices'                => $devices,
            'journals'               => $journals,
            'selectedJournal'        => $selectedJournal,
            'filteredCount'          => $filteredCount,
            'filteredSalesCount'     => $filteredSalesCount,
            'filteredCreditCount'    => $filteredCreditCount,
            'filteredTtc'            => $filteredTtc,
            'filteredTva'            => $filteredTva,
            'filteredAnomaliesCount' => $filteredAnomaliesCount,
            'hasActiveFilters'       => $hasActiveFilters,
        ])->layout('layouts.app', ['title' => 'Rapports & Exports']);
    }
}
