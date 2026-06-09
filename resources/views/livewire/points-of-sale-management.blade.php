@section('title', 'Points de Vente')

<div>
    {{-- Flash --}}
    @if(session('success'))
        <div style="background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.3);border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#34D399;font-size:13px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="card" style="margin-bottom:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Recherche</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nom, ville, identifiant..."
                    style="width:100%;background:var(--surface-3);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;font-size:13px;outline:none;">
            </div>
            <div style="margin-top:20px;">
                <button wire:click="openCreatePos" class="btn btn-primary">＋ Nouveau Point de Vente</button>
            </div>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
        @php
            $allPos = \App\Models\PointOfSale::withCount('devices')->get();
            $totalDef = \App\Models\Device::count();
            $totalJournaux = \App\Models\Journal::count();
        @endphp
        <div class="kpi-card blue">
            <div class="kpi-icon blue"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:22px;height:22px;color:#60A5FA"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
            <div class="kpi-value">{{ $pointsOfSale->count() }}</div>
            <div class="kpi-label">Points de Vente</div>
            <div class="kpi-sub">{{ $pointsOfSale->where('is_active', true)->count() }} actifs</div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-icon green"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:22px;height:22px;color:#34D399"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg></div>
            <div class="kpi-value">{{ $totalDef }}</div>
            <div class="kpi-label">Appareils DEF</div>
            <div class="kpi-sub">{{ \App\Models\Device::where('status','active')->count() }} actifs</div>
        </div>
        <div class="kpi-card yellow">
            <div class="kpi-icon yellow"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:22px;height:22px;color:#FCD34D"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <div class="kpi-value">{{ $totalJournaux }}</div>
            <div class="kpi-label">Journaux importés</div>
            <div class="kpi-sub">Total tous PDV</div>
        </div>
    </div>

    {{-- POS Cards --}}
    @forelse($pointsOfSale as $pos)
        @php
            $isExpanded = in_array($pos->id, $expandedPos);
            $devices = $pos->devices->sortBy('nid');
            $jCount = $pos->devices->sum(fn($d) => $d->journals->count());
        @endphp
        <div class="card" style="margin-bottom:16px;padding:0;overflow:hidden;">

            {{-- POS Header --}}
            <div style="padding:20px 24px;display:flex;align-items:center;gap:16px;cursor:pointer;transition:background 0.2s;"
                 wire:click="toggleExpand({{ $pos->id }})"
                 onmouseover="this.style.background='rgba(255,255,255,0.03)'"
                 onmouseout="this.style.background='transparent'">

                {{-- Expand icon --}}
                <div style="color:var(--muted);transition:transform 0.2s;transform:rotate({{ $isExpanded ? '90' : '0' }}deg);">▶</div>

                {{-- POS status dot --}}
                <div style="width:10px;height:10px;border-radius:50%;background:{{ $pos->is_active ? '#10B981' : '#EF4444' }};flex-shrink:0;"></div>

                {{-- Name + location --}}
                <div style="flex:1;">
                    <div style="font-size:15px;font-weight:700;color:var(--text);">{{ $pos->name }}</div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                        @if($pos->location_identifier)📍 {{ $pos->location_identifier }} — @endif
                        {{ $pos->city ?? 'Ville non renseignée' }}
                        @if($pos->address) · {{ $pos->address }}@endif
                    </div>
                </div>

                {{-- Stats pills --}}
                <div style="display:flex;gap:8px;align-items:center;">
                    <span style="background:rgba(59,130,246,.1);color:#60A5FA;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                        🖥 {{ $devices->count() }} DEF
                    </span>
                    <span style="background:rgba(16,185,129,.1);color:#34D399;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                        📋 {{ $jCount }} journaux
                    </span>
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:6px;" wire:click.stop>
                    <button wire:click="openCreateDef({{ $pos->id }})"
                        title="Ajouter un DEF"
                        style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);color:#34D399;padding:6px 12px;border-radius:6px;font-size:12px;cursor:pointer;">
                        ＋ DEF
                    </button>
                    <button wire:click="openEditPos({{ $pos->id }})"
                        title="Modifier le PDV"
                        style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);color:#60A5FA;padding:6px 10px;border-radius:6px;font-size:12px;cursor:pointer;">
                        ✏️
                    </button>
                    <button wire:click="togglePosActive({{ $pos->id }})"
                        title="{{ $pos->is_active ? 'Désactiver' : 'Activer' }}"
                        style="background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);color:#FCD34D;padding:6px 10px;border-radius:6px;font-size:12px;cursor:pointer;">
                        {{ $pos->is_active ? '🔒' : '✅' }}
                    </button>
                    <button wire:click="confirmDelete({{ $pos->id }}, 'pos')"
                        title="Supprimer"
                        style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#FCA5A5;padding:6px 10px;border-radius:6px;font-size:12px;cursor:pointer;">
                        🗑️
                    </button>
                </div>
            </div>

            {{-- DEF Table (expandable) --}}
            @if($isExpanded)
                <div style="border-top:1px solid var(--border);background:rgba(0,0,0,0.15);">
                    @if($devices->isEmpty())
                        <div style="padding:24px;text-align:center;color:var(--muted);font-size:13px;">
                            Aucun appareil DEF enregistré pour ce PDV.
                            <button wire:click="openCreateDef({{ $pos->id }})" style="background:none;border:none;color:var(--accent);cursor:pointer;font-size:13px;margin-left:4px;">＋ Ajouter un DEF</button>
                        </div>
                    @else
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr style="background:rgba(255,255,255,0.02);">
                                    <th style="padding:10px 24px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;text-align:left;">NID</th>
                                    <th style="padding:10px 14px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:left;">ISF</th>
                                    <th style="padding:10px 14px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:left;">Modèle</th>
                                    <th style="padding:10px 14px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:left;">N° Série</th>
                                    <th style="padding:10px 14px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:center;">Statut</th>
                                    <th style="padding:10px 14px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:center;">Journaux</th>
                                    <th style="padding:10px 14px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($devices as $dev)
                                    <tr style="border-top:1px solid rgba(255,255,255,0.04);">
                                        <td style="padding:12px 24px;">
                                            <div style="font-weight:700;font-family:monospace;color:var(--accent);font-size:13px;">{{ $dev->nid }}</div>
                                            @if($dev->description)
                                                <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $dev->description }}</div>
                                            @endif
                                        </td>
                                        <td style="padding:12px 14px;font-size:12px;color:var(--muted);font-family:monospace;">{{ $dev->isf ?? '—' }}</td>
                                        <td style="padding:12px 14px;font-size:13px;">{{ $dev->model ?? '—' }}</td>
                                        <td style="padding:12px 14px;font-size:12px;color:var(--muted);">{{ $dev->serial_number ?? '—' }}</td>
                                        <td style="padding:12px 14px;text-align:center;">
                                            <span style="color:{{ $dev->statusColor() }};font-size:12px;font-weight:600;">{{ $dev->statusBadge() }}</span>
                                        </td>
                                        <td style="padding:12px 14px;text-align:center;">
                                            <span style="background:rgba(59,130,246,.1);color:#60A5FA;padding:3px 10px;border-radius:20px;font-size:12px;">
                                                {{ $dev->journals->count() }}
                                            </span>
                                        </td>
                                        <td style="padding:12px 14px;text-align:center;">
                                            <div style="display:flex;gap:5px;justify-content:center;">
                                                <button wire:click="openEditDef({{ $dev->id }})"
                                                    style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);color:#60A5FA;padding:4px 8px;border-radius:5px;font-size:11px;cursor:pointer;">
                                                    ✏️
                                                </button>
                                                <button wire:click="toggleDefStatus({{ $dev->id }})"
                                                    style="background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);color:#FCD34D;padding:4px 8px;border-radius:5px;font-size:11px;cursor:pointer;">
                                                    {{ $dev->status === 'active' ? '⏸' : '▶' }}
                                                </button>
                                                <button wire:click="confirmDelete({{ $dev->id }}, 'def')"
                                                    style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#FCA5A5;padding:4px 8px;border-radius:5px;font-size:11px;cursor:pointer;">
                                                    🗑️
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endif
        </div>
    @empty
        <div class="card" style="text-align:center;padding:60px 20px;color:var(--muted);">
            <div style="font-size:40px;margin-bottom:16px;">🏪</div>
            <div style="font-size:16px;font-weight:600;color:var(--text);margin-bottom:8px;">Aucun point de vente</div>
            <div style="font-size:13px;margin-bottom:20px;">Créez votre premier PDV pour commencer.</div>
            <button wire:click="openCreatePos" class="btn btn-primary">＋ Créer un Point de Vente</button>
        </div>
    @endforelse

    {{-- ══ POS Modal ══ --}}
    @if($showPosModal)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);z-index:50;display:flex;align-items:center;justify-content:center;padding:20px;" wire:click.self="$set('showPosModal',false)">
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:32px;width:100%;max-width:540px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 60px rgba(0,0,0,0.5);animation:slideUp 0.25s ease both;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                    <h2 style="font-size:18px;font-weight:700;">{{ $posIsEditing ? '✏️ Modifier le PDV' : '🏪 Nouveau Point de Vente' }}</h2>
                    <button wire:click="$set('showPosModal',false)" style="background:none;border:none;color:var(--muted);font-size:20px;cursor:pointer;">✕</button>
                </div>
                <form wire:submit="savePos">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Nom du PDV *</label>
                            <input type="text" wire:model="posName" placeholder="Restaurant Leon"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                            @error('posName') <div style="font-size:12px;color:#FCA5A5;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Identifiant lieu</label>
                            <input type="text" wire:model="posLocationId" placeholder="DEP-01"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Ville</label>
                            <input type="text" wire:model="posCity" placeholder="Kinshasa"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        </div>
                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Adresse</label>
                            <input type="text" wire:model="posAddress" placeholder="12 Avenue Luambo Makiadi, Gombe"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Téléphone</label>
                            <input type="text" wire:model="posPhone" placeholder="+243 81 000 0000"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Email</label>
                            <input type="email" wire:model="posEmail" placeholder="pdv@miltex.cd"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        </div>
                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Description</label>
                            <textarea wire:model="posDescription" rows="2" placeholder="Notes sur ce point de vente..."
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;resize:vertical;"></textarea>
                        </div>
                        <div style="grid-column:1/-1;display:flex;align-items:center;gap:8px;">
                            <input type="checkbox" wire:model="posIsActive" id="posIsActive" style="width:16px;height:16px;accent-color:var(--accent);">
                            <label for="posIsActive" style="font-size:13px;color:var(--muted);cursor:pointer;">PDV actif</label>
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:24px;">
                        <button type="button" wire:click="$set('showPosModal',false)"
                            style="background:var(--surface-3);border:1px solid var(--border);color:var(--muted);padding:10px 20px;border-radius:8px;font-size:13px;cursor:pointer;">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="savePos">{{ $posIsEditing ? 'Enregistrer' : 'Créer' }}</span>
                            <span wire:loading wire:target="savePos">Enregistrement…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══ DEF Modal ══ --}}
    @if($showDefModal)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);z-index:50;display:flex;align-items:center;justify-content:center;padding:20px;" wire:click.self="$set('showDefModal',false)">
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:16px;padding:32px;width:100%;max-width:480px;box-shadow:0 25px 60px rgba(0,0,0,0.5);animation:slideUp 0.25s ease both;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                    <h2 style="font-size:18px;font-weight:700;">{{ $defIsEditing ? '✏️ Modifier le DEF' : '🖥 Nouvel Appareil DEF' }}</h2>
                    <button wire:click="$set('showDefModal',false)" style="background:none;border:none;color:var(--muted);font-size:20px;cursor:pointer;">✕</button>
                </div>
                <form wire:submit="saveDef">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">NID DEF *</label>
                            <input type="text" wire:model="defNid" placeholder="IC02000193-1"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;font-family:monospace;outline:none;">
                            @error('defNid') <div style="font-size:12px;color:#FCA5A5;margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">ISF</label>
                            <input type="text" wire:model="defIsf" placeholder="102030405"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Modèle</label>
                            <input type="text" wire:model="defModel" placeholder="Incotex 133"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">N° Série</label>
                            <input type="text" wire:model="defSerialNumber" placeholder="SN-XXXX-XXXX"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Firmware</label>
                            <input type="text" wire:model="defFirmware" placeholder="1.2.0"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Statut *</label>
                            <select wire:model="defStatus"
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                                <option value="active">🟢 Actif</option>
                                <option value="inactive">🔴 Inactif</option>
                                <option value="maintenance">🟡 Maintenance</option>
                            </select>
                        </div>
                        <div style="grid-column:1/-1;">
                            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;margin-bottom:6px;">Description</label>
                            <input type="text" wire:model="defDescription" placeholder="Caisse principale, rez-de-chaussée..."
                                style="width:100%;background:var(--surface-2);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px;outline:none;">
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:24px;">
                        <button type="button" wire:click="$set('showDefModal',false)"
                            style="background:var(--surface-3);border:1px solid var(--border);color:var(--muted);padding:10px 20px;border-radius:8px;font-size:13px;cursor:pointer;">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveDef">{{ $defIsEditing ? 'Enregistrer' : 'Ajouter' }}</span>
                            <span wire:loading wire:target="saveDef">Enregistrement…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══ Delete Confirm ══ --}}
    @if($showDeleteConfirm)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);z-index:50;display:flex;align-items:center;justify-content:center;padding:20px;">
            <div style="background:var(--surface);border:1px solid rgba(239,68,68,0.3);border-radius:16px;padding:32px;width:100%;max-width:380px;text-align:center;">
                <div style="font-size:40px;margin-bottom:16px;">⚠️</div>
                <h2 style="font-size:18px;font-weight:700;margin-bottom:8px;">Confirmer la suppression</h2>
                <p style="font-size:13px;color:var(--muted);margin-bottom:6px;">
                    @if($deleteType === 'pos')
                        Supprimer ce point de vente supprimera aussi tous ses DEF associés.
                    @else
                        Supprimer ce DEF est irréversible.
                    @endif
                </p>
                <p style="font-size:12px;color:var(--muted);margin-bottom:24px;">Les journaux et factures déjà importés ne seront pas supprimés.</p>
                <div style="display:flex;gap:10px;justify-content:center;">
                    <button wire:click="$set('showDeleteConfirm',false)"
                        style="background:var(--surface-3);border:1px solid var(--border);color:var(--muted);padding:10px 20px;border-radius:8px;font-size:13px;cursor:pointer;">
                        Annuler
                    </button>
                    <button wire:click="doDelete"
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
