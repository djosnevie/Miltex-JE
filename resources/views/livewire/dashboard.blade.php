@section('title', 'Tableau de Bord')

<div>
    {{-- Filters Bar --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 18px;">🔍</span>
                <h3 style="margin: 0; font-size: 15px; font-weight: 600;">Filtres d'Analyse</h3>
            </div>
            @if($hasFilters)
                <button wire:click="clearFilters" class="btn btn-ghost btn-sm" style="color: var(--danger); border-color: rgba(239,68,68,0.2);">
                    Réinitialiser les filtres
                </button>
            @endif
        </div>
        
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            {{-- Point of Sale Select --}}
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 11px; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase;">Point de Vente</label>
                <select wire:model.live="posId" style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); color: var(--text); padding: 8px 12px; border-radius: 8px; font-size: 13px; outline: none;">
                    <option value="">Tous les points de vente (Consolidé)</option>
                    @foreach($pointsOfSale as $pos)
                        <option value="{{ $pos->id }}">{{ $pos->name }} ({{ $pos->city ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Device Select --}}
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 11px; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase;">Matériel DEF</label>
                <select wire:model.live="deviceId" style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); color: var(--text); padding: 8px 12px; border-radius: 8px; font-size: 13px; outline: none;">
                    <option value="">Tous les matériels</option>
                    @foreach($devices as $dev)
                        <option value="{{ $dev->id }}">{{ $dev->nid }} @if($dev->model) — {{ $dev->model }}@endif</option>
                    @endforeach
                </select>
            </div>

            {{-- Journal Select --}}
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 11px; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase;">Journal</label>
                <select wire:model.live="selectedJournalId" style="width: 100%; background: var(--surface-3); border: 1px solid var(--border); color: var(--text); padding: 8px 12px; border-radius: 8px; font-size: 13px; outline: none;">
                    <option value="">Tous les journaux</option>
                    @foreach($journals as $j)
                        <option value="{{ $j->id }}">{{ $j->original_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Context Banner --}}
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.15); padding: 12px 20px; border-radius: 10px;">
        <span style="font-size: 13px; color: var(--muted);">
            Données analysées : 
            <strong style="color: var(--accent);">
                @if($selectedJournalId)
                    Journal spécifique ({{ \App\Models\Journal::find($selectedJournalId)?->original_name }})
                @elseif($deviceId)
                    Matériel DEF ({{ \App\Models\Device::find($deviceId)?->nid }})
                @elseif($posId)
                    Point de Vente ({{ \App\Models\PointOfSale::find($posId)?->name }})
                @else
                    Global Enterprise (Consolidé)
                @endif
            </strong>
        </span>
        @if(!$selectedJournalId && $posId)
            <span style="font-size: 11px; background: rgba(16, 185, 129, 0.15); color: #34D399; padding: 2px 8px; border-radius: 20px;">Mode PDV Actif</span>
        @endif
    </div>

    {{-- KPI Grid --}}
    <div class="kpi-grid">
        <div class="kpi-card blue">
            <div class="kpi-icon blue">
                <svg width="22" height="22" fill="none" stroke="#3B82F6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-value">{{ number_format($totalTtc, 0, ',', ' ') }} CDF</div>
            <div class="kpi-label">CA TTC</div>
            <div class="kpi-sub">Total des ventes</div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-icon green">
                <svg width="22" height="22" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            </div>
            <div class="kpi-value">{{ number_format($totalTva, 0, ',', ' ') }} CDF</div>
            <div class="kpi-label">TVA Collectée</div>
            <div class="kpi-sub">Taux légal : 16%</div>
        </div>
        <div class="kpi-card yellow">
            <div class="kpi-icon yellow">
                <svg width="22" height="22" fill="none" stroke="#F59E0B" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="kpi-value">{{ number_format($totalInvoices, 0, ',', ' ') }}</div>
            <div class="kpi-label">Factures de Vente</div>
            <div class="kpi-sub">{{ $totalCancelled }} annulée(s)</div>
        </div>
        <div class="kpi-card {{ $anomalyCount > 0 ? 'red' : 'green' }}">
            <div class="kpi-icon {{ $anomalyCount > 0 ? 'red' : 'green' }}">
                <svg width="22" height="22" fill="none" stroke="{{ $anomalyCount > 0 ? '#EF4444' : '#10B981' }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="kpi-value">{{ $anomalyCount }}</div>
            <div class="kpi-label">Anomalies Actives</div>
            <div class="kpi-sub">{{ $anomalyCount > 0 ? 'Action requise' : 'Tout est conforme ✓' }}</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:24px;">
        {{-- Daily Sales Chart --}}
        <div class="card" wire:ignore id="daily-chart-card">
            <div class="card-title">Évolution Journalière du CA (CDF TTC)</div>
            <div id="dailyChart"></div>
        </div>
        {{-- Hourly heatmap --}}
        <div class="card" wire:ignore id="hourly-chart-card">
            <div class="card-title">Activité par Heure</div>
            <div id="hourlyChart"></div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px;">
        {{-- Top Articles --}}
        <div class="card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                <div class="card-title" style="margin:0;">Top Articles (CA TTC)</div>
                @if($journal)
                    <a href="{{ route('export.articles.excel', $journal->id) }}" class="btn btn-ghost btn-sm">↓ Excel</a>
                @elseif($posId)
                    <a href="{{ route('export.pos.articles.excel', $posId) }}" class="btn btn-ghost btn-sm">↓ Excel PDV</a>
                @endif
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Article</th><th>Qté</th><th style="text-align:right">CA (CDF)</th></tr></thead>
                    <tbody>
                        @forelse($topArticles as $article)
                        <tr>
                            <td>{{ $article['name'] }}</td>
                            <td>{{ number_format($article['qty'], 0, ',', ' ') }}</td>
                            <td style="text-align:right; font-weight:600;">{{ number_format($article['revenue'], 0, ',', ' ') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align:center; padding: 20px; color: var(--muted);">Aucune transaction de vente</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Anomalies --}}
        <div class="card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                <div class="card-title" style="margin:0;">Anomalies Récentes</div>
                <a href="{{ route('anomalies.index') }}" class="btn btn-ghost btn-sm">Voir tout</a>
            </div>
            @if($recentAnomalies->isEmpty())
                <div style="text-align:center; color:#10B981; padding:24px; font-size:13px;">✓ Aucune anomalie non résolue</div>
            @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Sévérité</th><th>Description</th></tr></thead>
                    <tbody>
                        @foreach($recentAnomalies as $anomaly)
                        <tr>
                            <td>
                                <span class="badge badge-{{ $anomaly->severity }}">
                                    {{ strtoupper($anomaly->severity) }}
                                </span>
                            </td>
                            <td style="font-size:12px;">{{ Str::limit($anomaly->description, 60) }}</td>
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
        <div class="card-title">Option d'Exportation de Données</div>
        
        @if($journal)
            {{-- Single Journal Context --}}
            <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 8px; border: 1px solid var(--border); margin-bottom: 12px;">
                <h4 style="margin: 0 0 8px 0; font-size: 13px; color: var(--text);">Rapports pour le Journal : <span style="color: var(--accent);">{{ $journal->original_name }}</span></h4>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a href="{{ route('export.invoices.excel', [$journal->id, 'all']) }}" class="btn btn-ghost btn-sm">📊 Toutes les transactions (Excel)</a>
                    <a href="{{ route('export.invoices.excel', [$journal->id, 'sale']) }}" class="btn btn-ghost btn-sm">📗 Ventes (Excel)</a>
                    <a href="{{ route('export.articles.excel', $journal->id) }}" class="btn btn-ghost btn-sm">📦 Articles (Excel)</a>
                    <a href="{{ route('export.full.excel', $journal->id) }}" class="btn btn-success btn-sm">📁 Rapport Complet (Excel)</a>
                    <a href="{{ route('export.compliance.pdf', $journal->id) }}" class="btn btn-primary btn-sm">🛡 Conformité DGI (PDF)</a>
                    <a href="{{ route('export.tva.pdf', $journal->id) }}" class="btn btn-primary btn-sm">🧾 Synthèse TVA (PDF)</a>
                    <a href="{{ route('export.tva.daily.pdf', $journal->id) }}" class="btn btn-primary btn-sm">📅 TVA Journalière (PDF)</a>
                    <a href="{{ route('export.tva.excel', $journal->id) }}" class="btn btn-ghost btn-sm">🧾 Détail TVA (Excel)</a>
                </div>
            </div>
        @endif

        @if($posId)
            {{-- Consolidated Point of Sale Context --}}
            <div style="background: rgba(16, 185, 129, 0.04); padding: 16px; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.15);">
                <h4 style="margin: 0 0 8px 0; font-size: 13px; color: var(--text);">Rapports Consolidés pour le Point de Vente : <span style="color: var(--success);">{{ \App\Models\PointOfSale::find($posId)?->name }}</span></h4>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a href="{{ route('export.pos.invoices.excel', [$posId, 'all']) }}" class="btn btn-ghost btn-sm">📊 Transactions PDV (Excel)</a>
                    <a href="{{ route('export.pos.invoices.excel', [$posId, 'sale']) }}" class="btn btn-ghost btn-sm">📗 Ventes PDV (Excel)</a>
                    <a href="{{ route('export.pos.articles.excel', $posId) }}" class="btn btn-ghost btn-sm">📦 Articles PDV (Excel)</a>
                    <a href="{{ route('export.pos.full.excel', $posId) }}" class="btn btn-success btn-sm">📁 Rapport Complet PDV (Excel)</a>
                    <a href="{{ route('export.pos.tva.excel', $posId) }}" class="btn btn-ghost btn-sm">🧾 Détail TVA PDV (Excel)</a>
                </div>
            </div>
        @elseif(!$journal)
            <div style="padding: 12px; background: rgba(255,255,255,0.02); border-radius: 8px; text-align: center; color: var(--muted); font-size: 13px;">
                💡 <em>Sélectionnez un Point de Vente ou un Journal dans les filtres ci-dessus pour accéder aux options de téléchargement d'exports Excel/PDF.</em>
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

    // Re-initialize charts on Livewire updates
    window.addEventListener('charts-updated', () => {
        initCharts();
    });

    function initCharts() {
        const dailyData = @json($dailySales ?? []);
        const hourlyData = @json($hourlySales ?? []);

        // Destroy existing instances if they exist
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
                chart: { type: 'area', height: 220, toolbar: { show: false }, background: 'transparent', foreColor: '#94A3B8' },
                series: [{ name: 'CA TTC (CDF)', data: dailyData.map(d => ({ x: d.date, y: d.total })) }],
                xaxis: { type: 'datetime', labels: { style: { colors: '#94A3B8', fontSize: '11px' } } },
                yaxis: { labels: { formatter: v => v >= 1000 ? (v/1000).toFixed(0)+'K' : v, style: { colors: '#94A3B8' } } },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02 } },
                stroke: { curve: 'smooth', width: 2.5 },
                colors: ['#3B82F6'],
                grid: { borderColor: 'rgba(255,255,255,0.06)' },
                tooltip: { theme: 'dark', y: { formatter: v => new Intl.NumberFormat('fr-CD').format(v) + ' CDF' } },
            });
            dailyChart.render();
        } else if (dailyEl) {
            dailyEl.innerHTML = '<div style="text-align:center; padding: 80px 0; color: var(--muted); font-size:13px;">Aucune donnée de vente pour cette période</div>';
        }

        // Hourly bar chart
        const hourlyEl = document.getElementById('hourlyChart');
        if (hourlyEl) {
            const hours = Array.from({length:24}, (_,i) => i+'h');
            const counts = hours.map((_,i) => hourlyData[i] ?? 0);
            
            // Check if we have any data to show
            const hasData = counts.some(c => c > 0);
            
            if (hasData) {
                hourlyChart = new ApexCharts(hourlyEl, {
                    chart: { type: 'bar', height: 220, toolbar: { show: false }, background: 'transparent', foreColor: '#94A3B8' },
                    series: [{ name: 'Factures', data: counts }],
                    xaxis: { categories: hours, labels: { style: { fontSize: '10px', colors: Array(24).fill('#94A3B8') } } },
                    yaxis: { labels: { style: { colors: '#94A3B8' } } },
                    colors: ['#10B981'],
                    grid: { borderColor: 'rgba(255,255,255,0.06)' },
                    plotOptions: { bar: { borderRadius: 4 } },
                    tooltip: { theme: 'dark' },
                });
                hourlyChart.render();
            } else {
                hourlyEl.innerHTML = '<div style="text-align:center; padding: 80px 0; color: var(--muted); font-size:13px;">Aucune donnée d\'activité horaire</div>';
            }
        }
    }
</script>
@endpush
