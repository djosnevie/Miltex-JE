@section('title', 'Rapports & Exports Fiscaux')

<div>
    {{-- Page Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <h1 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.02em;">
                    Rapports & Exports Fiscaux
                </h1>
                <span class="badge badge-info">
                    <span class="status-dot"></span>
                    Conformité DGI
                </span>
            </div>
            <p style="font-size: 13.5px; color: var(--text-muted); margin: 0;">
                Générez des états fiscaux certifiés, grands livres comptables Excel et synthèses PDF avec filtres avancés par date, période et périmètre.
            </p>
        </div>

        {{-- Active Scope Badge --}}
        <div>
            @if($selectedJournal)
                <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px; padding: 8px 14px; display: flex; align-items: center; gap: 10px;">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #2563EB;"></div>
                    <div>
                        <div style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; color: #1E40AF; letter-spacing: 0.05em;">Périmètre Actif</div>
                        <div style="font-size: 13px; font-weight: 700; color: #1E3A8A;">Journal : {{ Str::limit($selectedJournal->original_name, 28) }}</div>
                    </div>
                </div>
            @elseif($posId)
                <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 12px; padding: 8px 14px; display: flex; align-items: center; gap: 10px;">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #059669;"></div>
                    <div>
                        <div style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; color: #065F46; letter-spacing: 0.05em;">Périmètre Actif</div>
                        <div style="font-size: 13px; font-weight: 700; color: #064E3B;">Point de Vente : {{ \App\Models\PointOfSale::find($posId)?->name }}</div>
                    </div>
                </div>
            @else
                <div style="background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 12px; padding: 8px 14px; display: flex; align-items: center; gap: 10px;">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #64748B;"></div>
                    <div>
                        <div style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; color: #475569; letter-spacing: 0.05em;">Périmètre Actif</div>
                        <div style="font-size: 13px; font-weight: 700; color: #0F172A;">Consolidation Nationale (Tous PDV)</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- FILTRES AVANCÉS D'EXTRACTION --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="card" style="margin-bottom: 24px; padding: 22px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 34px; height: 34px; border-radius: 10px; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: var(--text-main);">Filtres & Critères d'Extraction</h3>
                    <p style="margin: 2px 0 0 0; font-size: 12px; color: var(--text-muted);">Paramétrez les critères pour cibler précisément les écritures comptables et fiscales à exporter.</p>
                </div>
            </div>

            @if($hasActiveFilters)
                <button wire:click="resetFilters" class="btn btn-ghost btn-sm" style="color: #DC2626; border-color: #FECACA; background: #FEF2F2;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Réinitialiser tous les filtres</span>
                </button>
            @endif
        </div>

        {{-- Filter Fields Grid --}}
        <div style="display: flex; flex-direction: column; gap: 18px;">
            
            {{-- ROW 1: Périmètre Organisationnel --}}
            <div>
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 0.06em; display: block; margin-bottom: 10px;">
                    1. Périmètre Organisationnel (PDV, Matériel, Journal)
                </span>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
                    {{-- Point de Vente --}}
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Point de Vente</label>
                        <select wire:model.live="posId" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                            <option value="">Tous les points de vente (Consolidé National)</option>
                            @foreach($pointsOfSale as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->city ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Matériel DEF / NID --}}
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Dispositif DEF (NID)
                            @if($posId)<span style="color: var(--primary); font-size: 10.5px;">(filtré par PDV)</span>@endif
                        </label>
                        <select wire:model.live="deviceId" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                            <option value="">Tous les dispositifs DEF</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}">
                                    DEF NID : {{ $device->nid }} ({{ $device->pointOfSale->name ?? 'PDV' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Journal Électronique --}}
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Journal Électronique
                            @if($selectedJournalId)<span style="color: var(--primary); font-size: 10.5px;">(spécifique)</span>@endif
                        </label>
                        <select wire:model.live="selectedJournalId" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                            <option value="">Consolidation multi-journaux</option>
                            @foreach($journals as $j)
                                <option value="{{ $j->id }}">
                                    {{ $j->original_name }} ({{ $j->device->nid ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ROW 2: Période & Plage de Dates --}}
            <div style="border-top: 1px dashed var(--border-subtle); padding-top: 16px;">
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 0.06em; display: block; margin-bottom: 10px;">
                    2. Période & Plage Chronologique
                </span>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
                    {{-- Période Prédéfinie --}}
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Période Prédéfinie</label>
                        <select wire:model.live="period" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                            <option value="all">Toutes les dates (Historique complet)</option>
                            <option value="today">Aujourd'hui</option>
                            <option value="yesterday">Hier</option>
                            <option value="this_week">Cette semaine en cours</option>
                            <option value="this_month">Ce mois-ci (Mois courant)</option>
                            <option value="last_month">Mois dernier (Clôturé)</option>
                            <option value="this_quarter">Ce trimestre (Trimestre courant)</option>
                            <option value="this_year">Cette année (Exercice fiscal courant)</option>
                            <option value="custom">Plage personnalisée (Dates précises)</option>
                        </select>
                    </div>

                    {{-- Date de début --}}
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Date Début (Inclus)
                        </label>
                        <input type="date" wire:model.live="dateFrom" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 8.5px 12px; border-radius: 9px; font-size: 13px; outline: none;">
                    </div>

                    {{-- Date de fin --}}
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">
                            Date Fin (Inclus)
                        </label>
                        <input type="date" wire:model.live="dateTo" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 8.5px 12px; border-radius: 9px; font-size: 13px; outline: none;">
                    </div>
                </div>
            </div>

            {{-- ROW 3: Typologie, Mode de Règlement & Statut MCF --}}
            <div style="border-top: 1px dashed var(--border-subtle); padding-top: 16px;">
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 0.06em; display: block; margin-bottom: 10px;">
                    3. Typologie des Transactions & Contrôle Fiscal
                </span>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
                    {{-- Type d'opération --}}
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Type d'Opération</label>
                        <select wire:model.live="type" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                            <option value="all">Toutes les opérations</option>
                            <option value="sale">Ventes normales uniquement</option>
                            <option value="credit_note">Avoirs / Notes de crédit</option>
                            <option value="cancelled">Factures annulées</option>
                        </select>
                    </div>

                    {{-- Mode de règlement --}}
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Mode de Règlement</label>
                        <select wire:model.live="paymentMode" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                            <option value="all">Tous les modes de règlement</option>
                            <option value="cash">Espèces (Cash)</option>
                            <option value="card">Carte Bancaire (POS/TPE)</option>
                            <option value="mobile_money">Mobile Money (M-Pesa, Orange, Airtel)</option>
                            <option value="bank_transfer">Virement Bancaire</option>
                            <option value="credit">Ventes à Crédit</option>
                        </select>
                    </div>

                    {{-- Statut MCF / Audit --}}
                    <div>
                        <label style="display: block; font-size: 11.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px;">Conformité MCF</label>
                        <select wire:model.live="mcfFilter" style="width: 100%; background: #FFFFFF; border: 1px solid var(--border-light); color: var(--text-main); padding: 9px 12px; border-radius: 9px; font-size: 13px; outline: none; cursor: pointer;">
                            <option value="all">Toutes les transactions</option>
                            <option value="without_error">Certifiées valides (Sans anomalie MCF)</option>
                            <option value="with_error">Avec anomalie / Erreur MCF signalée</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        {{-- Active Filters Recap Bar --}}
        @if($hasActiveFilters)
            <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Filtres actifs :</span>
                    
                    @if($posId)
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            PDV : {{ \App\Models\PointOfSale::find($posId)?->name }}
                        </span>
                    @endif

                    @if($deviceId)
                        <span class="badge badge-neutral">
                            <span class="status-dot"></span>
                            DEF : {{ \App\Models\Device::find($deviceId)?->nid }}
                        </span>
                    @endif

                    @if($selectedJournalId && $selectedJournal)
                        <span class="badge badge-credit">
                            <span class="status-dot"></span>
                            Journal : {{ Str::limit($selectedJournal->original_name, 20) }}
                        </span>
                    @endif

                    @if($period !== 'all')
                        <span class="badge badge-info">
                            <span class="status-dot"></span>
                            Période : {{ ucfirst(str_replace('_', ' ', $period)) }}
                        </span>
                    @endif

                    @if($dateFrom || $dateTo)
                        <span class="badge badge-neutral">
                            <span class="status-dot"></span>
                            Du {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : 'Début' }} au {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : 'Fin' }}
                        </span>
                    @endif

                    @if($type !== 'all')
                        <span class="badge badge-warning">
                            <span class="status-dot"></span>
                            Type : {{ ucfirst($type) }}
                        </span>
                    @endif

                    @if($paymentMode !== 'all')
                        <span class="badge badge-neutral">
                            <span class="status-dot"></span>
                            Paiement : {{ ucfirst(str_replace('_', ' ', $paymentMode)) }}
                        </span>
                    @endif

                    @if($mcfFilter !== 'all')
                        <span class="badge {{ $mcfFilter === 'with_error' ? 'badge-cancelled' : 'badge-sale' }}">
                            <span class="status-dot"></span>
                            MCF : {{ $mcfFilter === 'with_error' ? 'Erreurs' : 'Valide' }}
                        </span>
                    @endif
                </div>

                <div wire:loading style="font-size: 12px; color: var(--primary); font-weight: 600; display: flex; align-items: center; gap: 6px;">
                    <svg class="animate-spin" width="14" height="14" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Actualisation des données...</span>
                </div>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- SYNTHÈSE DYNAMIQUE DU PÉRIMÈTRE FILTRÉ (LIVE METRICS) --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <h4 style="margin: 0; font-size: 13px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 8px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Aperçu en Direct des Données Sélectionnées</span>
            </h4>
            <span style="font-size: 12px; color: var(--text-muted);">
                Données calculées en temps réel selon les filtres appliqués
            </span>
        </div>

        <div class="kpi-grid" style="margin-bottom: 0;">
            {{-- KPI 1: Total Transactions --}}
            <div class="kpi-card blue">
                <div class="kpi-icon-wrap blue">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="kpi-value">{{ number_format($filteredCount, 0, ',', ' ') }}</div>
                <div class="kpi-label">Transactions Émises</div>
                <div class="kpi-sub">
                    <span style="color: #059669; font-weight: 600;">{{ number_format($filteredSalesCount, 0, ',', ' ') }} ventes</span>
                    <span>•</span>
                    <span style="color: #2563EB; font-weight: 600;">{{ number_format($filteredCreditCount, 0, ',', ' ') }} avoirs</span>
                </div>
            </div>

            {{-- KPI 2: Total TTC --}}
            <div class="kpi-card green">
                <div class="kpi-icon-wrap green">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="kpi-value" style="font-size: 21px;">{{ number_format($filteredTtc, 2, ',', ' ') }} <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">CDF</span></div>
                <div class="kpi-label">Chiffre d'Affaires TTC Filtré</div>
                <div class="kpi-sub">
                    <span style="color: #059669; font-weight: 600;">Ventes brutes sélectionnées</span>
                </div>
            </div>

            {{-- KPI 3: Total TVA --}}
            <div class="kpi-card yellow">
                <div class="kpi-icon-wrap yellow">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="kpi-value" style="font-size: 21px;">{{ number_format($filteredTva, 2, ',', ' ') }} <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">CDF</span></div>
                <div class="kpi-label">TVA Collectée (16%)</div>
                <div class="kpi-sub">
                    <span style="color: #D97706; font-weight: 600;">Assiette fiscale auditable</span>
                </div>
            </div>

            {{-- KPI 4: Anomalies --}}
            <div class="kpi-card {{ $filteredAnomaliesCount > 0 ? 'red' : 'green' }}">
                <div class="kpi-icon-wrap {{ $filteredAnomaliesCount > 0 ? 'red' : 'green' }}">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="kpi-value">{{ number_format($filteredAnomaliesCount, 0, ',', ' ') }}</div>
                <div class="kpi-label">Anomalies & Écarts Détectés</div>
                <div class="kpi-sub">
                    @if($filteredAnomaliesCount === 0)
                        <span style="color: #059669; font-weight: 600;">Aucune anomalie fiscale</span>
                    @else
                        <span style="color: #DC2626; font-weight: 600;">Événements à régulariser</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- CARTES D'EXPORTS SELON LE PÉRIMÈTRE ACTIF --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}

    {{-- ── CAS 1 : EXPORT D'UN JOURNAL SPÉCIFIQUE ─────────────────────────────── --}}
    @if($selectedJournalId && $selectedJournal)
        <div class="card" style="margin-bottom: 24px; padding: 18px 24px; border-left: 4px solid var(--primary); background: #FFFFFF;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 18px; flex: 1;">
                    <div>
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 3px; letter-spacing: 0.05em;">Entreprise & NIF</span>
                        <span style="font-size: 13.5px; font-weight: 600; color: var(--text-main);">{{ $selectedJournal->device->pointOfSale->company->name ?? 'Miltex SARL' }}</span>
                        <span style="font-size: 11.5px; color: var(--text-muted); display: block; font-family: monospace;">NIF : {{ $selectedJournal->device->pointOfSale->company->nif ?? 'A1234567Z' }}</span>
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 3px; letter-spacing: 0.05em;">Point de Vente</span>
                        <span style="font-size: 13.5px; font-weight: 600; color: var(--text-main);">{{ $selectedJournal->device->pointOfSale->name ?? 'N/A' }}</span>
                        <span style="font-size: 11.5px; color: var(--text-muted); display: block;">{{ $selectedJournal->device->pointOfSale->city ?? 'Kinshasa' }}</span>
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 3px; letter-spacing: 0.05em;">Dispositif DEF (NID)</span>
                        <span style="font-size: 13.5px; font-weight: 700; font-family: monospace; color: var(--primary);">{{ $selectedJournal->device->nid ?? 'N/A' }}</span>
                        <span style="font-size: 11.5px; color: var(--text-muted); display: block;">{{ $selectedJournal->device->label ?? 'Caisse Principale' }}</span>
                    </div>
                    <div>
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 3px; letter-spacing: 0.05em;">Période du Journal</span>
                        <span style="font-size: 13.5px; font-weight: 600; color: var(--text-main);">
                            {{ $selectedJournal->start_date?->format('d/m/Y') ?? 'N/A' }} au {{ $selectedJournal->end_date?->format('d/m/Y') ?? 'N/A' }}
                        </span>
                        <span style="font-size: 11.5px; color: var(--text-muted); display: block;">Fichier : {{ Str::limit($selectedJournal->original_name, 22) }}</span>
                    </div>
                </div>

                <div>
                    <span class="badge badge-credit">
                        <span class="status-dot"></span>
                        Journal Individuel
                    </span>
                </div>
            </div>
        </div>

        {{-- Export Cards Grid (Journal) --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
            
            {{-- Card 1: Rapport Complet --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Fichier XLSX (Excel)
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Rapport Consolidé Complet</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Classeur Excel complet structuré en 4 feuilles : Synthèse fiscale, Grand livre exhaustif des ventes, Palmarès des articles vendus et Registre officiel des anomalies.
                    </p>
                </div>
                <a href="{{ route('export.full.excel', array_merge([$selectedJournal->id], $queryParams)) }}" class="btn btn-success" style="width: 100%; justify-content: center;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger le Rapport Excel Filtré</span>
                </a>
            </div>

            {{-- Card 2: Liste des Transactions --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Fichier XLSX (Excel)
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Grand Livre des Transactions</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Liste exhaustive de chaque facture émise, avoir et annulation. Contient les N° MCF, dates précises, acheteurs, modes de paiement et montants détaillés.
                    </p>
                </div>
                <a href="{{ route('export.invoices.excel', array_merge([$selectedJournal->id, $type], $queryParams)) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: #A7F3D0; color: #059669; background: #FFFFFF;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger les Écritures Filtrées</span>
                </a>
            </div>

            {{-- Card 3: Statistiques d'Articles --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Fichier XLSX (Excel)
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Palmarès des Ventes d'Articles</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Agrégation statistique par article et groupe de taxation. Affiche les volumes totaux vendus, le chiffre d'affaires HT, la TVA générée et les prix moyens.
                    </p>
                </div>
                <a href="{{ route('export.articles.excel', array_merge([$selectedJournal->id], $queryParams)) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: #A7F3D0; color: #059669; background: #FFFFFF;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger le Palmarès Articles</span>
                </a>
            </div>

            {{-- Card 4: Détail TVA Excel --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Fichier XLSX (Excel)
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Grand Livre Détaillé de la TVA</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Export exhaustif de chaque calcul de TVA pour chaque ligne de facture. Outil indispensable pour les contrôles fiscaux approfondis et la réconciliation.
                    </p>
                </div>
                <a href="{{ route('export.tva.excel', array_merge([$selectedJournal->id], $queryParams)) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: #A7F3D0; color: #059669; background: #FFFFFF;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger le Grand Livre TVA</span>
                </a>
            </div>

            {{-- Card 5: Synthèse de TVA PDF --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-credit">
                            <span class="status-dot"></span>
                            Fichier PDF Officiel
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Synthèse Mensuelle de la TVA</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        État récapitulatif certifié conforme DGI avec décomposition des bases taxables, TVA à 16%, exonérations et totaux TTC servant d'annexe à la déclaration.
                    </p>
                </div>
                <a href="{{ route('export.tva.pdf', array_merge([$selectedJournal->id], $queryParams)) }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger la Synthèse TVA (PDF)</span>
                </a>
            </div>

            {{-- Card 6: TVA Journalière PDF --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-credit">
                            <span class="status-dot"></span>
                            Fichier PDF Officiel
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Rapport TVA Jour par Jour</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Ventilation journalière des opérations de vente, de la taxe collectée et du nombre de transactions pour le suivi de la trésorerie et des encaissements.
                    </p>
                </div>
                <a href="{{ route('export.tva.daily.pdf', array_merge([$selectedJournal->id], $queryParams)) }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger la TVA Journalière (PDF)</span>
                </a>
            </div>

            {{-- Card 7: Audit & Conformité PDF (Full Width) --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; grid-column: 1 / -1; border-color: #BFDBFE; background: #F8FAFC;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                            <span class="badge badge-credit">
                                <span class="status-dot"></span>
                                Audit Réglementaire DGI
                            </span>
                            <span style="font-size: 12px; font-weight: 600; color: var(--primary);">Document à valeur probante</span>
                        </div>
                        <h4 style="margin: 0 0 8px 0; font-size: 16.5px; font-weight: 800; color: var(--text-main);">Dossier d'Audit de Conformité Fiscale DGI</h4>
                        <p style="margin: 0 0 16px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.6; max-width: 900px;">
                            Rapport d'audit officiel comprenant le contrôle d'intégrité des numéros de facture, le bilan de séquençage, l'audit de transmission MCF et le relevé exhaustif des anomalies détectées.
                        </p>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                </div>

                <a href="{{ route('export.compliance.pdf', array_merge([$selectedJournal->id], $queryParams)) }}" class="btn btn-primary" style="align-self: flex-start; padding: 11px 22px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger le Rapport d'Audit Complet (PDF)</span>
                </a>
            </div>

        </div>

    {{-- ── CAS 2 : EXPORT CONSOLIDÉ PAR POINT DE VENTE ───────────────────────── --}}
    @elseif($posId && !$selectedJournalId)
        @php
            $currentPos = \App\Models\PointOfSale::with('company')->find($posId);
        @endphp
        <div class="card" style="margin-bottom: 24px; padding: 18px 24px; border-left: 4px solid #059669; background: #ECFDF5; border-color: #A7F3D0;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #065F46; display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Rapports Consolidés : {{ $currentPos?->name }} ({{ $currentPos?->city ?? 'N/A' }})</span>
                    </h3>
                    <p style="margin: 4px 0 0 0; font-size: 12.5px; color: #047857;">
                        Tous les fichiers ci-dessous consolident l'ensemble des dispositifs DEF et journaux rattachés à ce point de vente selon vos filtres.
                    </p>
                </div>
                <span class="badge badge-sale">
                    <span class="status-dot"></span>
                    Consolidation Point de Vente
                </span>
            </div>
        </div>

        {{-- Export Cards Grid (POS Consolidated) --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
            
            {{-- Card 1: Rapport Complet Consolidé PDV --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Excel Consolidé PDV
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Rapport Complet Consolidé PDV</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Multi-feuilles complet regroupant toutes les caisses et dispositifs DEF de ce point de vente selon les dates et critères choisis.
                    </p>
                </div>
                <a href="{{ route('export.pos.full.excel', array_merge([$posId], $queryParams)) }}" class="btn btn-success" style="width: 100%; justify-content: center;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger le Rapport Consolidé PDV</span>
                </a>
            </div>

            {{-- Card 2: Transactions Consolidées PDV --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Grand Livre PDV (Excel)
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Toutes Transactions du Point de Vente</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Grand livre complet de toutes les transactions du site, avec indication du dispositif DEF émetteur pour chaque écriture.
                    </p>
                </div>
                <a href="{{ route('export.pos.invoices.excel', array_merge([$posId, $type], $queryParams)) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: #A7F3D0; color: #059669; background: #FFFFFF;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger les Transactions PDV</span>
                </a>
            </div>

            {{-- Card 3: Articles Consolidés PDV --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Articles PDV (Excel)
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Palmarès des Articles du Site</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Agrégation des ventes par article sur tout le magasin, quantités consolidées, CA HT et TVA globale générée.
                    </p>
                </div>
                <a href="{{ route('export.pos.articles.excel', array_merge([$posId], $queryParams)) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: #A7F3D0; color: #059669; background: #FFFFFF;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger les Articles du Site</span>
                </a>
            </div>

            {{-- Card 4: Détail TVA Consolidé PDV --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Grand Livre TVA (Excel)
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Détail des Calculs TVA du Site</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Export exhaustif de tous les calculs de taxe collectée ventilée par caisse pour les déclarations et la comptabilité.
                    </p>
                </div>
                <a href="{{ route('export.pos.tva.excel', array_merge([$posId], $queryParams)) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: #A7F3D0; color: #059669; background: #FFFFFF;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger le Détail TVA PDV</span>
                </a>
            </div>

            {{-- Card 5: Synthèse TVA PDV (PDF) --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-credit">
                            <span class="status-dot"></span>
                            PDF Officiel PDV
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Synthèse de TVA du Point de Vente</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        État officiel récapitulant les totaux HT, TVA et TTC du site pour la période sélectionnée, conforme DGI.
                    </p>
                </div>
                <a href="{{ route('export.pos.tva.pdf', array_merge([$posId], $queryParams)) }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger la Synthèse TVA PDV (PDF)</span>
                </a>
            </div>

            {{-- Card 6: TVA Journalière PDV (PDF) --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-credit">
                            <span class="status-dot"></span>
                            PDF Officiel PDV
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Rapport TVA Jour par Jour du PDV</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Chronologie journalière du chiffre d'affaires et de la TVA perçue sur l'ensemble des caisses du site.
                    </p>
                </div>
                <a href="{{ route('export.pos.tva.daily.pdf', array_merge([$posId], $queryParams)) }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger la TVA Journalière PDV (PDF)</span>
                </a>
            </div>

            {{-- Card 7: Audit PDV (Full Width) --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; grid-column: 1 / -1; border-color: #BFDBFE; background: #F8FAFC;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                            <span class="badge badge-credit">
                                <span class="status-dot"></span>
                                Audit Réglementaire DGI
                            </span>
                            <span style="font-size: 12px; font-weight: 600; color: var(--primary);">Consolidation de site certifiée</span>
                        </div>
                        <h4 style="margin: 0 0 8px 0; font-size: 16.5px; font-weight: 800; color: var(--text-main);">Rapport d'Audit de Conformité Fiscale du Point de Vente</h4>
                        <p style="margin: 0 0 16px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.6; max-width: 900px;">
                            Dossier d'audit recensant l'état de l'ensemble des dispositifs DEF du point de vente, le contrôle des séquences de factures et l'exhaustivité des anomalies fiscales enregistrées.
                        </p>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                </div>

                <a href="{{ route('export.pos.compliance.pdf', array_merge([$posId], $queryParams)) }}" class="btn btn-primary" style="align-self: flex-start; padding: 11px 22px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger le Rapport d'Audit PDV (PDF)</span>
                </a>
            </div>

        </div>

    {{-- ── CAS 3 : EXPORTS CONSOLIDÉS GLOBAUX / NATIONAUX (MULTI-POINTS DE VENTE) ── --}}
    @else
        <div class="card" style="margin-bottom: 24px; padding: 18px 24px; border-left: 4px solid #2563EB; background: #EFF6FF; border-color: #BFDBFE;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #1E40AF; display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" fill="none" stroke="#2563EB" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Rapports Consolidés Nationaux (Tous Points de Vente de l'Entreprise)</span>
                    </h3>
                    <p style="margin: 4px 0 0 0; font-size: 12.5px; color: #1D4ED8;">
                        Consolidation multi-sites de l'ensemble de votre réseau commercial, tous dispositifs DEF et tous journaux électroniques confondus.
                    </p>
                </div>
                <span class="badge badge-credit">
                    <span class="status-dot"></span>
                    Consolidation Globale Entreprise
                </span>
            </div>
        </div>

        {{-- Export Cards Grid (Global) --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
            
            {{-- Card 1: Rapport Complet Global --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Excel Consolidé National
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Rapport National Complet</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Grand classeur multi-onglets consolidant toutes les ventes, tous les articles, la TVA globale et l'audit d'anomalies de toute l'entreprise.
                    </p>
                </div>
                <a href="{{ route('export.global.full.excel', $queryParams) }}" class="btn btn-success" style="width: 100%; justify-content: center;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger le Rapport National (Excel)</span>
                </a>
            </div>

            {{-- Card 2: Transactions Globales --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Grand Livre National (Excel)
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Grand Livre National des Transactions</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Toutes les écritures de facturation de tous les points de vente avec colonnes PDV, DEF NID, client, mode de règlement et totaux.
                    </p>
                </div>
                <a href="{{ route('export.global.invoices.excel', array_merge([$type], $queryParams)) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: #A7F3D0; color: #059669; background: #FFFFFF;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger les Écritures Globales</span>
                </a>
            </div>

            {{-- Card 3: Articles Globaux --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Articles Global (Excel)
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Palmarès Global des Articles Vendus</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Statistiques consolidées de l'ensemble des ventes de produits au niveau de toute l'entreprise pour analyser la performance commerciale.
                    </p>
                </div>
                <a href="{{ route('export.global.articles.excel', $queryParams) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: #A7F3D0; color: #059669; background: #FFFFFF;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger le Palmarès Global</span>
                </a>
            </div>

            {{-- Card 4: Détail TVA Global --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-sale">
                            <span class="status-dot"></span>
                            Grand Livre TVA (Excel)
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #ECFDF5; color: #059669; border: 1px solid #D1FAE5; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Grand Livre National de la TVA</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Détail fiscal complet de toutes les lignes de taxe collectée sur l'ensemble du réseau, prêt pour l'audit et la télé-déclaration.
                    </p>
                </div>
                <a href="{{ route('export.global.tva.excel', $queryParams) }}" class="btn btn-ghost" style="width: 100%; justify-content: center; border-color: #A7F3D0; color: #059669; background: #FFFFFF;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger le Grand Livre TVA National</span>
                </a>
            </div>

            {{-- Card 5: Synthèse TVA Nationale (PDF) --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-credit">
                            <span class="status-dot"></span>
                            PDF National Officiel
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Synthèse Nationale de TVA (PDF)</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Document certifié officiel regroupant les montants imposables et la taxe due pour toute l'entreprise selon les critères temporels.
                    </p>
                </div>
                <a href="{{ route('export.global.tva.pdf', $queryParams) }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger la Synthèse TVA Globale (PDF)</span>
                </a>
            </div>

            {{-- Card 6: TVA Journalière Nationale (PDF) --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                        <span class="badge badge-credit">
                            <span class="status-dot"></span>
                            PDF National Officiel
                        </span>
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <h4 style="margin: 0 0 8px 0; font-size: 15.5px; font-weight: 700; color: var(--text-main);">Rapport TVA Journalier National</h4>
                    <p style="margin: 0 0 20px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.55;">
                        Tableau chronologique jour par jour consolidant tous les points de vente pour un suivi rigoureux de l'activité commerciale nationale.
                    </p>
                </div>
                <a href="{{ route('export.global.tva.daily.pdf', $queryParams) }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger la TVA Journalière Nationale (PDF)</span>
                </a>
            </div>

            {{-- Card 7: Audit National (Full Width) --}}
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; grid-column: 1 / -1; border-color: #BFDBFE; background: #F8FAFC;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                            <span class="badge badge-credit">
                                <span class="status-dot"></span>
                                Audit Réglementaire DGI
                            </span>
                            <span style="font-size: 12px; font-weight: 600; color: var(--primary);">Consolidation d'entreprise intégrale</span>
                        </div>
                        <h4 style="margin: 0 0 8px 0; font-size: 16.5px; font-weight: 800; color: var(--text-main);">Rapport Global d'Audit de Conformité Fiscale DGI</h4>
                        <p style="margin: 0 0 16px 0; font-size: 13px; color: var(--text-secondary); line-height: 1.6; max-width: 900px;">
                            Dossier de conformité légale officiel recensant l'inventaire complet des dispositifs DEF de l'entreprise, le registre d'intégrité de facturation et toutes les anomalies fiscales enregistrées.
                        </p>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                </div>

                <a href="{{ route('export.global.compliance.pdf', $queryParams) }}" class="btn btn-primary" style="align-self: flex-start; padding: 11px 22px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Télécharger le Rapport d'Audit National (PDF)</span>
                </a>
            </div>

        </div>
    @endif
</div>
