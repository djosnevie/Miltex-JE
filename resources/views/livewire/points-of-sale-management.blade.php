@section('title', 'Points de Vente')

<div>
    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 24px; background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 14px 18px; border-radius: 10px; display: flex; align-items: center; gap: 12px; font-size: 13.5px; font-weight: 500;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Toolbar & Quota Status --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px;">
                <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Rechercher un point de vente</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <svg style="position: absolute; left: 12px; color: var(--text-muted); width: 16px; height: 16px; pointer-events: none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nom du magasin, ville, code identifiant..."
                        style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 9px 12px 9px 36px; border-radius: 9px; font-size: 13px; outline: none;">
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 14px; margin-top: 22px; flex-wrap: wrap;">
                {{-- Quota Badge / Indicator --}}
                <div style="display: flex; align-items: center; gap: 10px; background: #F8FAFC; border: 1px solid var(--border-subtle); padding: 7px 14px; border-radius: 9px;">
                    <div style="text-align: right;">
                        <div style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em;">Quota Points de Vente</div>
                        <div style="font-size: 12.5px; font-weight: 700; color: {{ $isQuotaReached ? '#DC2626' : 'var(--text-main)' }};">
                            {{ $currentPosCount }} / {{ $maxPosAllowed ?? 'Illimité' }}
                            @if($maxPosAllowed)
                                <span style="font-size: 11px; font-weight: 600; color: {{ $isQuotaReached ? '#DC2626' : '#059669' }};">({{ $quotaPercentage }}%)</span>
                            @endif
                        </div>
                    </div>
                    @if($maxPosAllowed)
                        <div style="width: 44px; height: 6px; background: #E2E8F0; border-radius: 9999px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $quotaPercentage }}%; background: {{ $isQuotaReached ? '#DC2626' : ($quotaPercentage > 80 ? '#D97706' : '#2563EB') }}; border-radius: 9999px;"></div>
                        </div>
                    @endif
                </div>

                @if($isQuotaReached && !auth()->user()->isSuperAdmin())
                    <button class="btn btn-secondary" disabled title="Quota maximum atteint. Contactez le SuperAdmin pour augmenter votre capacité." style="opacity: 0.65; cursor: not-allowed; display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Quota Atteint</span>
                    </button>
                @else
                    <button wire:click="openCreatePos" class="btn btn-primary">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Nouveau Point de Vente</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Summary Stats KPIs --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; margin-bottom: 24px;">
        @php
            $totalDef = \App\Models\Device::count();
            $totalJournaux = \App\Models\Journal::count();
        @endphp
        <div class="kpi-card blue">
            <div class="kpi-icon-wrap blue">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="kpi-value">{{ $pointsOfSale->count() }}</div>
            <div class="kpi-label">Points de Vente Enregistrés</div>
            <div class="kpi-sub">
                <span style="color: #059669; font-weight: 600;">{{ $pointsOfSale->where('is_active', true)->count() }}</span> actifs en exploitation
            </div>
        </div>

        <div class="kpi-card green">
            <div class="kpi-icon-wrap green">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                </svg>
            </div>
            <div class="kpi-value">{{ $totalDef }}</div>
            <div class="kpi-label">Dispositifs DEF Associés</div>
            <div class="kpi-sub">
                <span style="color: #059669; font-weight: 600;">{{ \App\Models\Device::where('status','active')->count() }}</span> dispositifs opérationnels
            </div>
        </div>

        <div class="kpi-card yellow">
            <div class="kpi-icon-wrap yellow">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="kpi-value">{{ $totalJournaux }}</div>
            <div class="kpi-label">Journaux Électroniques Fiscaux</div>
            <div class="kpi-sub">Consolidé total des imports</div>
        </div>
    </div>

    {{-- POS Cards List --}}
    @forelse($pointsOfSale as $pos)
        @php
            $isExpanded = in_array($pos->id, $expandedPos);
            $devices = $pos->devices->sortBy('nid');
            $jCount = $pos->devices->sum(fn($d) => $d->journals->count());
        @endphp
        <div class="card" style="margin-bottom: 16px; padding: 0; overflow: hidden;">

            {{-- POS Header Row --}}
            <div style="padding: 20px 24px; display: flex; align-items: center; gap: 16px; cursor: pointer; transition: background 0.15s ease;"
                 wire:click="toggleExpand({{ $pos->id }})"
                 onmouseover="this.style.background='#F8FAFC'"
                 onmouseout="this.style.background='#FFFFFF'">

                {{-- Expand icon --}}
                <div style="color: var(--text-muted); transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1); transform: rotate({{ $isExpanded ? '90' : '0' }}deg);">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>

                {{-- Status Dot --}}
                <div style="width: 9px; height: 9px; border-radius: 50%; background: {{ $pos->is_active ? '#10B981' : '#EF4444' }}; box-shadow: 0 0 8px {{ $pos->is_active ? '#10B981' : '#EF4444' }}; flex-shrink: 0;"></div>

                {{-- Name & Details --}}
                <div style="flex: 1;">
                    <div style="font-size: 15.5px; font-weight: 700; color: var(--text-main); letter-spacing: -0.01em;">{{ $pos->name }}</div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 3px; display: flex; align-items: center; gap: 6px;">
                        @if($pos->location_identifier)
                            <span style="font-family: monospace; color: var(--primary); font-weight: 600;">{{ $pos->location_identifier }}</span>
                            <span>·</span>
                        @endif
                        <span>{{ $pos->city ?? 'Ville non renseignée' }}</span>
                        @if($pos->address)
                            <span>·</span>
                            <span>{{ $pos->address }}</span>
                        @endif
                    </div>
                </div>

                {{-- Counter Pills --}}
                <div style="display: flex; gap: 8px; align-items: center;">
                    <span class="badge badge-credit">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $devices->count() }} DEF</span>
                    </span>
                    <span class="badge badge-sale">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>{{ $jCount }} journaux</span>
                    </span>
                </div>

                {{-- Actions --}}
                <div style="display: flex; gap: 8px; align-items: center;" wire:click.stop>
                    <button wire:click="openCreateDef({{ $pos->id }})"
                        class="btn btn-ghost btn-sm"
                        title="Ajouter un matériel DEF"
                        style="color: #059669; border-color: #A7F3D0; background: #ECFDF5;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>DEF</span>
                    </button>
                    <button wire:click="openEditPos({{ $pos->id }})"
                        class="btn btn-ghost btn-sm btn-icon"
                        title="Modifier le point de vente">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>
                    <button wire:click="togglePosActive({{ $pos->id }})"
                        class="btn btn-ghost btn-sm btn-icon"
                        title="{{ $pos->is_active ? 'Désactiver' : 'Activer' }}"
                        style="color: {{ $pos->is_active ? '#D97706' : '#059669' }};">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            @if($pos->is_active)
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @endif
                        </svg>
                    </button>
                    <button wire:click="confirmDelete({{ $pos->id }}, 'pos')"
                        class="btn btn-ghost btn-sm btn-icon"
                        title="Supprimer"
                        style="color: #DC2626;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- DEF Table (Expandable) --}}
            @if($isExpanded)
                <div style="border-top: 1px solid var(--border-subtle); background: #F8FAFC;">
                    @if($devices->isEmpty())
                        <div style="padding: 28px 24px; text-align: center; color: var(--text-muted); font-size: 13px;">
                            <span>Aucun matériel DEF enregistré pour ce point de vente.</span>
                            <button wire:click="openCreateDef({{ $pos->id }})" class="btn btn-ghost btn-sm" style="margin-left: 10px; color: var(--primary);">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>Ajouter un DEF</span>
                            </button>
                        </div>
                    @else
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th style="padding-left: 24px;">Identifiant DEF (NID)</th>
                                        <th>ISF</th>
                                        <th>Modèle</th>
                                        <th>N° Série</th>
                                        <th style="text-align: center;">Statut</th>
                                        <th style="text-align: center;">Journaux</th>
                                        <th style="text-align: right; padding-right: 24px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($devices as $dev)
                                        <tr>
                                            <td style="padding-left: 24px;">
                                                <div style="font-weight: 700; font-family: monospace; color: var(--primary); font-size: 13px;">{{ $dev->nid }}</div>
                                                @if($dev->description)
                                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">{{ $dev->description }}</div>
                                                @endif
                                            </td>
                                            <td style="font-size: 12.5px; color: var(--text-secondary); font-family: monospace;">{{ $dev->isf ?? '—' }}</td>
                                            <td style="font-size: 13px; color: var(--text-main);">{{ $dev->model ?? '—' }}</td>
                                            <td style="font-size: 12px; color: var(--text-muted); font-family: monospace;">{{ $dev->serial_number ?? '—' }}</td>
                                            <td style="text-align: center;">
                                                @php
                                                    $dBadge = match($dev->status) {
                                                        'active'      => 'badge-sale',
                                                        'maintenance' => 'badge-warning',
                                                        default       => 'badge-cancelled',
                                                    };
                                                    $dLabel = match($dev->status) {
                                                        'active'      => 'Actif',
                                                        'maintenance' => 'Maintenance',
                                                        default       => 'Inactif',
                                                    };
                                                @endphp
                                                <span class="badge {{ $dBadge }}">
                                                    <span class="status-dot"></span>
                                                    {{ $dLabel }}
                                                </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="badge badge-neutral tabular-nums">
                                                    {{ $dev->journals->count() }}
                                                </span>
                                            </td>
                                            <td style="text-align: right; padding-right: 24px;">
                                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                                    <button wire:click="openEditDef({{ $dev->id }})"
                                                        class="btn btn-ghost btn-sm btn-icon"
                                                        title="Modifier le DEF">
                                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </button>
                                                    <button wire:click="toggleDefStatus({{ $dev->id }})"
                                                        class="btn btn-ghost btn-sm btn-icon"
                                                        title="{{ $dev->status === 'active' ? 'Mettre en maintenance' : 'Activer' }}"
                                                        style="color: {{ $dev->status === 'active' ? '#D97706' : '#059669' }};">
                                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            @if($dev->status === 'active')
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            @else
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                            @endif
                                                        </svg>
                                                    </button>
                                                    <button wire:click="confirmDelete({{ $dev->id }}, 'def')"
                                                        class="btn btn-ghost btn-sm btn-icon"
                                                        title="Supprimer le DEF"
                                                        style="color: #DC2626;">
                                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @empty
        <div class="card" style="text-align: center; padding: 60px 20px;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(59, 130, 246, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">Aucun point de vente répertorié</div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 22px;">Créez votre premier point de vente pour associer vos dispositifs DEF et débuter les audits.</div>
            <button wire:click="openCreatePos" class="btn btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Créer un Point de Vente</span>
            </button>
        </div>
    @endforelse

    {{-- ══ POS Modal ══ --}}
    @if($showPosModal)
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); z-index: 60; display: flex; align-items: center; justify-content: center; padding: 20px;" wire:click.self="$set('showPosModal', false)">
            <div class="card animate-slide-up" style="width: 100%; max-width: 540px; max-height: 90vh; overflow-y: auto; padding: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 16px;">
                    <h2 style="font-size: 17px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>{{ $posIsEditing ? 'Modifier le Point de Vente' : 'Nouveau Point de Vente' }}</span>
                    </h2>
                    <button wire:click="$set('showPosModal', false)" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; display: flex; align-items: center;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form wire:submit="savePos">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div style="grid-column: 1 / -1;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Nom du Point de Vente *</label>
                            <input type="text" wire:model="posName" placeholder="Ex: Restaurant Central Gombe"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                            @error('posName') <div style="font-size: 12px; color: #DC2626; font-weight: 500; margin-top: 4px;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Code Identifiant Lieu</label>
                            <input type="text" wire:model="posLocationId" placeholder="Ex: DEP-01"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Ville</label>
                            <input type="text" wire:model="posCity" placeholder="Kinshasa"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Adresse Complète</label>
                            <input type="text" wire:model="posAddress" placeholder="Ex: 12 Avenue des Aviateurs, Gombe"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Téléphone</label>
                            <input type="text" wire:model="posPhone" placeholder="+243 81 000 0000"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Adresse E-mail</label>
                            <input type="email" wire:model="posEmail" placeholder="pdv@miltex.cd"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Description / Notes</label>
                            <textarea wire:model="posDescription" rows="2" placeholder="Informations complémentaires..."
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none; resize: vertical;"></textarea>
                        </div>
                        <div style="grid-column: 1 / -1; display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" wire:model="posIsActive" id="posIsActive" style="width: 16px; height: 16px; accent-color: var(--primary);">
                            <label for="posIsActive" style="font-size: 13px; color: var(--text-secondary); cursor: pointer; user-select: none;">Point de vente actif et en service</label>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 28px;">
                        <button type="button" wire:click="$set('showPosModal', false)" class="btn btn-ghost">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="savePos">{{ $posIsEditing ? 'Enregistrer les modifications' : 'Créer le Point de Vente' }}</span>
                            <span wire:loading wire:target="savePos">Enregistrement…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══ DEF Modal ══ --}}
    @if($showDefModal)
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); z-index: 60; display: flex; align-items: center; justify-content: center; padding: 20px;" wire:click.self="$set('showDefModal', false)">
            <div class="card animate-slide-up" style="width: 100%; max-width: 480px; padding: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 16px;">
                    <h2 style="font-size: 17px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $defIsEditing ? 'Modifier le Dispositif DEF' : 'Nouveau Dispositif DEF' }}</span>
                    </h2>
                    <button wire:click="$set('showDefModal', false)" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; display: flex; align-items: center;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form wire:submit="saveDef">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">NID DEF *</label>
                            <input type="text" wire:model="defNid" placeholder="IC02000193-1"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; font-family: monospace; outline: none;">
                            @error('defNid') <div style="font-size: 12px; color: #DC2626; font-weight: 500; margin-top: 4px;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">ISF</label>
                            <input type="text" wire:model="defIsf" placeholder="102030405"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Modèle Matériel</label>
                            <input type="text" wire:model="defModel" placeholder="Incotex 133"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">N° de Série</label>
                            <input type="text" wire:model="defSerialNumber" placeholder="SN-XXXX-XXXX"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Version Firmware</label>
                            <input type="text" wire:model="defFirmware" placeholder="1.2.0"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Statut Opérationnel *</label>
                            <select wire:model="defStatus"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                                <option value="active">Actif</option>
                                <option value="inactive">Inactif</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 7px; letter-spacing: 0.05em;">Emplacement / Description</label>
                            <input type="text" wire:model="defDescription" placeholder="Ex: Caisse principale n°1, rez-de-chaussée"
                                style="width: 100%; background: var(--surface-input); border: 1px solid var(--border-subtle); color: var(--text-main); padding: 10px 14px; border-radius: 9px; font-size: 13.5px; outline: none;">
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 28px;">
                        <button type="button" wire:click="$set('showDefModal', false)" class="btn btn-ghost">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveDef">{{ $defIsEditing ? 'Enregistrer' : 'Ajouter le DEF' }}</span>
                            <span wire:loading wire:target="saveDef">Enregistrement…</span>
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
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; line-height: 1.5;">
                    @if($deleteType === 'pos')
                        Supprimer ce point de vente supprimera également l'association de tous ses matériels DEF.
                    @else
                        La suppression de ce dispositif fiscal DEF est définitive.
                    @endif
                </p>
                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 24px;">Les journaux et transactions comptables déjà importés restent conservés pour les audits.</p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button wire:click="$set('showDeleteConfirm', false)" class="btn btn-ghost">
                        Annuler
                    </button>
                    <button wire:click="doDelete" class="btn btn-danger">
                        Confirmer la suppression
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
