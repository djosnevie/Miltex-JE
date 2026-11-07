@section('title', 'Espace SuperAdmin — Quotas & Utilisateurs')

<div>
    {{-- Header with SuperAdmin Badge --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <h1 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.02em;">
                    Espace SuperAdmin Plateforme
                </h1>
                <span style="background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px; letter-spacing: 0.04em;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #D97706;"></span>
                    SUPERADMINISTRATION
                </span>
            </div>
            <p style="font-size: 13.5px; color: var(--text-muted); margin: 0;">
                Supervision globale, gestion centralisée des utilisateurs et contrôle des quotas de points de vente par organisation.
            </p>
        </div>

        {{-- Top actions / tabs buttons --}}
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <button wire:click="setTab('quotas')" class="btn {{ $activeTab === 'quotas' ? 'btn-primary' : 'btn-ghost' }}" style="font-size: 13px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Quotas des Points de Vente</span>
            </button>
            <button wire:click="setTab('users')" class="btn {{ $activeTab === 'users' ? 'btn-primary' : 'btn-ghost' }}" style="font-size: 13px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Gestion des Utilisateurs</span>
            </button>
            <button wire:click="setTab('tenants')" class="btn {{ $activeTab === 'tenants' ? 'btn-primary' : 'btn-ghost' }}" style="font-size: 13px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Organisations / Tenants</span>
            </button>
            <button wire:click="setTab('passkeys')" class="btn {{ $activeTab === 'passkeys' ? 'btn-primary' : 'btn-ghost' }}" style="font-size: 13px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.87M12 15a2 2 0 100-4 2 2 0 000 4z"/></svg>
                <span>Clés d'Accès (Touch ID / Face ID)</span>
                @if(isset($myPasskeys) && $myPasskeys->count() > 0)
                    <span style="background: rgba(16, 185, 129, 0.15); color: #059669; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 9999px; margin-left: 4px;">{{ $myPasskeys->count() }}</span>
                @endif
            </button>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 22px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 22px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Global Platform KPIs --}}
    <div class="kpi-grid" style="margin-bottom: 24px;">
        <div class="kpi-card blue">
            <div class="kpi-icon-wrap blue">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="kpi-value">{{ $totalPos }}</div>
            <div class="kpi-label">Points de Vente Déployés</div>
            <div class="kpi-sub">
                <span style="color: #2563EB; font-weight: 600;">{{ $totalTenants }} organisations</span> enregistrées
            </div>
        </div>

        <div class="kpi-card green">
            <div class="kpi-icon-wrap green">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                </svg>
            </div>
            <div class="kpi-value">{{ $totalDef }}</div>
            <div class="kpi-label">Dispositifs DEF Fiscaux</div>
            <div class="kpi-sub">
                <span style="color: #059669; font-weight: 600;">{{ $totalCompanies }} entreprises</span> assujetties
            </div>
        </div>

        <div class="kpi-card yellow">
            <div class="kpi-icon-wrap yellow">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="kpi-value">{{ $totalUsers }}</div>
            <div class="kpi-label">Utilisateurs Plateforme</div>
            <div class="kpi-sub">
                <span style="color: #D97706; font-weight: 600;">{{ $totalActiveUsers }} comptes actifs</span>
            </div>
        </div>

        <div class="kpi-card red" style="background: #FFFBEB; border-color: #FDE68A;">
            <div class="kpi-icon-wrap yellow">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="kpi-value" style="color: #B45309;">{{ $totalSuperAdmins }}</div>
            <div class="kpi-label" style="color: #92400E;">Super Administrateurs</div>
            <div class="kpi-sub" style="color: #B45309;">
                {{ $totalAdmins }} admins · {{ $totalAnalysts }} analystes
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ONGLET 1 : GESTION DES QUOTAS DE POINTS DE VENTE --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'quotas')
        <div class="card" style="margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Quotas de Points de Vente par Organisation</span>
                    </h3>
                    <p style="margin: 3px 0 0 0; font-size: 12.5px; color: var(--text-muted);">
                        Définissez le nombre maximum de points de vente qu'une organisation / entreprise cliente a le droit de créer dans le système.
                    </p>
                </div>
                <button wire:click="openCreateTenant" class="btn btn-primary btn-sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Nouvelle Organisation</span>
                </button>
            </div>

            {{-- Quotas Table --}}
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 28%;">Organisation / Entreprise</th>
                            <th style="width: 14%;">Statut</th>
                            <th style="width: 18%; text-align: center;">Points de Vente Créés</th>
                            <th style="width: 22%;">Utilisation du Quota</th>
                            <th style="width: 18%; text-align: right;">Action Quota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenantsWithQuotas as $tenant)
                            @php
                                $posCount = $tenant->currentPointsOfSaleCount();
                                $maxPos   = $tenant->max_points_of_sale;
                                $percent  = $tenant->pointsOfSaleQuotaPercentage();
                                $isFull   = ($maxPos !== null && $maxPos > 0 && $posCount >= $maxPos);
                                $isClose  = ($maxPos !== null && $maxPos > 0 && $percent >= 80 && !$isFull);
                                $isExpanded = in_array($tenant->id, $expandedTenantIds);
                            @endphp
                            <tr style="{{ $isFull ? 'background-color: #FEF2F2;' : '' }}">
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <button wire:click="toggleExpandTenant({{ $tenant->id }})" style="background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 2px;">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="transform: rotate({{ $isExpanded ? '90deg' : '0deg' }}); transition: transform 0.15s ease;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                        <div>
                                            <div style="font-weight: 700; color: var(--text-main); font-size: 14px;">{{ $tenant->name }}</div>
                                            <div style="font-size: 11.5px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                                <span>Sous-domaine : <code style="font-family: monospace; background: var(--surface-2); padding: 1px 5px; border-radius: 4px;">{{ $tenant->subdomain ?? 'miltex' }}</code></span>
                                                <span>•</span>
                                                <span>{{ $tenant->companies->count() }} société(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($tenant->status === 'active')
                                        <span class="badge badge-sale">
                                            <span class="status-dot"></span>
                                            Actif
                                        </span>
                                    @elseif($tenant->status === 'trial')
                                        <span class="badge badge-warning">
                                            <span class="status-dot"></span>
                                            Essai
                                        </span>
                                    @else
                                        <span class="badge badge-cancelled">
                                            <span class="status-dot"></span>
                                            Suspendu
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div style="font-size: 15px; font-weight: 800; font-family: monospace; color: {{ $isFull ? '#DC2626' : 'var(--text-main)' }};">
                                        {{ $posCount }} <span style="font-size: 12px; font-weight: 600; color: var(--text-muted);">/ {{ $maxPos ? $maxPos : '∞' }}</span>
                                    </div>
                                    <div style="font-size: 10.5px; color: var(--text-muted); margin-top: 2px;">
                                        {{ $maxPos ? "Plafond : {$maxPos} PDV" : "Quota Illimité" }}
                                    </div>
                                </td>
                                <td>
                                    @if($maxPos)
                                        <div style="width: 100%; background: #E2E8F0; border-radius: 9999px; height: 8px; overflow: hidden; margin-bottom: 5px;">
                                            <div style="height: 100%; border-radius: 9999px; width: {{ $percent }}%; background: {{ $isFull ? '#DC2626' : ($isClose ? '#D97706' : '#059669') }}; transition: width 0.3s ease;"></div>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; font-size: 11px;">
                                            <span style="font-weight: 600; color: {{ $isFull ? '#DC2626' : ($isClose ? '#D97706' : 'var(--text-secondary)') }};">
                                                {{ $percent }}% utilisé
                                            </span>
                                            @if($isFull)
                                                <span style="color: #DC2626; font-weight: 700; text-transform: uppercase; font-size: 10px;">Plafond Atteint</span>
                                            @elseif($isClose)
                                                <span style="color: #D97706; font-weight: 700; font-size: 10px;">Proche du quota</span>
                                            @else
                                                <span style="color: #059669; font-weight: 600; font-size: 10px;">{{ $maxPos - $posCount }} restant(s)</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge badge-credit">
                                            <span class="status-dot"></span>
                                            Quota Illimité
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <button wire:click="openEditTenantQuota({{ $tenant->id }})" class="btn btn-ghost btn-sm" style="border-color: #CBD5E1;">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        <span>Ajuster Quota</span>
                                    </button>
                                </td>
                            </tr>

                            {{-- Accordion: Nested Points of Sale and Companies for this Tenant --}}
                            @if($isExpanded)
                                <tr style="background: #F8FAFC;">
                                    <td colspan="5" style="padding: 16px 24px; border-left: 3px solid var(--primary);">
                                        <div style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 0.05em; margin-bottom: 10px;">
                                            Détail des Sociétés & Points de Vente de {{ $tenant->name }} ({{ $posCount }} / {{ $maxPos ?: 'Illimité' }})
                                        </div>

                                        @if($tenant->companies->isEmpty())
                                            <div style="font-size: 12.5px; color: var(--text-muted); font-style: italic;">
                                                Aucune société enregistrée sous cette organisation.
                                            </div>
                                        @else
                                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                                @foreach($tenant->companies as $comp)
                                                    <div style="background: #FFFFFF; border: 1px solid var(--border-subtle); border-radius: 10px; padding: 12px 16px;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                                            <div>
                                                                <span style="font-weight: 700; font-size: 13px; color: var(--text-main);">{{ $comp->name }}</span>
                                                                <span style="font-size: 11px; color: var(--text-muted); margin-left: 8px; font-family: monospace;">NIF : {{ $comp->nif }}</span>
                                                            </div>
                                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                                <span style="font-size: 11.5px; color: var(--text-muted);">
                                                                    {{ $comp->pointsOfSale->count() }} point(s) de vente
                                                                </span>
                                                                <button wire:click="openEditCompanyQuota({{ $comp->id }})" class="btn btn-ghost btn-sm" style="font-size: 11px; padding: 3px 8px;">
                                                                    <span>Quota Société ({{ $comp->max_points_of_sale ? $comp->max_points_of_sale . ' max' : 'Hérité' }})</span>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        {{-- Points of Sale Pills --}}
                                                        @if($comp->pointsOfSale->isEmpty())
                                                            <div style="font-size: 12px; color: var(--text-muted); font-style: italic;">Aucun point de vente créé pour le moment.</div>
                                                        @else
                                                            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                                                @foreach($comp->pointsOfSale as $pos)
                                                                    <div style="background: var(--surface-2); border: 1px solid var(--border-subtle); padding: 5px 10px; border-radius: 7px; font-size: 12px; display: flex; align-items: center; gap: 6px;">
                                                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $pos->is_active ? '#059669' : '#DC2626' }};"></span>
                                                                        <span style="font-weight: 600; color: var(--text-main);">{{ $pos->name }}</span>
                                                                        <span style="color: var(--text-muted); font-size: 11px;">({{ $pos->city ?? 'N/A' }})</span>
                                                                        <span style="font-size: 10.5px; background: #FFFFFF; border: 1px solid var(--border-subtle); padding: 1px 5px; border-radius: 4px; font-family: monospace;">
                                                                            {{ $pos->devices->count() }} DEF
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 32px; color: var(--text-muted);">
                                    Aucune organisation enregistrée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ONGLET 2 : GESTION DES UTILISATEURS PLATEFORME --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'users')
        <div class="card" style="margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Gestion Centralisée de Tous les Utilisateurs (Multi-Tenants)</span>
                    </h3>
                    <p style="margin: 3px 0 0 0; font-size: 12.5px; color: var(--text-muted);">
                        Créez, assignez des organisations, attribuez des rôles administratifs et réinitialisez les accès utilisateurs.
                    </p>
                </div>
                <button wire:click="openCreateUser" class="btn btn-primary btn-sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Nouvel Utilisateur</span>
                </button>
            </div>

            {{-- Users Filter Bar --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 18px;">
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 5px;">Recherche</label>
                    <input type="text" wire:model.live.debounce.300ms="userSearch" placeholder="Nom ou adresse email..." style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 8px 12px; border-radius: 9px; font-size: 13px; outline: none;">
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 5px;">Rôle</label>
                    <select wire:model.live="userRoleFilter" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 8px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                        <option value="">Tous les rôles</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Administrateur</option>
                        <option value="analyst">Analyste Fiscal</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 5px;">Organisation / Tenant</label>
                    <select wire:model.live="userTenantFilter" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 8px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                        <option value="">Toutes les organisations</option>
                        <option value="global">Sans organisation (Plateforme Globale)</option>
                        @foreach($allTenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 5px;">Statut</label>
                    <select wire:model.live="userStatusFilter" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 8px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                        <option value="">Tous les statuts</option>
                        <option value="active">Comptes Actifs</option>
                        <option value="inactive">Comptes Désactivés</option>
                    </select>
                </div>
            </div>

            {{-- Users Table --}}
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 28%;">Utilisateur</th>
                            <th style="width: 16%;">Rôle</th>
                            <th style="width: 22%;">Organisation Rattachée</th>
                            <th style="width: 14%;">Statut Compte</th>
                            <th style="width: 20%; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="user-avatar" style="width: 34px; height: 34px; font-size: 12px; background: {{ $u->isSuperAdmin() ? 'linear-gradient(135deg, #D97706, #B45309)' : 'linear-gradient(135deg, #2563EB, #1D4ED8)' }};">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="display: flex; align-items: center; gap: 6px;">
                                                <span style="font-weight: 700; color: var(--text-main); font-size: 13.5px;">{{ $u->name }}</span>
                                                @if($u->passkeys->count() > 0)
                                                    <span title="{{ $u->passkeys->count() }} clé(s) biométrique(s) Touch ID / Face ID enregistrée(s)" style="display: inline-flex; align-items: center; gap: 3px; font-size: 10px; font-weight: 700; color: #0284C7; background: #E0F2FE; border: 1px solid #BAE6FD; padding: 1px 5px; border-radius: 4px;">
                                                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.87M12 15a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                                        Passkey
                                                    </span>
                                                @endif
                                            </div>
                                            <div style="font-size: 11.5px; color: var(--text-muted);">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($u->role === 'super_admin')
                                        <span style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;">
                                            <span style="width: 5px; height: 5px; border-radius: 50%; background: #D97706;"></span>
                                            Super Admin
                                        </span>
                                    @elseif($u->role === 'admin')
                                        <span class="badge badge-credit">
                                            <span class="status-dot"></span>
                                            Admin
                                        </span>
                                    @else
                                        <span class="badge badge-sale">
                                            <span class="status-dot"></span>
                                            Analyste
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($u->tenant)
                                        <span style="font-size: 13px; font-weight: 600; color: var(--text-main);">{{ $u->tenant->name }}</span>
                                    @else
                                        <span style="font-size: 12px; color: #D97706; font-weight: 600; font-style: italic;">Plateforme Globale (Sans restriction)</span>
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="toggleUserActive({{ $u->id }})" style="background: none; border: none; cursor: pointer; padding: 0;" title="Cliquer pour basculer le statut">
                                        @if($u->is_active)
                                            <span class="badge badge-sale">
                                                <span class="status-dot"></span>
                                                Actif
                                            </span>
                                        @else
                                            <span class="badge badge-cancelled">
                                                <span class="status-dot"></span>
                                                Désactivé
                                            </span>
                                        @endif
                                    </button>
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                                        <button wire:click="openResetPassword({{ $u->id }})" class="btn btn-ghost btn-sm" title="Réinitialiser le mot de passe" style="padding: 4px 8px; font-size: 11px;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                            <span>MDP</span>
                                        </button>
                                        <button wire:click="openEditUser({{ $u->id }})" class="btn btn-ghost btn-sm" title="Modifier l'utilisateur" style="padding: 4px 8px; font-size: 11px;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            <span>Modifier</span>
                                        </button>
                                        @if($u->id !== auth()->id())
                                            <button wire:click="deleteUser({{ $u->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer définitivement cet utilisateur ?" class="btn btn-danger btn-sm" title="Supprimer" style="padding: 4px 8px; font-size: 11px;">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 32px; color: var(--text-muted);">
                                    Aucun utilisateur trouvé avec les critères de recherche.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 16px;">
                {{ $users->links() }}
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ONGLET 3 : GESTION DES ORGANISATIONS (TENANTS) --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'tenants')
        <div class="card" style="margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Organisations & Entreprises Clientes</span>
                    </h3>
                    <p style="margin: 3px 0 0 0; font-size: 12.5px; color: var(--text-muted);">
                        Créez et configurez les comptes organisations, sous-domaines, plafonds de caisses et statuts d'accès.
                    </p>
                </div>
                <button wire:click="openCreateTenant" class="btn btn-primary btn-sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Nouvelle Organisation</span>
                </button>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 30%;">Nom Organisation</th>
                            <th style="width: 20%;">Sous-domaine</th>
                            <th style="width: 15%; text-align: center;">Quota PDV</th>
                            <th style="width: 15%;">Statut</th>
                            <th style="width: 20%; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allTenants as $t)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 14px;">{{ $t->name }}</div>
                                    <div style="font-size: 11.5px; color: var(--text-muted);">
                                        Créée le {{ $t->created_at?->format('d/m/Y') ?? 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <code style="font-family: monospace; background: var(--surface-2); padding: 3px 7px; border-radius: 5px; font-size: 12px; color: var(--primary);">
                                        {{ $t->subdomain ?? 'miltex' }}.miltex-group.com
                                    </code>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-weight: 800; font-family: monospace; font-size: 13.5px; color: var(--text-main);">
                                        {{ $t->max_points_of_sale ? $t->max_points_of_sale . ' PDV' : 'Illimité' }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="toggleTenantStatus({{ $t->id }})" style="background: none; border: none; cursor: pointer; padding: 0;">
                                        @if($t->status === 'active')
                                            <span class="badge badge-sale">
                                                <span class="status-dot"></span>
                                                Actif
                                            </span>
                                        @elseif($t->status === 'trial')
                                            <span class="badge badge-warning">
                                                <span class="status-dot"></span>
                                                Essai
                                            </span>
                                        @else
                                            <span class="badge badge-cancelled">
                                                <span class="status-dot"></span>
                                                Suspendu
                                            </span>
                                        @endif
                                    </button>
                                </td>
                                <td style="text-align: right;">
                                    <button wire:click="openEditTenant({{ $t->id }})" class="btn btn-ghost btn-sm">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        <span>Modifier</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 32px; color: var(--text-muted);">
                                    Aucune organisation enregistrée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ONGLET 4 : CLÉS D'ACCÈS BIOMÉTRIQUES (PASSKEYS / TOUCH ID / FACE ID) --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'passkeys')
        <div class="card" style="margin-bottom: 24px;" x-data="{
            regLoading: false,
            regError: null,
            regSuccess: null,
            async registerPasskey() {
                this.regLoading = true;
                this.regError = null;
                this.regSuccess = null;
                try {
                    const defaultName = navigator.userAgent.includes('Macintosh') ? 'MacBook Pro (Touch ID)' : (navigator.userAgent.includes('iPhone') ? 'iPhone (Face ID)' : 'Clé Biométrique');
                    const keyName = prompt('Nom descriptif pour cet appareil :', defaultName);
                    if (!keyName) {
                        this.regLoading = false;
                        return;
                    }
                    const res = await window.Passkeys.register(keyName);
                    this.regSuccess = res.message || 'Clé biométrique enregistrée avec succès.';
                    setTimeout(() => window.location.reload(), 1500);
                } catch (err) {
                    console.error(err);
                    if (err.name !== 'NotAllowedError') {
                        this.regError = err.message || 'Erreur lors de l\'enregistrement de la clé.';
                    }
                } finally {
                    this.regLoading = false;
                }
            }
        }">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" fill="none" stroke="#DC2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.87M12 15a2 2 0 100-4 2 2 0 000 4z"/></svg>
                        <span>Clés d'Accès Biométriques (Passkeys / Touch ID / Face ID)</span>
                    </h3>
                    <p style="margin: 4px 0 0; font-size: 12.5px; color: var(--text-muted);">
                        Authentification instantanée ultra-sécurisée conforme FIDO2/WebAuthn. Utilisez l'empreinte Touch ID de votre Mac ou Face ID sur mobile sans aucun mot de passe.
                    </p>
                </div>
                <div>
                    <button
                        type="button"
                        @click="registerPasskey"
                        :disabled="regLoading"
                        class="btn btn-primary"
                        style="display: inline-flex; align-items: center; gap: 8px;"
                    >
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span x-show="!regLoading">Enregistrer cet appareil (Touch ID / Face ID)</span>
                        <span x-show="regLoading" x-cloak>Configuration Touch ID en cours…</span>
                    </button>
                </div>
            </div>

            <template x-if="regSuccess">
                <div class="alert alert-success" style="margin-bottom: 18px;" x-text="regSuccess"></div>
            </template>

            <template x-if="regError">
                <div class="alert alert-danger" style="margin-bottom: 18px;" x-text="regError"></div>
            </template>

            {{-- Biometric Readiness Banner --}}
            <div style="background: #F8FAFC; border: 1px solid var(--border-subtle); border-radius: 12px; padding: 16px 20px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #FEF3C7; border: 1px solid #FDE68A; display: flex; align-items: center; justify-content: center; color: #D97706; flex-shrink: 0;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: var(--text-main);">
                            Support Biométrique Navigateur Actif
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            Votre navigateur prend en charge les clés de sécurité WebAuthn, Apple Touch ID, Face ID et Windows Hello.
                        </div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-flex; align-items: center; gap: 6px; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; padding: 4px 10px; border-radius: 9999px; font-size: 11.5px; font-weight: 700;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
                        Prêt pour Touch ID / Face ID
                    </span>
                </div>
            </div>

            {{-- Table of registered passkeys --}}
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Appareil / Clé d'accès</th>
                            <th>Identifiant (Credential)</th>
                            <th>Type Authentificateur</th>
                            <th>Date d'ajout</th>
                            <th>Dernière utilisation</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myPasskeys as $pk)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; border: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: center; color: var(--text-secondary);">
                                            @if($pk->iconName() === 'apple')
                                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.37c.63-.77 1.06-1.85.94-2.93-1 .04-2.13.66-2.76 1.41-.56.65-1.05 1.74-.92 2.8 1.11.09 2.11-.53 2.74-1.28z"/></svg>
                                            @else
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.87M12 15a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--text-main); font-size: 13.5px;">{{ $pk->name }}</div>
                                            <div style="font-size: 11px; color: var(--text-muted);">Associé au compte {{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code style="font-size: 11.5px; background: #F1F5F9; padding: 2px 6px; border-radius: 4px; color: #475569; font-family: monospace;">
                                        {{ substr($pk->credential_id, 0, 10) }}...{{ substr($pk->credential_id, -8) }}
                                    </code>
                                </td>
                                <td>
                                    <span style="font-size: 12px; font-weight: 600; color: #0284C7; background: #E0F2FE; border: 1px solid #BAE6FD; padding: 3px 8px; border-radius: 6px;">
                                        Biométrie Plateforme
                                    </span>
                                </td>
                                <td style="font-size: 12.5px; color: var(--text-secondary);">
                                    {{ $pk->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td style="font-size: 12.5px; color: var(--text-secondary);">
                                    {{ $pk->last_used_at ? $pk->last_used_at->diffForHumans() : 'Pas encore utilisée' }}
                                </td>
                                <td style="text-align: right;">
                                    <button
                                        type="button"
                                        wire:click="deletePasskey({{ $pk->id }})"
                                        wire:confirm="Êtes-vous sûr de vouloir révoquer cette clé d'accès biométrique ?"
                                        class="btn btn-ghost btn-sm"
                                        style="color: #DC2626;"
                                    >
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Révoquer</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px 20px;">
                                    <div style="width: 48px; height: 48px; margin: 0 auto 12px; border-radius: 12px; background: #FFFBEB; border: 1px solid #FDE68A; display: flex; align-items: center; justify-content: center; color: #D97706;">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.87M12 15a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                    </div>
                                    <div style="font-size: 14px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">
                                        Aucune clé biométrique configurée
                                    </div>
                                    <div style="font-size: 12.5px; color: var(--text-muted); max-width: 420px; margin: 0 auto 16px;">
                                        Enregistrez le capteur Touch ID de ce Mac ou Face ID pour vous connecter en un instant sans mot de passe.
                                    </div>
                                    <button
                                        type="button"
                                        @click="registerPasskey"
                                        class="btn btn-primary btn-sm"
                                        style="display: inline-flex; align-items: center; gap: 6px;"
                                    >
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        <span>Configurer Touch ID / Face ID</span>
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : AJUSTER LE QUOTA DE POINTS DE VENTE --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if($showQuotaModal)
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 999; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div class="card" style="width: 100%; max-width: 480px; padding: 26px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border-subtle);">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-main);">
                        Ajuster le Quota de Points de Vente
                    </h3>
                    <button wire:click="$set('showQuotaModal', false)" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 18px;">✕</button>
                </div>

                <div style="margin-bottom: 18px;">
                    <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 4px;">Entité concernée</span>
                    <div style="font-size: 15px; font-weight: 700; color: var(--primary);">{{ $targetEntityName }}</div>
                </div>

                <form wire:submit="saveQuota">
                    <div style="margin-bottom: 18px;">
                        <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; cursor: pointer;">
                            <input type="checkbox" wire:model.live="formQuotaIsUnlimited" style="width: 16px; height: 16px; accent-color: var(--primary);">
                            <span style="font-size: 13.5px; font-weight: 600; color: var(--text-main);">Quota illimité (Aucun plafond de points de vente)</span>
                        </label>

                        @if(!$formQuotaIsUnlimited)
                            <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-bottom: 6px;">
                                Nombre Maximum de Points de Vente Autorisés
                            </label>
                            <input type="number" min="1" max="1000" wire:model="formMaxPointsOfSale" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 14px; font-weight: 700; outline: none;">
                            <span style="font-size: 11.5px; color: var(--text-muted); display: block; margin-top: 5px;">
                                L'administrateur de cette organisation ne pourra pas créer plus de PDV que ce nombre.
                            </span>
                        @endif
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                        <button type="button" wire:click="$set('showQuotaModal', false)" class="btn btn-ghost">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer le Quota</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : CRÉER / ÉDITER UN UTILISATEUR --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if($showUserModal)
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 999; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div class="card" style="width: 100%; max-width: 520px; padding: 26px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border-subtle);">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-main);">
                        {{ $isEditingUser ? "Modifier l'Utilisateur" : "Créer un Nouvel Utilisateur" }}
                    </h3>
                    <button wire:click="$set('showUserModal', false)" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 18px;">✕</button>
                </div>

                <form wire:submit="saveUser">
                    <div style="display: grid; gap: 14px;">
                        <div>
                            <label style="display: block; font-size: 11.5px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Nom complet</label>
                            <input type="text" wire:model="userName" placeholder="Ex: Jean Dupont" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13.5px; outline: none;">
                            @error('userName') <span style="font-size: 11.5px; color: #DC2626;">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label style="display: block; font-size: 11.5px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Adresse email</label>
                            <input type="email" wire:model="userEmail" placeholder="nom@entreprise.cd" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13.5px; outline: none;">
                            @error('userEmail') <span style="font-size: 11.5px; color: #DC2626;">{{ $message }}</span> @enderror
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div>
                                <label style="display: block; font-size: 11.5px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Rôle</label>
                                <select wire:model.live="userRole" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                                    <option value="analyst">Analyste Fiscal</option>
                                    <option value="admin">Administrateur</option>
                                    <option value="super_admin">Super Administrateur</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 11.5px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Organisation / Tenant</label>
                                <select wire:model="userTenantId" {{ $userRole === 'super_admin' ? 'disabled' : '' }} style="width: 100%; background: {{ $userRole === 'super_admin' ? '#F1F5F9' : '#FFFFFF' }}; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                                    <option value="">{{ $userRole === 'super_admin' ? 'Aucune (Global)' : '-- Sélectionner un tenant --' }}</option>
                                    @foreach($allTenants as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 11.5px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">
                                {{ $isEditingUser ? 'Nouveau mot de passe (laisser vide pour ne pas modifier)' : 'Mot de passe' }}
                            </label>
                            <input type="password" wire:model="userPassword" placeholder="••••••••" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13.5px; outline: none;">
                            @error('userPassword') <span style="font-size: 11.5px; color: #DC2626;">{{ $message }}</span> @enderror
                        </div>

                        @if(!empty($userPassword))
                            <div>
                                <label style="display: block; font-size: 11.5px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Confirmer le mot de passe</label>
                                <input type="password" wire:model="userPasswordConfirmation" placeholder="••••••••" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13.5px; outline: none;">
                            </div>
                        @endif

                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-top: 6px;">
                            <input type="checkbox" wire:model="userIsActive" style="width: 16px; height: 16px; accent-color: var(--primary);">
                            <span style="font-size: 13px; font-weight: 600; color: var(--text-main);">Compte actif (autorisé à se connecter)</span>
                        </label>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                        <button type="button" wire:click="$set('showUserModal', false)" class="btn btn-ghost">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEditingUser ? 'Enregistrer les modifications' : 'Créer l\'utilisateur' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : RÉINITIALISATION RAPIDE MOT DE PASSE --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if($showResetPasswordModal)
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 999; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div class="card" style="width: 100%; max-width: 440px; padding: 26px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border-subtle);">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-main);">
                        Réinitialiser le Mot de Passe
                    </h3>
                    <button wire:click="$set('showResetPasswordModal', false)" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 18px;">✕</button>
                </div>

                <p style="font-size: 13px; color: var(--text-secondary); margin: 0 0 16px 0;">
                    Un nouveau mot de passe aléatoire et sécurisé a été généré pour <strong>{{ $resetUserTargetName }}</strong>.
                </p>

                <div style="background: #F1F5F9; border: 1px dashed var(--border-light); padding: 14px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">Nouveau Mot de Passe Temporaire</div>
                    <code style="font-size: 16px; font-weight: 800; color: var(--primary); font-family: monospace; letter-spacing: 0.05em;">
                        {{ $newGeneratedPassword }}
                    </code>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" wire:click="$set('showResetPasswordModal', false)" class="btn btn-ghost">Fermer</button>
                    <button type="button" wire:click="confirmPasswordReset" class="btn btn-primary">Appliquer ce Mot de Passe</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL : CRÉER / ÉDITER UN TENANT (ORGANISATION) --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if($showTenantModal)
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(4px); z-index: 999; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div class="card" style="width: 100%; max-width: 520px; padding: 26px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border-subtle);">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-main);">
                        {{ $isEditingTenant ? "Modifier l'Organisation" : "Créer une Nouvelle Organisation" }}
                    </h3>
                    <button wire:click="$set('showTenantModal', false)" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 18px;">✕</button>
                </div>

                <form wire:submit="saveTenant">
                    <div style="display: grid; gap: 14px;">
                        <div>
                            <label style="display: block; font-size: 11.5px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Nom de l'Organisation / Groupe</label>
                            <input type="text" wire:model="tenantName" placeholder="Ex: Groupe Commercial Congo" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13.5px; outline: none;">
                            @error('tenantName') <span style="font-size: 11.5px; color: #DC2626;">{{ $message }}</span> @enderror
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div>
                                <label style="display: block; font-size: 11.5px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Sous-domaine</label>
                                <input type="text" wire:model="tenantSubdomain" placeholder="ex: groupe-congo" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13.5px; outline: none;">
                                @error('tenantSubdomain') <span style="font-size: 11.5px; color: #DC2626;">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label style="display: block; font-size: 11.5px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">Statut</label>
                                <select wire:model="tenantStatus" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                                    <option value="active">Actif</option>
                                    <option value="trial">Essai</option>
                                    <option value="suspended">Suspendu</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label style="display: block; font-size: 11.5px; font-weight: 700; color: var(--text-secondary); margin-bottom: 5px;">
                                Quota de Points de Vente Autorisés
                            </label>
                            <input type="number" min="1" max="1000" wire:model="tenantMaxPos" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 14px; font-weight: 700; outline: none;">
                            <span style="font-size: 11.5px; color: var(--text-muted); display: block; margin-top: 4px;">
                                Nombre limite de caisses / magasins que ce client pourra enregistrer.
                            </span>
                            @error('tenantMaxPos') <span style="font-size: 11.5px; color: #DC2626;">{{ $message }}</span> @enderror
                        </div>

                        @if(!$isEditingTenant)
                            <div style="border-top: 1px dashed var(--border-subtle); padding-top: 12px; margin-top: 4px;">
                                <span style="font-size: 11.5px; font-weight: 700; color: var(--primary); text-transform: uppercase; display: block; margin-bottom: 10px;">
                                    Société Initiale (Facultatif)
                                </span>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px;">Raison Sociale</label>
                                        <input type="text" wire:model="companyName" placeholder="Nom de l'entreprise" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 8px 10px; border-radius: 8px; font-size: 12.5px; outline: none;">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 11px; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px;">NIF DGI</label>
                                        <input type="text" wire:model="companyNif" placeholder="Ex: A1234567Z" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 8px 10px; border-radius: 8px; font-size: 12.5px; outline: none;">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                        <button type="button" wire:click="$set('showTenantModal', false)" class="btn btn-ghost">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEditingTenant ? 'Mettre à jour l\'organisation' : 'Créer l\'organisation' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
