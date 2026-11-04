@section('title', 'Tableau de Bord')

<div>
    {{-- Filters Bar --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 14px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #EFF6FF; display: flex; align-items: center; justify-content: center; color: var(--primary); border: 1px solid #DBEAFE;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </div>
                <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--text-main);">Filtres d'Analyse</h3>
            </div>
            @if($hasFilters)
                <button wire:click="clearFilters" class="btn btn-ghost btn-sm" style="color: #DC2626; border-color: #FECACA; background: #FEF2F2;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Réinitialiser les filtres</span>
                </button>
            @endif
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
            {{-- Point of Sale Select --}}
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Point de Vente</label>
                <select wire:model.live="posId" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 10px 14px; border-radius: 10px; font-size: 13.5px; outline: none;">
                    <option value="">Tous les points de vente (Consolidé)</option>
                    @foreach($pointsOfSale as $pos)
                        <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->city ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Device Select --}}
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Matériel DEF</label>
                <select wire:model.live="deviceId" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 10px 14px; border-radius: 10px; font-size: 13.5px; outline: none;">
                    <option value="">Tous les matériels</option>
                    @foreach($devices as $dev)
                        <option value="{{ $dev->id }}">{{ $dev->nid }} @if($dev->model) — {{ $dev->model }}@endif</option>
                    @endforeach
                </select>
            </div>

            {{-- Journal Select --}}
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;">Journal Électronique</label>
                <select wire:model.live="selectedJournalId" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 10px 14px; border-radius: 10px; font-size: 13.5px; outline: none;">
                    <option value="">Tous les journaux</option>
                    @foreach($journals as $j)
                        <option value="{{ $j->id }}">{{ $j->original_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Context Banner --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; background: #EFF6FF; border: 1px solid #BFDBFE; padding: 13px 20px; border-radius: 12px; gap: 12px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <svg width="18" height="18" fill="none" stroke="#2563EB" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="font-size: 13px; color: #1E40AF;">
                Périmètre d'analyse actif : 
                <strong style="color: #1E3A8A; font-weight: 700;">
                    @if($selectedJournalId)
                        Journal spécifique ({{ \App\Models\Journal::find($selectedJournalId)?->original_name }})
                    @elseif($deviceId)
                        Matériel DEF ({{ \App\Models\Device::find($deviceId)?->nid }})
                    @elseif($posId)
                        Point de Vente ({{ \App\Models\PointOfSale::find($posId)?->name }})
                    @else
                        Consolidé Global Entreprise
                    @endif
                </strong>
            </span>
        </div>
        @if(!$selectedJournalId && $posId)
            <span class="badge badge-sale">
                <span class="status-dot"></span>
                Mode PDV Actif
            </span>
        @endif
    </div>

    {{-- KPI Grid --}}
    <div class="kpi-grid">
        <div class="kpi-card blue">
            <div class="kpi-icon-wrap blue">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="kpi-value">{{ number_format($totalTtc, 0, ',', ' ') }} <span style="font-size: 14px; font-weight: 600; color: var(--text-muted);">CDF</span></div>
            <div class="kpi-label">Chiffre d'Affaires TTC</div>
            <div class="kpi-sub">Total des ventes enregistrées</div>
        </div>

        <div class="kpi-card green">
            <div class="kpi-icon-wrap green">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
            </div>
            <div class="kpi-value">{{ number_format($totalTva, 0, ',', ' ') }} <span style="font-size: 14px; font-weight: 600; color: var(--text-muted);">CDF</span></div>
            <div class="kpi-label">TVA Collectée</div>
            <div class="kpi-sub">Taux légal d'application : 16%</div>
        </div>

        <div class="kpi-card yellow">
            <div class="kpi-icon-wrap yellow">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="kpi-value">{{ number_format($totalInvoices, 0, ',', ' ') }}</div>
            <div class="kpi-label">Factures de Vente Émises</div>
            <div class="kpi-sub">
                <span style="color: #DC2626; font-weight: 600;">{{ $totalCancelled }}</span> transaction(s) annulée(s)
            </div>
        </div>

        <div class="kpi-card {{ $anomalyCount > 0 ? 'red' : 'green' }}">
            <div class="kpi-icon-wrap {{ $anomalyCount > 0 ? 'red' : 'green' }}">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    @if($anomalyCount > 0)
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @endif
                </svg>
            </div>
            <div class="kpi-value">{{ $anomalyCount }}</div>
            <div class="kpi-label">Anomalies Actives</div>
            <div class="kpi-sub">
                @if($anomalyCount > 0)
                    <span style="color: #DC2626; font-weight: 700;">Intervention requise</span>
                @else
                    <span style="color: #059669; font-weight: 700;">Conformité fiscale vérifiée</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
        {{-- Daily Sales Chart --}}
        <div class="card" wire:ignore id="daily-chart-card">
            <div class="card-title">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                Évolution Journalière du Chiffre d'Affaires (CDF TTC)
            </div>
            <div id="dailyChart" style="min-height: 230px;"></div>
        </div>

        {{-- Hourly activity --}}
        <div class="card" wire:ignore id="hourly-chart-card">
            <div class="card-title">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Activité de Facturation par Heure
            </div>
            <div id="hourlyChart" style="min-height: 230px;"></div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
        {{-- Top Articles --}}
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div class="card-title" style="margin: 0;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Top Articles (CA TTC)
                </div>
                @if($journal)
                    <a href="{{ route('export.articles.excel', $journal->id) }}" class="btn btn-ghost btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Excel</span>
                    </a>
                @elseif($posId)
                    <a href="{{ route('export.pos.articles.excel', $posId) }}" class="btn btn-ghost btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Excel PDV</span>
                    </a>
                @endif
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th style="text-align: right;">Qté</th>
                            <th style="text-align: right;">CA TTC (CDF)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topArticles as $article)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-main);">{{ $article['name'] }}</td>
                            <td style="text-align: right; font-family: monospace;" class="tabular-nums">{{ number_format($article['qty'], 0, ',', ' ') }}</td>
                            <td style="text-align: right; font-weight: 700; color: #059669; font-family: monospace;" class="tabular-nums">{{ number_format($article['revenue'], 0, ',', ' ') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 32px; color: var(--text-muted);">
                                Aucune transaction d'article pour ce périmètre
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Anomalies --}}
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <div class="card-title" style="margin: 0;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Anomalies Récentes
                </div>
                <a href="{{ route('anomalies.index') }}" class="btn btn-ghost btn-sm">
                    <span>Consulter tout</span>
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @if($recentAnomalies->isEmpty())
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; text-align: center; gap: 8px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; border: 1px solid #D1FAE5;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div style="font-size: 13.5px; font-weight: 700; color: #065F46;">Aucune anomalie non résolue</div>
                    <div style="font-size: 12px; color: var(--text-muted);">Tous les journaux et factures respectent les règles de conformité DGI</div>
                </div>
            @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Sévérité</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAnomalies as $anomaly)
                        <tr>
                            <td>
                                <span class="badge badge-{{ $anomaly->severity }}">
                                    <span class="status-dot"></span>
                                    {{ strtoupper($anomaly->severity) }}
                                </span>
                            </td>
                            <td style="font-size: 12.5px; color: var(--text-main);">{{ Str::limit($anomaly->description, 60) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Exports Section --}}
    @auth
    @if(auth()->user()->canExport())
    <div class="card">
        <div class="card-title">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Rapports et Fichiers d'Exportation
        </div>
        
        @if($journal)
            {{-- Single Journal Context --}}
            <div style="background: #F8FAFC; padding: 18px 20px; border-radius: 12px; border: 1px solid var(--border-subtle); margin-bottom: 14px;">
                <h4 style="margin: 0 0 12px 0; font-size: 13.5px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    <span>Rapports pour le Journal :</span>
                    <span style="color: var(--primary); font-family: monospace;">{{ $journal->original_name }}</span>
                </h4>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('export.invoices.excel', [$journal->id, 'all']) }}" class="btn btn-ghost btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Toutes transactions (Excel)
                    </a>
                    <a href="{{ route('export.invoices.excel', [$journal->id, 'sale']) }}" class="btn btn-ghost btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Ventes (Excel)
                    </a>
                    <a href="{{ route('export.articles.excel', $journal->id) }}" class="btn btn-ghost btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Articles (Excel)
                    </a>
                    <a href="{{ route('export.full.excel', $journal->id) }}" class="btn btn-success btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Rapport Complet (Excel)
                    </a>
                    <a href="{{ route('export.compliance.pdf', $journal->id) }}" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Conformité DGI (PDF)
                    </a>
                    <a href="{{ route('export.tva.pdf', $journal->id) }}" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        Synthèse TVA (PDF)
                    </a>
                    <a href="{{ route('export.tva.daily.pdf', $journal->id) }}" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        TVA Journalière (PDF)
                    </a>
                    <a href="{{ route('export.tva.excel', $journal->id) }}" class="btn btn-ghost btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Détail TVA (Excel)
                    </a>
                </div>
            </div>
        @endif

        @if($posId)
            {{-- Consolidated Point of Sale Context --}}
            <div style="background: #ECFDF5; padding: 18px 20px; border-radius: 12px; border: 1px solid #A7F3D0; margin-bottom: 14px;">
                <h4 style="margin: 0 0 12px 0; font-size: 13.5px; font-weight: 700; color: #065F46; display: flex; align-items: center; gap: 8px;">
                    <span>Rapports Consolidés pour le Point de Vente :</span>
                    <span style="color: #059669;">{{ \App\Models\PointOfSale::find($posId)?->name }}</span>
                </h4>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('export.pos.invoices.excel', [$posId, 'all']) }}" class="btn btn-ghost btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Transactions PDV (Excel)
                    </a>
                    <a href="{{ route('export.pos.invoices.excel', [$posId, 'sale']) }}" class="btn btn-ghost btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Ventes PDV (Excel)
                    </a>
                    <a href="{{ route('export.pos.articles.excel', $posId) }}" class="btn btn-ghost btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Articles PDV (Excel)
                    </a>
                    <a href="{{ route('export.pos.full.excel', $posId) }}" class="btn btn-success btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Rapport Complet PDV (Excel)
                    </a>
                    <a href="{{ route('export.pos.tva.excel', $posId) }}" class="btn btn-ghost btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Détail TVA PDV (Excel)
                    </a>
                </div>
            </div>
        @elseif(!$journal)
            <div style="padding: 18px 24px; background: #F8FAFC; border-radius: 12px; text-align: center; color: var(--text-muted); font-size: 13px; border: 1px dashed #CBD5E1; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Sélectionnez un Point de Vente ou un Journal dans les filtres d'analyse pour activer les options de téléchargement d'exports Excel et PDF.</span>
            </div>
        @endif
    </div>
    @endif
    @endauth
