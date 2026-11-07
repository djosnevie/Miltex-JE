<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Device;
use App\Models\PointOfSale;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Espace SuperAdmin — Quotas & Utilisateurs'])]
class SuperAdminHub extends Component
{
    use WithPagination;

    // ── Onglet Actif ────────────────────────────────────────────────────────
    public string $activeTab = 'quotas'; // 'quotas', 'users', 'tenants'

    // ── Quotas Section State ────────────────────────────────────────────────
    public bool $showQuotaModal = false;
    public ?int $selectedTenantId = null;
    public ?int $selectedCompanyId = null;
    public string $targetEntityName = '';
    public int $formMaxPointsOfSale = 5;
    public bool $formQuotaIsUnlimited = false;

    // Expandable rows for tenants/companies in quota view
    public array $expandedTenantIds = [];

    // ── Users Section State ─────────────────────────────────────────────────
    public string $userSearch = '';
    public string $userRoleFilter = '';
    public string $userTenantFilter = '';
    public string $userStatusFilter = '';

    public bool $showUserModal = false;
    public bool $isEditingUser = false;
    public ?int $userEditingId = null;

    #[Rule('required|min:2|max:100')]
    public string $userName = '';

    #[Rule('required|email|max:150')]
    public string $userEmail = '';

    #[Rule('required|in:super_admin,admin,analyst')]
    public string $userRole = 'analyst';

    public ?int $userTenantId = null;
    public string $userPassword = '';
    public string $userPasswordConfirmation = '';
    public bool $userIsActive = true;

    // Password quick reset modal
    public bool $showResetPasswordModal = false;
    public ?int $resetUserTargetId = null;
    public string $resetUserTargetName = '';
    public string $newGeneratedPassword = '';

    // ── Tenants Section State ───────────────────────────────────────────────
    public bool $showTenantModal = false;
    public bool $isEditingTenant = false;
    public ?int $tenantEditingId = null;

    #[Rule('required|min:2|max:150')]
    public string $tenantName = '';

    #[Rule('nullable|max:50|alpha_dash')]
    public string $tenantSubdomain = '';

    #[Rule('required|in:active,suspended,trial')]
    public string $tenantStatus = 'active';

    #[Rule('required|integer|min:1|max:1000')]
    public int $tenantMaxPos = 5;

    // Optional company fields when creating tenant
    public string $companyName = '';
    public string $companyNif = '';
    public string $companyRccm = '';
    public string $companyCity = 'Kinshasa';

