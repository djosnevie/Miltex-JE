@section('title', 'Gestion des Utilisateurs')

<div>
    {{-- Flash messages --}}
    @if(session('success'))
        <div style="background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.3);border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#34D399;font-size:13px;display:flex;align-items:center;gap:8px;">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#FCA5A5;font-size:13px;display:flex;align-items:center;gap:8px;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Header bar --}}
    <div class="card" style="margin-bottom:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
            <div style="display:flex;gap:12px;flex:1;flex-wrap:wrap;">
                <div style="min-width:200px;flex:1;">
                    <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;text-transform:uppercase;">Recherche</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nom, email..."
                        style="width:100%;background:var(--surface-3);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;font-size:13px;outline:none;">
                </div>
                <div style="width:160px;">
                    <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;text-transform:uppercase;">Rôle</label>
                    <select wire:model.live="roleFilter" style="width:100%;background:var(--surface-3);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;font-size:13px;outline:none;">
                        <option value="">Tous les rôles</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="analyst">Analyste</option>
                    </select>
                </div>
            </div>
            <button wire:click="openCreate" class="btn btn-primary" style="margin-top:20px;">
                ＋ Nouvel Utilisateur
            </button>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="card">
        <div style="padding:12px 20px;border-bottom:1px solid var(--border);font-size:13px;color:var(--muted);">
            <strong style="color:var(--text);">{{ $users->total() }}</strong> utilisateur(s)
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Tenant</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                    <div style="font-size:10px;color:var(--accent);">← Vous</div>
                                @endif
                            </td>
                            <td style="color:var(--muted);font-size:13px;">{{ $user->email }}</td>
                            <td>
                                <span class="badge" style="background:rgba(255,255,255,0.05);color:{{ $user->roleColor() }};">
                                    {{ $user->roleBadge() }}
                                </span>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge badge-sale">Actif</span>
                                @else
                                    <span class="badge badge-cancelled">Inactif</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--muted);">
                                {{ $user->tenant?->name ?? '—' }}
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;justify-content:center;">
                                    <button wire:click="openEdit({{ $user->id }})"
                                        title="Modifier"
                                        style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);color:#60A5FA;padding:5px 10px;border-radius:6px;font-size:12px;cursor:pointer;transition:all 0.2s;"
                                        onmouseover="this.style.background='rgba(59,130,246,.2)'"
                                        onmouseout="this.style.background='rgba(59,130,246,.1)'">
                                        ✏️ Modifier
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button wire:click="toggleActive({{ $user->id }})"
                                            title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}"
                                            style="background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);color:#FCD34D;padding:5px 10px;border-radius:6px;font-size:12px;cursor:pointer;transition:all 0.2s;"
                                            onmouseover="this.style.background='rgba(245,158,11,.2)'"
                                            onmouseout="this.style.background='rgba(245,158,11,.1)'">
                                            {{ $user->is_active ? '🔒 Désactiver' : '✅ Activer' }}
                                        </button>
                                        <button wire:click="confirmDelete({{ $user->id }})"
                                            title="Supprimer"
                                            style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#FCA5A5;padding:5px 10px;border-radius:6px;font-size:12px;cursor:pointer;transition:all 0.2s;"
                                            onmouseover="this.style.background='rgba(239,68,68,.2)'"
                                            onmouseout="this.style.background='rgba(239,68,68,.1)'">
                                            🗑️
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:40px 0;color:var(--muted);">
                                Aucun utilisateur trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div style="margin-top:20px;padding:0 20px 20px;">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- ══ Create / Edit Modal ══ --}}
    @if($showModal)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);z-index:50;display:flex;align-items:center;justify-content:center;padding:20px;" wire:click.self="$set('showModal', false)">
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:32px;width:100%;max-width:480px;box-shadow:0 25px 60px rgba(0,0,0,0.5);animation:slideUp 0.25s ease both;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                    <h2 style="font-size:18px;font-weight:700;">
                        {{ $isEditing ? '✏️ Modifier l\'utilisateur' : '＋ Nouvel utilisateur' }}
                    </h2>
                    <button wire:click="$set('showModal', false)" style="background:none;border:none;color:var(--muted);font-size:20px;cursor:pointer;padding:4px 8px;border-radius:6px;" onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">✕</button>
                </div>

                <form wire:submit="save">
                    {{-- Name --}}
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Nom complet</label>
                        <input type="text" wire:model="formName" placeholder="Jean Dupont"
                            style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        @error('formName') <div style="font-size:12px;color:#FCA5A5;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    {{-- Email --}}
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Adresse e-mail</label>
                        <input type="email" wire:model="formEmail" placeholder="jean@miltex.cd"
                            style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        @error('formEmail') <div style="font-size:12px;color:#FCA5A5;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    {{-- Role --}}
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Rôle</label>
                        <select wire:model.live="formRole"
                            style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                            @if(auth()->user()->isSuperAdmin())
                                <option value="super_admin">⭐ Super Admin</option>
                            @endif
                            <option value="admin">🔧 Admin</option>
                            <option value="analyst">👁 Analyste</option>
                        </select>
                        @error('formRole') <div style="font-size:12px;color:#FCA5A5;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    {{-- Password --}}
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">
                            Mot de passe {{ $isEditing ? '(laisser vide pour ne pas changer)' : '' }}
                        </label>
                        <input type="password" wire:model="formPassword" placeholder="••••••••"
                            style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;margin-bottom:8px;">
                        <input type="password" wire:model="formPasswordConfirmation" placeholder="Confirmer le mot de passe"
                            style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        @error('formPassword') <div style="font-size:12px;color:#FCA5A5;margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    {{-- Active toggle --}}
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:24px;">
                        <input type="checkbox" wire:model="formIsActive" id="formIsActive" style="width:16px;height:16px;accent-color:var(--accent);">
                        <label for="formIsActive" style="font-size:13px;color:var(--muted);cursor:pointer;">Compte actif</label>
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" wire:click="$set('showModal', false)"
                            style="background:var(--surface-3);border:1px solid var(--border);color:var(--muted);padding:10px 20px;border-radius:8px;font-size:13px;cursor:pointer;">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Enregistrer' : 'Créer' }}</span>
                            <span wire:loading wire:target="save">Enregistrement…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══ Delete Confirm Modal ══ --}}
    @if($showDeleteConfirm)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);z-index:50;display:flex;align-items:center;justify-content:center;padding:20px;">
            <div style="background:var(--surface);border:1px solid rgba(239,68,68,0.3);border-radius:16px;padding:32px;width:100%;max-width:380px;text-align:center;">
                <div style="font-size:40px;margin-bottom:16px;">⚠️</div>
                <h2 style="font-size:18px;font-weight:700;margin-bottom:8px;">Confirmer la suppression</h2>
                <p style="font-size:13px;color:var(--muted);margin-bottom:24px;">Cette action est irréversible. L'utilisateur sera définitivement supprimé.</p>
                <div style="display:flex;gap:10px;justify-content:center;">
                    <button wire:click="$set('showDeleteConfirm', false)"
                        style="background:var(--surface-3);border:1px solid var(--border);color:var(--muted);padding:10px 20px;border-radius:8px;font-size:13px;cursor:pointer;">
                        Annuler
                    </button>
                    <button wire:click="deleteUser"
                        style="background:rgba(239,68,68,.8);border:none;color:white;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
@keyframes slideUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>
