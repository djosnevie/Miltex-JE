@section('title', 'Tableau de Bord')

<div>
    {{-- Journal Selector --}}
    @if($journals->isNotEmpty())
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px; flex-wrap:wrap;">
        <span style="font-size:13px; color:var(--muted);">Journal :</span>
        @foreach($journals as $j)
            <button wire:click="selectJournal({{ $j->id }})"
                class="btn btn-sm {{ $selectedJournalId == $j->id ? 'btn-primary' : 'btn-ghost' }}">
                {{ $j->original_name }}
            </button>
        @endforeach
    </div>
    @endif

    @if(!$journal)
        <div class="alert alert-info">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Aucun journal importé. <a href="{{ route('journals.import') }}" style="color:inherit; font-weight:600; margin-left:6px;">Importer un journal →</a>
        </div>
    @else

    {{-- KPI Grid --}}
    <div class="kpi-grid">
        <div class="kpi-card blue">
            <div class="kpi-icon blue">
                <svg width="22" height="22" fill="none" stroke="#3B82F6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-value">{{ number_format($totalTtc, 0, ',', ' ') }}</div>
            <div class="kpi-label">CA TTC (CDF)</div>
            <div class="kpi-sub">{{ $journal->start_date?->format('d/m/Y') }} → {{ $journal->end_date?->format('d/m/Y') }}</div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-icon green">
                <svg width="22" height="22" fill="none" stroke="#10B981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            </div>
            <div class="kpi-value">{{ number_format($totalTva, 0, ',', ' ') }}</div>
            <div class="kpi-label">TVA Collectée (CDF)</div>
            <div class="kpi-sub">Taux : 16%</div>
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
            <div class="kpi-label">Anomalies Non Résolues</div>
            <div class="kpi-sub">{{ $anomalyCount > 0 ? 'Action requise' : 'Tout est conforme ✓' }}</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:24px;">
        {{-- Daily Sales Chart --}}
        <div class="card">
            <div class="card-title">Évolution Journalière du CA (CDF TTC)</div>
            <div id="dailyChart"></div>
        </div>
        {{-- Hourly heatmap --}}
        <div class="card">
            <div class="card-title">Activité par Heure</div>
            <div id="hourlyChart"></div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        {{-- Top Articles --}}
        <div class="card">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                <div class="card-title" style="margin:0;">Top Articles (CA TTC)</div>
                <a href="{{ route('export.articles.excel', $journal->id) }}" class="btn btn-ghost btn-sm">↓ Excel</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Article</th><th>Qté</th><th style="text-align:right">CA (CDF)</th></tr></thead>
                    <tbody>
                        @foreach($topArticles as $article)
                        <tr>
                            <td>{{ $article['name'] }}</td>
                            <td>{{ number_format($article['qty'], 0, ',', ' ') }}</td>
                            <td style="text-align:right; font-weight:600;">{{ number_format($article['revenue'], 0, ',', ' ') }}</td>
                        </tr>
                        @endforeach
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

    {{-- Exports rapides --}}
    <div class="card" style="margin-top:16px;">
        <div class="card-title">Exports Rapides</div>
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
</div>

@push('scripts')
<script>
document.addEventListener('livewire:navigated', () => initCharts());
document.addEventListener('DOMContentLoaded', () => initCharts());

function initCharts() {
    const dailyData = @json($dailySales ?? []);
    const hourlyData = @json($hourlySales ?? []);

    // Daily Chart
    const dailyEl = document.getElementById('dailyChart');
    if (dailyEl && dailyData.length > 0) {
        new ApexCharts(dailyEl, {
            chart: { type: 'area', height: 220, toolbar: { show: false }, background: 'transparent', foreColor: '#94A3B8' },
            series: [{ name: 'CA TTC (CDF)', data: dailyData.map(d => ({ x: d.date, y: d.total })) }],
            xaxis: { type: 'datetime', labels: { style: { colors: '#94A3B8', fontSize: '11px' } } },
            yaxis: { labels: { formatter: v => v >= 1000 ? (v/1000).toFixed(0)+'K' : v, style: { colors: '#94A3B8' } } },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02 } },
            stroke: { curve: 'smooth', width: 2.5 },
            colors: ['#3B82F6'],
            grid: { borderColor: 'rgba(255,255,255,0.06)' },
            tooltip: { theme: 'dark', y: { formatter: v => new Intl.NumberFormat('fr-CD').format(v) + ' CDF' } },
        }).render();
    }

    // Hourly bar chart
    const hourlyEl = document.getElementById('hourlyChart');
    if (hourlyEl) {
        const hours = Array.from({length:24}, (_,i) => i+'h');
        const counts = hours.map((_,i) => hourlyData[i] ?? 0);
        new ApexCharts(hourlyEl, {
            chart: { type: 'bar', height: 220, toolbar: { show: false }, background: 'transparent', foreColor: '#94A3B8' },
            series: [{ name: 'Factures', data: counts }],
            xaxis: { categories: hours, labels: { style: { fontSize: '10px', colors: Array(24).fill('#94A3B8') } } },
            yaxis: { labels: { style: { colors: '#94A3B8' } } },
            colors: ['#10B981'],
            grid: { borderColor: 'rgba(255,255,255,0.06)' },
            plotOptions: { bar: { borderRadius: 4 } },
            tooltip: { theme: 'dark' },
        }).render();
    }
}
</script>
@endpush
