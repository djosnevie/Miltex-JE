<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Device;
use App\Models\PointOfSale;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;

class PointsOfSaleManagement extends Component
{
    // ── List state ────────────────────────────────────────────────────────
    public string $search      = '';
    public array  $expandedPos = [];   // IDs of expanded POS rows

    // ── POS Modal ─────────────────────────────────────────────────────────
    public bool  $showPosModal  = false;
    public bool  $posIsEditing  = false;
    public ?int  $posEditingId  = null;

    #[Rule('required|min:2|max:150')]
    public string $posName        = '';
    #[Rule('nullable|max:50')]
    public string $posLocationId  = '';
    #[Rule('nullable|max:100')]
    public string $posCity        = '';
    #[Rule('nullable|max:200')]
    public string $posAddress     = '';
    #[Rule('nullable|max:30')]
    public string $posPhone       = '';
    #[Rule('nullable|email|max:150')]
    public string $posEmail       = '';
    #[Rule('nullable|max:500')]
    public string $posDescription = '';
    public bool   $posIsActive    = true;
    public ?int   $posCompanyId   = null;

    // ── DEF Modal ─────────────────────────────────────────────────────────
    public bool  $showDefModal  = false;
    public bool  $defIsEditing  = false;
    public ?int  $defEditingId  = null;
    public ?int  $defPosId      = null;   // POS context for new DEF

    #[Rule('required|max:50')]
    public string $defNid            = '';
    #[Rule('nullable|max:50')]
    public string $defIsf            = '';
    #[Rule('nullable|max:50')]
    public string $defSerialNumber   = '';
    #[Rule('nullable|max:100')]
    public string $defModel          = '';
    #[Rule('nullable|max:30')]
    public string $defFirmware       = '';
    #[Rule('required|in:active,inactive,maintenance')]
    public string $defStatus         = 'active';
    #[Rule('nullable|max:200')]
    public string $defDescription    = '';

    // ── Delete confirm ────────────────────────────────────────────────────
    public bool  $showDeleteConfirm = false;
    public ?int  $deleteTargetId    = null;
    public string $deleteType       = ''; // 'pos' or 'def'

    public function mount(): void
    {
        $this->posCompanyId = Company::first()?->id;
    }

    // ── Toggle expand ─────────────────────────────────────────────────────
    public function toggleExpand(int $posId): void
    {
        if (in_array($posId, $this->expandedPos)) {
            $this->expandedPos = array_values(array_diff($this->expandedPos, [$posId]));
        } else {
            $this->expandedPos[] = $posId;
        }
    }

    // ── POS CRUD ──────────────────────────────────────────────────────────

    public function openCreatePos(): void
    {
        $this->resetPosForm();
        $this->posIsEditing = false;
        $this->posEditingId = null;
        $this->showPosModal = true;
    }

    public function openEditPos(int $id): void
    {
        $pos = PointOfSale::findOrFail($id);
        $this->resetPosForm();
        $this->posIsEditing    = true;
        $this->posEditingId    = $id;
        $this->posName         = $pos->name;
        $this->posLocationId   = $pos->location_identifier ?? '';
        $this->posCity         = $pos->city ?? '';
        $this->posAddress      = $pos->address ?? '';
        $this->posPhone        = $pos->phone ?? '';
        $this->posEmail        = $pos->email ?? '';
        $this->posDescription  = $pos->description ?? '';
        $this->posIsActive     = $pos->is_active;
        $this->posCompanyId    = $pos->company_id;
        $this->showPosModal    = true;
    }

    public function savePos(): void
    {
        $this->validateOnly('posName');

        $data = [
            'company_id'          => $this->posCompanyId ?? Company::first()?->id,
            'name'                => $this->posName,
            'location_identifier' => $this->posLocationId ?: null,
            'city'                => $this->posCity ?: null,
            'address'             => $this->posAddress ?: null,
            'phone'               => $this->posPhone ?: null,
            'email'               => $this->posEmail ?: null,
            'description'         => $this->posDescription ?: null,
            'is_active'           => $this->posIsActive,
        ];

        if ($this->posIsEditing) {
            PointOfSale::findOrFail($this->posEditingId)->update($data);
            session()->flash('success', "Point de vente \"{$this->posName}\" mis à jour.");
        } else {
            $pos = PointOfSale::create($data);
            $this->expandedPos[] = $pos->id;
            session()->flash('success', "Point de vente \"{$this->posName}\" créé avec succès.");
        }

        $this->showPosModal = false;
        $this->resetPosForm();
    }