    public function mount(): void
    {
        // Enforce SuperAdmin access
        if (! Auth::check() || ! Auth::user()->isSuperAdmin()) {
            abort(403, 'Accès réservé exclusivement aux Super Administrateurs.');
        }

        // Auto-expand first tenant
        $firstTenant = Tenant::first();
        if ($firstTenant) {
            $this->expandedTenantIds[] = $firstTenant->id;
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage('usersPage');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // QUOTAS DE POINTS DE VENTE
    // ══════════════════════════════════════════════════════════════════════════

    public function toggleExpandTenant(int $tenantId): void
    {
        if (in_array($tenantId, $this->expandedTenantIds)) {
            $this->expandedTenantIds = array_diff($this->expandedTenantIds, [$tenantId]);
        } else {
            $this->expandedTenantIds[] = $tenantId;
        }
    }

    public function openEditTenantQuota(int $tenantId): void
    {
        $tenant = Tenant::findOrFail($tenantId);
        $this->selectedTenantId    = $tenant->id;
        $this->selectedCompanyId   = null;
        $this->targetEntityName    = $tenant->name;
        $this->formQuotaIsUnlimited = ($tenant->max_points_of_sale === null || $tenant->max_points_of_sale <= 0);
        $this->formMaxPointsOfSale = $this->formQuotaIsUnlimited ? 10 : (int)$tenant->max_points_of_sale;
        $this->showQuotaModal      = true;
    }

    public function openEditCompanyQuota(int $companyId): void
    {
        $company = Company::findOrFail($companyId);
        $this->selectedCompanyId   = $company->id;
        $this->selectedTenantId    = null;
        $this->targetEntityName    = $company->name . ' (NIF: ' . ($company->nif ?? 'N/A') . ')';
        $this->formQuotaIsUnlimited = ($company->max_points_of_sale === null);
        $this->formMaxPointsOfSale = $company->max_points_of_sale ?? 5;
        $this->showQuotaModal      = true;
    }

    public function saveQuota(): void
    {
        $quotaValue = $this->formQuotaIsUnlimited ? null : max(1, (int)$this->formMaxPointsOfSale);

        if ($this->selectedTenantId) {
            $tenant = Tenant::findOrFail($this->selectedTenantId);
            $tenant->update(['max_points_of_sale' => $quotaValue]);
            session()->flash('success', "Quota de points de vente mis à jour pour l'organisation \"{$tenant->name}\" : " . ($quotaValue ? "{$quotaValue} PDV max" : "Illimité") . ".");
        } elseif ($this->selectedCompanyId) {
            $company = Company::findOrFail($this->selectedCompanyId);
            $company->update(['max_points_of_sale' => $quotaValue]);
            session()->flash('success', "Quota de points de vente mis à jour pour la société \"{$company->name}\" : " . ($quotaValue ? "{$quotaValue} PDV max" : "Hérité de l'organisation") . ".");
        }

        $this->showQuotaModal = false;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // GESTION DES UTILISATEURS (MULTI-TENANTS)
    // ══════════════════════════════════════════════════════════════════════════

    public function updatingUserSearch(): void
    {
        $this->resetPage('usersPage');
    }

    public function openCreateUser(): void
    {
        $this->resetUserForm();
        $this->isEditingUser = false;
        $this->userEditingId = null;
        $this->userPassword = '';
        $this->userPasswordConfirmation = '';
        $this->showUserModal = true;
    }

    public function openEditUser(int $id): void
    {
        $user = User::findOrFail($id);
        $this->resetUserForm();
        $this->isEditingUser = true;
        $this->userEditingId = $user->id;
        $this->userName      = $user->name;
        $this->userEmail     = $user->email;
        $this->userRole      = $user->role;
        $this->userTenantId  = $user->tenant_id;
        $this->userIsActive  = (bool) $user->is_active;
        $this->userPassword  = '';
        $this->userPasswordConfirmation = '';
        $this->showUserModal = true;
    }

    public function saveUser(): void
    {
        $rules = [
            'userName' => 'required|min:2|max:100',
            'userRole' => 'required|in:super_admin,admin,analyst',
        ];

        if ($this->isEditingUser) {
            $rules['userEmail'] = 'required|email|max:150|unique:users,email,' . $this->userEditingId;
            if (!empty($this->userPassword)) {
                $rules['userPassword'] = 'min:8|same:userPasswordConfirmation';
            }
        } else {
            $rules['userEmail'] = 'required|email|max:150|unique:users,email';
            $rules['userPassword'] = 'required|min:8|same:userPasswordConfirmation';
        }

        $this->validate($rules);

        $data = [
            'name'      => $this->userName,
            'email'     => $this->userEmail,
            'role'      => $this->userRole,
            'tenant_id' => $this->userRole === 'super_admin' ? null : $this->userTenantId,
            'is_active' => $this->userIsActive,
        ];

        if (!empty($this->userPassword)) {
            $data['password'] = Hash::make($this->userPassword);
        }

        if ($this->isEditingUser) {
            User::findOrFail($this->userEditingId)->update($data);
            session()->flash('success', "Utilisateur \"{$this->userName}\" mis à jour avec succès.");
        } else {
            User::create($data);
            session()->flash('success', "Nouvel utilisateur \"{$this->userName}\" créé avec succès.");
        }

        $this->showUserModal = false;
        $this->resetUserForm();
    }

    public function toggleUserActive(int $id): void
    {
        if ($id === Auth::id()) {
            session()->flash('error', 'Vous ne pouvez pas désactiver votre propre compte SuperAdmin.');
            return;
        }

        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'activé' : 'désactivé';
        session()->flash('success', "Le compte de {$user->name} a été {$statusText}.");
    }

    public function openResetPassword(int $id): void
    {
        $user = User::findOrFail($id);
        $this->resetUserTargetId   = $user->id;
        $this->resetUserTargetName = $user->name;
        $this->newGeneratedPassword = Str::password(12, true, true, false);
        $this->showResetPasswordModal = true;
    }

    public function confirmPasswordReset(): void
    {
        if (!$this->resetUserTargetId || empty($this->newGeneratedPassword)) {
            return;
        }

        $user = User::findOrFail($this->resetUserTargetId);
        $user->update([
            'password' => Hash::make($this->newGeneratedPassword),
        ]);

        session()->flash('success', "Mot de passe réinitialisé pour {$user->name}. Nouveau mot de passe : {$this->newGeneratedPassword}");
        $this->showResetPasswordModal = false;
    }

    public function deleteUser(int $id): void
    {
        if ($id === Auth::id()) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte SuperAdmin.');
            return;
        }

        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        session()->flash('success', "Utilisateur \"{$name}\" supprimé.");
    }

    private function resetUserForm(): void
    {
        $this->reset(['userName', 'userEmail', 'userRole', 'userTenantId', 'userPassword', 'userPasswordConfirmation', 'userIsActive', 'userEditingId', 'isEditingUser']);
        $this->resetValidation();
        $this->userRole     = 'analyst';
        $this->userIsActive = true;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // GESTION DES ORGANISATIONS (TENANTS)
    // ══════════════════════════════════════════════════════════════════════════

    public function openCreateTenant(): void
    {
        $this->resetTenantForm();
        $this->isEditingTenant  = false;
        $this->tenantEditingId  = null;
        $this->tenantMaxPos     = 5;
        $this->tenantStatus     = 'active';
        $this->showTenantModal  = true;
    }

    public function openEditTenant(int $id): void
    {
        $tenant = Tenant::findOrFail($id);
        $this->resetTenantForm();
        $this->isEditingTenant  = true;
        $this->tenantEditingId  = $tenant->id;
        $this->tenantName       = $tenant->name;
        $this->tenantSubdomain  = $tenant->subdomain ?? '';
        $this->tenantStatus     = $tenant->status ?? 'active';
        $this->tenantMaxPos     = $tenant->max_points_of_sale ?? 5;
        $this->showTenantModal  = true;
    }

    public function saveTenant(): void
    {
        $rules = [
            'tenantName'    => 'required|min:2|max:150',
            'tenantStatus'  => 'required|in:active,suspended,trial',
            'tenantMaxPos'  => 'required|integer|min:1|max:1000',
        ];

        if ($this->isEditingTenant) {
            $rules['tenantSubdomain'] = 'nullable|max:50|alpha_dash|unique:tenants,subdomain,' . $this->tenantEditingId;
        } else {
            $rules['tenantSubdomain'] = 'nullable|max:50|alpha_dash|unique:tenants,subdomain';
        }

        $this->validate($rules);

        $tenantData = [
            'name'               => $this->tenantName,
            'subdomain'          => !empty($this->tenantSubdomain) ? Str::slug($this->tenantSubdomain) : Str::slug($this->tenantName),
            'status'             => $this->tenantStatus,
            'max_points_of_sale' => $this->tenantMaxPos,
        ];

        if ($this->isEditingTenant) {
            Tenant::findOrFail($this->tenantEditingId)->update($tenantData);
            session()->flash('success', "Organisation \"{$this->tenantName}\" mise à jour.");
        } else {
            $tenant = Tenant::create($tenantData);

            // Optional: auto-create initial company if specified
            if (!empty($this->companyName) || !empty($this->companyNif)) {
                Company::create([
                    'tenant_id'    => $tenant->id,
                    'name'         => !empty($this->companyName) ? $this->companyName : $tenant->name,
                    'nif'          => !empty($this->companyNif) ? $this->companyNif : 'A' . rand(1000000, 9999999) . 'Z',
                    'rccm'         => $this->companyRccm ?: null,
                    'address_city' => $this->companyCity ?: 'Kinshasa',
                ]);
            }

            session()->flash('success', "Nouvelle organisation \"{$this->tenantName}\" créée avec un quota de {$this->tenantMaxPos} points de vente.");
        }

        $this->showTenantModal = false;
        $this->resetTenantForm();
    }

    public function toggleTenantStatus(int $id): void
    {
        $tenant = Tenant::findOrFail($id);
        $newStatus = match ($tenant->status) {
            'active'    => 'suspended',
            'suspended' => 'active',
            'trial'     => 'active',
            default     => 'active',
        };

        $tenant->update(['status' => $newStatus]);
        session()->flash('success', "Statut de l'organisation \"{$tenant->name}\" modifié en : {$newStatus}.");
    }

    private function resetTenantForm(): void
    {
        $this->reset(['tenantName', 'tenantSubdomain', 'tenantStatus', 'tenantMaxPos', 'companyName', 'companyNif', 'companyRccm', 'companyCity', 'tenantEditingId', 'isEditingTenant']);
        $this->resetValidation();
        $this->tenantStatus = 'active';
        $this->tenantMaxPos = 5;
    }

    public function deletePasskey(int $id): void
    {
        $passkey = \App\Models\Passkey::findOrFail($id);
        if ($passkey->user_id !== Auth::id() && ! Auth::user()->isSuperAdmin()) {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        $name = $passkey->name;
        $passkey->delete();
        session()->flash('success', "Clé biométrique \"{$name}\" révoquée avec succès.");
    }

    // ══════════════════════════════════════════════════════════════════════════
    // RENDER
    // ══════════════════════════════════════════════════════════════════════════

    public function render()
    {
        // ── Global Platform Metrics ──────────────────────────────────────────
        $totalTenants     = Tenant::count();
        $totalCompanies   = Company::count();
        $totalPos         = PointOfSale::count();
        $totalDef         = Device::count();
        $totalUsers       = User::count();
        $totalSuperAdmins = User::where('role', 'super_admin')->count();
        $totalAdmins      = User::where('role', 'admin')->count();
        $totalAnalysts    = User::where('role', 'analyst')->count();
        $totalActiveUsers = User::where('is_active', true)->count();
        $totalPasskeys    = \App\Models\Passkey::count();

        // ── Quotas & Organizations List ──────────────────────────────────────
        $tenantsWithQuotas = Tenant::with(['companies.pointsOfSale.devices', 'users'])
            ->orderBy('name')
            ->get();

        // ── Users List with Filters ──────────────────────────────────────────
        $usersQuery = User::with(['tenant', 'passkeys'])
            ->when($this->userSearch, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%' . $this->userSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->userSearch . '%');
                });
            })
            ->when($this->userRoleFilter, fn($q) => $q->where('role', $this->userRoleFilter))
            ->when($this->userTenantFilter !== '', function ($q) {
                if ($this->userTenantFilter === 'global') {
                    $q->whereNull('tenant_id');
                } else {
                    $q->where('tenant_id', (int)$this->userTenantFilter);
                }
            })
            ->when($this->userStatusFilter !== '', function ($q) {
                $q->where('is_active', $this->userStatusFilter === 'active');
            })
            ->orderBy('role')
            ->orderBy('name');

        $users = $usersQuery->paginate(15, ['*'], 'usersPage');

        $allTenants = Tenant::orderBy('name')->get();

        // Current SuperAdmin's passkeys
        $myPasskeys = Auth::user()->passkeys()->latest()->get();

        $data = array_merge(get_object_vars($this), compact(
            'totalTenants',
            'totalCompanies',
            'totalPos',
            'totalDef',
            'totalUsers',
            'totalSuperAdmins',
            'totalAdmins',
            'totalAnalysts',
            'totalActiveUsers',
            'totalPasskeys',
            'tenantsWithQuotas',
            'users',
            'allTenants',
            'myPasskeys'
        ));

        return view('livewire.super-admin-hub', $data);
    }
}
