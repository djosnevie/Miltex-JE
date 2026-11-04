@section('title', 'Gestion des Utilisateurs')

<div>
    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom: 24px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header Bar --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; gap: 14px; flex: 1; flex-wrap: wrap;">
                <div style="min-width: 240px; flex: 1;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Rechercher un utilisateur</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <svg style="position: absolute; left: 12px; color: var(--text-muted); width: 16px; height: 16px; pointer-events: none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nom complet, adresse e-mail..."
                            style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 9px 12px 9px 36px; border-radius: 9px; font-size: 13px; outline: none;">
                    </div>
                </div>
                <div style="width: 180px;">
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Filtrer par Rôle</label>
                    <select wire:model.live="roleFilter" style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none;">
                        <option value="">Tous les rôles</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="analyst">Analyste</option>
                    </select>
                </div>
            </div>
            <div style="margin-top: 22px;">
                <button wire:click="openCreate" class="btn btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Nouvel Utilisateur</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Users Table Card --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 24px; border-bottom: 1px solid var(--border-subtle); font-size: 13px; color: var(--text-secondary); display: flex; align-items: center; justify-content: space-between; background: rgba(255, 255, 255, 0.01);">
            <div>
                <strong style="color: var(--text-main);" class="tabular-nums">{{ $users->total() }}</strong> utilisateur(s) configuré(s)
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="padding-left: 24px;">Utilisateur</th>
                        <th>Adresse E-mail</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Organisation / Tenant</th>
                        <th style="text-align: right; padding-right: 24px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td style="padding-left: 24px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--text-main);">{{ $user->name }}</div>
                                        @if($user->id === auth()->id())
                                            <div style="font-size: 10.5px; color: var(--primary); font-weight: 600;">(Votre compte actif)</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="color: var(--text-secondary); font-size: 13px; font-family: monospace;">{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleBadgeClass = match($user->role) {
                                        'super_admin' => 'badge-warning',
                                        'admin'       => 'badge-credit',
                                        default       => 'badge-sale',
                                    };
                                @endphp
                                <span class="badge {{ $roleBadgeClass }}">
                                    <span class="status-dot"></span>
                                    {{ $user->roleBadge() }}
                                </span>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge badge-sale">
                                        <span class="status-dot"></span>
                                        Actif
                                    </span>
                                @else
                                    <span class="badge badge-cancelled">
                                        <span class="status-dot"></span>
                                        Inactif
                                    </span>
                                @endif
                            </td>
                            <td style="font-size: 12.5px; color: var(--text-muted);">
                                {{ $user->tenant?->name ?? 'Miltex Principal' }}
                            </td>
                            <td style="text-align: right; padding-right: 24px;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <button wire:click="openEdit({{ $user->id }})"
                                        class="btn btn-ghost btn-sm"
                                        title="Modifier les informations">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        <span>Modifier</span>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button wire:click="toggleActive({{ $user->id }})"
                                            class="btn btn-ghost btn-sm btn-icon"
                                            title="{{ $user->is_active ? 'Désactiver le compte' : 'Activer le compte' }}"
                                            style="color: {{ $user->is_active ? '#D97706' : '#059669' }};">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                @if($user->is_active)
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                                @endif
                                            </svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $user->id }})"
                                            class="btn btn-ghost btn-sm btn-icon"
                                            title="Supprimer définitivement"
                                            style="color: #DC2626;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
                                Aucun compte utilisateur ne correspond aux critères de recherche.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div style="padding: 16px 24px; border-top: 1px solid var(--border-subtle); background: #F8FAFC;">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- ══ Create / Edit Modal ══ --}}
    @if($showModal)
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); z-index: 60; display: flex; align-items: center; justify-content: center; padding: 20px;" wire:click.self="$set('showModal', false)">
            <div class="card animate-slide-up" style="width: 100%; max-width: 480px; padding: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 16px;">
                    <h2 style="font-size: 17px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>{{ $isEditing ? 'Modifier l\'utilisateur' : 'Créer un nouvel utilisateur' }}</span>
                    </h2>
                    <button wire:click="$set('showModal', false)" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; display: flex; align-items: center;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="save">
                    {{-- Name --}}
                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Nom complet *</label>
                        <input type="text" wire:model="formName" placeholder="Ex: Jean Dupont"
                            style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        @error('formName') <div style="font-size: 12px; color: #DC2626; font-weight: 500; margin-top: 4px;">{{ $message }}</div> @enderror
                    </div>

                    {{-- Email --}}
                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Adresse e-mail *</label>
                        <input type="email" wire:model="formEmail" placeholder="jean.dupont@miltex.cd"
                            style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        @error('formEmail') <div style="font-size: 12px; color: #DC2626; font-weight: 500; margin-top: 4px;">{{ $message }}</div> @enderror
                    </div>

                    {{-- Role --}}
                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Rôle & Permissions *</label>
                        <select wire:model.live="formRole"
                            style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                            @if(auth()->user()->isSuperAdmin())
                                <option value="super_admin">Super Administrateur</option>
                            @endif
                            <option value="admin">Administrateur</option>
                            <option value="analyst">Analyste / Auditeur</option>
                        </select>
                        @error('formRole') <div style="font-size: 12px; color: #DC2626; font-weight: 500; margin-top: 4px;">{{ $message }}</div> @enderror
                    </div>

                    {{-- Password --}}
                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">
                            Mot de passe {{ $isEditing ? '(laisser vide pour ne pas modifier)' : '*' }}
                        </label>
                        <input type="password" wire:model="formPassword" placeholder="••••••••••••"
                            style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none; margin-bottom: 8px;">
                        <input type="password" wire:model="formPasswordConfirmation" placeholder="Confirmer le mot de passe"
                            style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        @error('formPassword') <div style="font-size: 12px; color: #DC2626; font-weight: 500; margin-top: 4px;">{{ $message }}</div> @enderror
                    </div>

                    {{-- Active toggle --}}
                    <div style="display: flex; align-items: center; gap: 9px; margin-bottom: 24px;">
                        <input type="checkbox" wire:model="formIsActive" id="formIsActive" style="width: 16px; height: 16px; accent-color: var(--primary);">
                        <label for="formIsActive" style="font-size: 13px; color: var(--text-secondary); cursor: pointer; user-select: none;">Compte actif et autorisé à se connecter</label>
                    </div>

                    {{-- Actions --}}
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Enregistrer les modifications' : 'Créer l\'utilisateur' }}</span>
                            <span wire:loading wire:target="save">Enregistrement…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══ Delete Confirm Modal ══ --}}
    @if($showDeleteConfirm)
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); z-index: 70; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div class="card animate-slide-up" style="width: 100%; max-width: 400px; text-align: center; border-color: #FECACA; padding: 32px;">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px;">
                    <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h2 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-main);">Confirmer la suppression</h2>
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 24px; line-height: 1.5;">Cette action est définitive. Le compte de cet utilisateur sera définitivement supprimé du système.</p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button wire:click="$set('showDeleteConfirm', false)" class="btn btn-ghost">
                        Annuler
                    </button>
                    <button wire:click="deleteUser" class="btn btn-danger">
                        Supprimer l'utilisateur
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