    public function togglePosActive(int $id): void
    {
        $pos = PointOfSale::findOrFail($id);
        $pos->update(['is_active' => ! $pos->is_active]);
        session()->flash('success', $pos->is_active ? "{$pos->name} activé." : "{$pos->name} désactivé.");
    }

    // ── DEF CRUD ──────────────────────────────────────────────────────────

    public function openCreateDef(int $posId): void
    {
        $this->resetDefForm();
        $this->defIsEditing = false;
        $this->defEditingId = null;
        $this->defPosId     = $posId;
        $this->showDefModal = true;
        if (! in_array($posId, $this->expandedPos)) {
            $this->expandedPos[] = $posId;
        }
    }

    public function openEditDef(int $id): void
    {
        $dev = Device::findOrFail($id);
        $this->resetDefForm();
        $this->defIsEditing     = true;
        $this->defEditingId     = $id;
        $this->defPosId         = $dev->point_of_sale_id;
        $this->defNid           = $dev->nid;
        $this->defIsf           = $dev->isf ?? '';
        $this->defSerialNumber  = $dev->serial_number ?? '';
        $this->defModel         = $dev->model ?? '';
        $this->defFirmware      = $dev->firmware_version ?? '';
        $this->defStatus        = $dev->status;
        $this->defDescription   = $dev->description ?? '';
        $this->showDefModal     = true;
    }

    public function saveDef(): void
    {
        $this->validateOnly('defNid');
        $this->validateOnly('defStatus');

        // Unique NID check
        $nidRule = $this->defIsEditing
            ? "required|max:50|unique:devices,nid,{$this->defEditingId}"
            : 'required|max:50|unique:devices,nid';

        $this->validate(['defNid' => $nidRule], [
            'defNid.unique' => 'Ce NID DEF est déjà enregistré.',
        ]);

        $data = [
            'point_of_sale_id' => $this->defPosId,
            'nid'              => strtoupper(trim($this->defNid)),
            'isf'              => $this->defIsf ?: null,
            'serial_number'    => $this->defSerialNumber ?: null,
            'model'            => $this->defModel ?: null,
            'firmware_version' => $this->defFirmware ?: null,
            'status'           => $this->defStatus,
            'description'      => $this->defDescription ?: null,
        ];

        if ($this->defIsEditing) {
            Device::findOrFail($this->defEditingId)->update($data);
            session()->flash('success', "DEF {$data['nid']} mis à jour.");
        } else {
            Device::create($data);
            session()->flash('success', "DEF {$data['nid']} ajouté avec succès.");
        }

        $this->showDefModal = false;
        $this->resetDefForm();
    }

    public function toggleDefStatus(int $id): void
    {
        $dev = Device::findOrFail($id);
        $newStatus = $dev->status === 'active' ? 'inactive' : 'active';
        $dev->update(['status' => $newStatus]);
        session()->flash('success', "DEF {$dev->nid} : statut → " . ($newStatus === 'active' ? 'Actif' : 'Inactif'));
    }

    // ── Delete ────────────────────────────────────────────────────────────

    public function confirmDelete(int $id, string $type): void
    {
        $this->deleteTargetId    = $id;
        $this->deleteType        = $type;
        $this->showDeleteConfirm = true;
    }

    public function doDelete(): void
    {
        if ($this->deleteType === 'pos') {
            $item = PointOfSale::findOrFail($this->deleteTargetId);
            $name = $item->name;
            $item->delete();
            session()->flash('success', "PDV \"{$name}\" supprimé.");
        } elseif ($this->deleteType === 'def') {
            $item = Device::findOrFail($this->deleteTargetId);
            $name = $item->nid;
            $item->delete();
            session()->flash('success', "DEF {$name} supprimé.");
        }
        $this->showDeleteConfirm = false;
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function resetPosForm(): void
    {
        $this->posName = $this->posLocationId = $this->posCity = '';
        $this->posAddress = $this->posPhone = $this->posEmail = $this->posDescription = '';
        $this->posIsActive = true;
        $this->resetValidation();
    }

    private function resetDefForm(): void
    {
        $this->defNid = $this->defIsf = $this->defSerialNumber = '';
        $this->defModel = $this->defFirmware = $this->defDescription = '';
        $this->defStatus = 'active';
        $this->resetValidation();
    }

    public function render()
    {
        $pointsOfSale = PointOfSale::with(['company', 'devices.journals'])
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%")
                  ->orWhere('location_identifier', 'like', "%{$this->search}%");
            }))
            ->orderBy('name')
            ->get();

        $companies = Company::orderBy('name')->get();

        return view('livewire.points-of-sale-management', compact('pointsOfSale', 'companies'))
            ->layout('layouts.app', ['title' => 'Points de Vente']);
    }
}
