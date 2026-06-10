<?php

namespace App\Livewire;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    // ── List filters ──────────────────────────────────────────────────────
    public string $search    = '';
    public string $roleFilter = '';

    // ── Modal state ───────────────────────────────────────────────────────
    public bool   $showModal  = false;
    public bool   $isEditing  = false;
    public ?int   $editingId  = null;

    // ── Form fields ───────────────────────────────────────────────────────
    #[Rule('required|min:2|max:100')]
    public string $formName      = '';

    #[Rule('required|email|max:150')]
    public string $formEmail     = '';

    #[Rule('required|in:super_admin,admin,analyst')]
    public string $formRole      = 'analyst';

    #[Rule('nullable|min:8|same:formPasswordConfirmation')]
    public string $formPassword  = '';
    public string $formPasswordConfirmation = '';

    public bool   $formIsActive  = true;

    public ?int   $formTenantId  = null;

    // ── Password reset confirm ─────────────────────────────────────────────
    public bool  $showDeleteConfirm = false;
    public ?int  $deleteTargetId    = null;

    public function updatingSearch(): void { $this->resetPage(); }

    // ── Open create modal ─────────────────────────────────────────────────
    public function openCreate(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    // ── Open edit modal ───────────────────────────────────────────────────
    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->resetForm();
        $this->isEditing  = true;
        $this->editingId  = $id;
        $this->formName   = $user->name;
        $this->formEmail  = $user->email;
        $this->formRole   = $user->role;
        $this->formIsActive = $user->is_active;
        $this->formTenantId = $user->tenant_id;
        $this->showModal  = true;
    }

    // ── Save (create or update) ───────────────────────────────────────────
    public function save(): void
    {
        $this->validateOnly('formName');
        $this->validateOnly('formRole');

        // Email uniqueness check
        $emailRule = $this->isEditing
            ? "required|email|unique:users,email,{$this->editingId}"
            : 'required|email|unique:users,email';

        $this->validate(['formEmail' => $emailRule], [
            'formEmail.unique' => 'Cette adresse e-mail est déjà utilisée.',
        ]);

        if ($this->isEditing) {
            $user = User::findOrFail($this->editingId);
            $data = [
                'name'      => $this->formName,
                'email'     => $this->formEmail,
                'role'      => $this->formRole,
                'is_active' => $this->formIsActive,
                'tenant_id' => $this->formRole === 'super_admin' ? null : $this->formTenantId,
            ];
            if ($this->formPassword) {
                $this->validate(
                    ['formPassword' => 'min:8|same:formPasswordConfirmation'],
                    [
                        'formPassword.min'  => 'Le mot de passe doit contenir au moins 8 caractères.',
                        'formPassword.same' => 'Les deux mots de passe ne correspondent pas.',
                    ]
                );
                $data['password'] = Hash::make($this->formPassword);
            }
            $user->update($data);
            session()->flash('success', "Utilisateur {$user->name} mis à jour.");
        } else {
            $this->validate(
                ['formPassword' => 'required|min:8|same:formPasswordConfirmation'],
                [
                    'formPassword.required' => 'Le mot de passe est obligatoire.',
                    'formPassword.min'      => 'Le mot de passe doit contenir au moins 8 caractères.',
                    'formPassword.same'     => 'Les deux mots de passe ne correspondent pas.',
                ]
            );
            User::create([
                'name'      => $this->formName,
                'email'     => $this->formEmail,
                'password'  => Hash::make($this->formPassword),
                'role'      => $this->formRole,
                'is_active' => $this->formIsActive,
                'tenant_id' => $this->formRole === 'super_admin' ? null : $this->formTenantId,
            ]);
            session()->flash('success', "Utilisateur {$this->formName} créé avec succès.");
        }

        $this->showModal = false;
        $this->resetForm();
    }

    // ── Toggle active status ──────────────────────────────────────────────
    public function toggleActive(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
            return;
        }
        $user->update(['is_active' => ! $user->is_active]);
        session()->flash('success', $user->is_active ? "{$user->name} activé." : "{$user->name} désactivé.");
    }

    // ── Delete confirm ────────────────────────────────────────────────────
    public function confirmDelete(int $id): void
    {
        $this->deleteTargetId    = $id;
        $this->showDeleteConfirm = true;
    }

    public function deleteUser(): void
    {
        $user = User::findOrFail($this->deleteTargetId);
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            $this->showDeleteConfirm = false;
            return;
        }
        $user->delete();
        session()->flash('success', "Utilisateur supprimé.");
        $this->showDeleteConfirm = false;
        $this->deleteTargetId    = null;
    }

    // ── Helpers ───────────────────────────────────────────────────────────
    private function resetForm(): void
    {
        $this->formName                 = '';
        $this->formEmail                = '';
        $this->formRole                 = 'analyst';
        $this->formPassword             = '';
        $this->formPasswordConfirmation = '';
        $this->formIsActive             = true;
        $this->formTenantId             = Auth::user()->tenant_id;
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search,     fn($q) => $q->where(function ($q) {
                $q->where('name',  'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->when(! Auth::user()->isSuperAdmin(), fn($q) => $q->where('tenant_id', Auth::user()->tenant_id))
            ->orderBy('name')
            ->paginate(20);

        $tenants = Tenant::orderBy('name')->get();

        return view('livewire.user-management', compact('users', 'tenants'))
            ->layout('layouts.app', ['title' => 'Utilisateurs']);
    }
}