</div>

@push('scripts')
<script>
    let dailyChart = null;
    let hourlyChart = null;

    document.addEventListener('livewire:navigated', () => initCharts());
    document.addEventListener('DOMContentLoaded', () => initCharts());

    window.addEventListener('charts-updated', () => {
        initCharts();
    });

    function initCharts() {
        const dailyData = @json($dailySales ?? []);
        const hourlyData = @json($hourlySales ?? []);

        if (dailyChart) {
            dailyChart.destroy();
            dailyChart = null;
        }
        if (hourlyChart) {
            hourlyChart.destroy();
            hourlyChart = null;
        }

        // Daily Chart
        const dailyEl = document.getElementById('dailyChart');
        if (dailyEl && dailyData.length > 0) {
            dailyChart = new ApexCharts(dailyEl, {
                chart: { 
                    type: 'area', 
                    height: 230, 
                    toolbar: { show: false }, 
                    background: 'transparent', 
                    foreColor: '#64748B',
                    fontFamily: 'Plus Jakarta Sans, sans-serif'
                },
                series: [{ name: 'CA TTC (CDF)', data: dailyData.map(d => ({ x: d.date, y: d.total })) }],
                xaxis: { 
                    type: 'datetime', 
                    labels: { style: { colors: '#64748B', fontSize: '11px' } },
                    axisBorder: { color: '#E2E8F0' },
                    axisTicks: { color: '#E2E8F0' }
                },
                yaxis: { 
                    labels: { 
                        formatter: v => v >= 1000 ? (v/1000).toFixed(0)+'K' : v, 
                        style: { colors: '#64748B', fontSize: '11px' } 
                    } 
                },
                fill: { 
                    type: 'gradient', 
                    gradient: { 
                        shadeIntensity: 1, 
                        opacityFrom: 0.28, 
                        opacityTo: 0.02,
                        stops: [0, 95, 100] 
                    } 
                },
                stroke: { curve: 'smooth', width: 2.5 },
                colors: ['#2563EB'],
                grid: { 
                    borderColor: '#E2E8F0',
                    strokeDashArray: 4
                },
                tooltip: { 
                    theme: 'light', 
                    y: { formatter: v => new Intl.NumberFormat('fr-CD').format(v) + ' CDF' } 
                },
            });
            dailyChart.render();
        } else if (dailyEl) {
            dailyEl.innerHTML = '<div style="text-align:center; padding: 75px 0; color: #64748B; font-size:13px;">Aucune donnée de vente pour cette période</div>';
        }

        // Hourly bar chart
        const hourlyEl = document.getElementById('hourlyChart');
        if (hourlyEl) {
            const hours = Array.from({length:24}, (_,i) => i+'h');
            const counts = hours.map((_,i) => hourlyData[i] ?? 0);
            const hasData = counts.some(c => c > 0);
            
            if (hasData) {
                hourlyChart = new ApexCharts(hourlyEl, {
                    chart: { 
                        type: 'bar', 
                        height: 230, 
                        toolbar: { show: false }, 
                        background: 'transparent', 
                        foreColor: '#64748B',
                        fontFamily: 'Plus Jakarta Sans, sans-serif'
                    },
                    series: [{ name: 'Factures émises', data: counts }],
                    xaxis: { 
                        categories: hours, 
                        labels: { style: { fontSize: '10px', colors: Array(24).fill('#64748B') } },
                        axisBorder: { color: '#E2E8F0' },
                        axisTicks: { color: '#E2E8F0' }
                    },
                    yaxis: { 
                        labels: { style: { colors: '#64748B', fontSize: '11px' } } 
                    },
                    colors: ['#059669'],
                    grid: { 
                        borderColor: '#E2E8F0',
                        strokeDashArray: 4
                    },
                    plotOptions: { 
                        bar: { 
                            borderRadius: 4,
                            columnWidth: '55%'
                        } 
                    },
                    tooltip: { theme: 'light' },
                });
                hourlyChart.render();
            } else {
                hourlyEl.innerHTML = '<div style="text-align:center; padding: 75px 0; color: #64748B; font-size:13px;">Aucune donnée d\'activité horaire</div>';
            }
        }
    }
</script>
@endpush
