<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Miltex EAJE') }} — @yield('title', 'Tableau de Bord')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        :root {
            --primary:   #1E3A5F;
            --accent:    #3B82F6;
            --success:   #10B981;
            --warning:   #F59E0B;
            --danger:    #EF4444;
            --surface:   #0F172A;
            --surface-2: #1E293B;
            --surface-3: #334155;
            --text:      #F1F5F9;
            --muted:     #94A3B8;
            --border:    rgba(255,255,255,.08);
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; font-family: 'Inter', sans-serif; background: var(--surface); color: var(--text); }

        /* ── Sidebar ──────────────────────────────── */
        .sidebar { width: 260px; min-height: 100vh; background: var(--surface-2); border-right: 1px solid var(--border); display: flex; flex-direction: column; position: fixed; top:0; left:0; z-index:50; }
        .sidebar-logo { padding: 24px 20px; border-bottom: 1px solid var(--border); }
        .sidebar-logo .brand { font-size: 20px; font-weight: 700; color: var(--text); letter-spacing: -.3px; }
        .sidebar-logo .tagline { font-size: 11px; color: var(--muted); margin-top: 2px; }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: var(--muted); padding: 12px 8px 4px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 8px; text-decoration: none; color: var(--muted); font-size: 13.5px; transition: all .18s; margin-bottom: 2px; }
        .nav-item:hover, .nav-item.active { background: rgba(59,130,246,.12); color: var(--text); }
        .nav-item.active { color: #60A5FA; font-weight: 500; }
        .nav-item svg { width: 18px; height: 18px; flex-shrink: 0; }

        /* ── Main ─────────────────────────────────── */
        .main { margin-left: 260px; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { height: 60px; background: var(--surface-2); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; position: sticky; top: 0; z-index: 40; }
        .topbar-title { font-size: 16px; font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; }

        .content { flex: 1; padding: 28px; }

        /* ── Cards ────────────────────────────────── */
        .card { background: var(--surface-2); border: 1px solid var(--border); border-radius: 14px; padding: 20px 24px; }
        .card-title { font-size: 13px; font-weight: 600; color: var(--muted); margin-bottom: 16px; text-transform: uppercase; letter-spacing: .5px; }

        /* ── KPI Cards ────────────────────────────── */
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .kpi-card { background: var(--surface-2); border: 1px solid var(--border); border-radius: 14px; padding: 20px; position: relative; overflow: hidden; transition: transform .2s, border-color .2s; }
        .kpi-card:hover { transform: translateY(-2px); border-color: rgba(255,255,255,.16); }
        .kpi-card::before { content: ''; position: absolute; top:0; left:0; right:0; height: 3px; }
        .kpi-card.blue::before   { background: linear-gradient(90deg, #3B82F6, #60A5FA); }
        .kpi-card.green::before  { background: linear-gradient(90deg, #10B981, #34D399); }
        .kpi-card.yellow::before { background: linear-gradient(90deg, #F59E0B, #FCD34D); }
        .kpi-card.red::before    { background: linear-gradient(90deg, #EF4444, #F87171); }
        .kpi-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
        .kpi-icon.blue   { background: rgba(59,130,246,.15); }
        .kpi-icon.green  { background: rgba(16,185,129,.15); }
        .kpi-icon.yellow { background: rgba(245,158,11,.15); }
        .kpi-icon.red    { background: rgba(239,68,68,.15); }
        .kpi-value { font-size: 22px; font-weight: 700; line-height: 1.2; }
        .kpi-label { font-size: 12px; color: var(--muted); margin-top: 3px; }
        .kpi-sub   { font-size: 11px; color: var(--muted); margin-top: 8px; }

        /* ── Tables ───────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; color: var(--muted); padding: 10px 14px; border-bottom: 1px solid var(--border); text-align: left; }
        tbody td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid var(--border); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: rgba(255,255,255,.03); }

        /* ── Badges ───────────────────────────────── */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }
        .badge-sale     { background: rgba(16,185,129,.15); color: #34D399; }
        .badge-credit   { background: rgba(59,130,246,.15);  color: #60A5FA; }
        .badge-cancelled{ background: rgba(239,68,68,.15);   color: #F87171; }
        .badge-critical { background: rgba(239,68,68,.15);   color: #F87171; }
        .badge-warning  { background: rgba(245,158,11,.15);  color: #FCD34D; }
        .badge-info     { background: rgba(59,130,246,.15);  color: #60A5FA; }

        /* ── Buttons ──────────────────────────────── */
        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; border-radius: 9px; font-size: 13px; font-weight: 500; cursor: pointer; border: none; transition: all .18s; text-decoration: none; }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: #2563EB; }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #059669; }
        .btn-ghost { background: transparent; color: var(--muted); border: 1px solid var(--border); }
        .btn-ghost:hover { color: var(--text); border-color: rgba(255,255,255,.2); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* ── Upload Zone ──────────────────────────── */
        .upload-zone { border: 2px dashed var(--border); border-radius: 14px; padding: 40px; text-align: center; transition: all .2s; cursor: pointer; }
        .upload-zone:hover, .upload-zone.dragover { border-color: var(--accent); background: rgba(59,130,246,.05); }

        /* ── Alerts ───────────────────────────────── */
        .alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.3); color: #34D399; }
        .alert-error   { background: rgba(239,68,68,.12);  border: 1px solid rgba(239,68,68,.3);  color: #F87171; }
        .alert-info    { background: rgba(59,130,246,.12); border: 1px solid rgba(59,130,246,.3); color: #60A5FA; }

        /* ── Progress bar ─────────────────────────── */
        .progress-bar { height: 4px; background: var(--surface-3); border-radius: 2px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, var(--accent), #818CF8); transition: width .4s; border-radius: 2px; }

        /* ── Scrollbar ────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--surface-3); border-radius: 3px; }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="brand">⚡ Miltex EAJE</div>
        <div class="tagline">Analyse des Journaux Électroniques</div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
            Tableau de Bord
        </a>
        <a href="{{ route('journals.import') }}" class="nav-item {{ request()->routeIs('journals.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Importer un Journal
        </a>

        <div class="nav-section">Analyse</div>
        <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Transactions
        </a>
        <a href="{{ route('anomalies.index') }}" class="nav-item {{ request()->routeIs('anomalies.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Anomalies
            @php $unresolvedCount = \App\Models\Anomaly::unresolved()->count(); @endphp
            @if($unresolvedCount > 0)
                <span style="margin-left:auto; background:#EF4444; color:white; font-size:10px; font-weight:700; padding:2px 7px; border-radius:10px;">{{ $unresolvedCount }}</span>
            @endif
        </a>
        <a href="{{ route('exports.index') }}" class="nav-item {{ request()->routeIs('exports.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Rapports & Exports
        </a>
    </nav>
</aside>

<!-- Main Content -->
<div class="main">
    <header class="topbar">
        <div class="topbar-title">@yield('title', 'Tableau de Bord')</div>
        <div class="topbar-right">
            <span style="font-size:12px; color:var(--muted);">{{ now()->format('d/m/Y') }}</span>
            <div class="avatar">M</div>
        </div>
    </header>
    <main class="content">
        @yield('content')
        {{ $slot ?? '' }}
    </main>
</div>

@livewireScripts
</body>
</html>
