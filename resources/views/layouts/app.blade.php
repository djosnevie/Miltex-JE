<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Miltex EAJE') }} — @yield('title', 'Tableau de Bord')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --sidebar-width: 270px;
        }

        /* ── Sidebar Layout (Light Executive) ──────── */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: #FFFFFF;
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 50;
        }

        .sidebar-brand {
            padding: 24px 22px 20px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            gap: 12px;
            background: #FFFFFF;
        }

        .brand-logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
            flex-shrink: 0;
        }

        .brand-text-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .brand-text-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 5px;
            background: #EFF6FF;
            color: #2563EB;
            border: 1px solid #BFDBFE;
            letter-spacing: 0.05em;
        }

        .brand-tagline {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
            font-weight: 500;
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 14px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: #FFFFFF;
        }

        .nav-section {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-faint);
            padding: 14px 10px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s ease;
            position: relative;
        }

        .nav-item:hover {
            background: var(--surface-2);
            color: var(--text-main);
        }

        .nav-item.active {
            background: #EFF6FF;
            color: #1D4ED8;
            font-weight: 600;
            border: 1px solid #DBEAFE;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -14px;
            top: 6px;
            bottom: 6px;
            width: 3px;
            border-radius: 0 4px 4px 0;
            background: var(--primary);
        }

        .nav-item svg {
            width: 19px;
            height: 19px;
            flex-shrink: 0;
            transition: transform 0.15s ease;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border-subtle);
            background: #F8FAFC;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2563EB 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #FFFFFF;
            flex-shrink: 0;
        }

        .btn-logout {
            width: 100%;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            padding: 8px 12px;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.18s ease;
        }

        .btn-logout:hover {
            background: #FEE2E2;
            border-color: #FCA5A5;
            color: #B91C1C;
        }

        /* ── Main View Layout ──────────────────────── */
        .main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-canvas);
        }

        .topbar {
            height: 64px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.01em;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .date-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-secondary);
            background: var(--surface-2);
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid var(--border-subtle);
        }

        .content {
            flex: 1;
            padding: 32px;
            max-width: 1600px;
            width: 100%;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<!-- Sidebar Navigation -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo-icon">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <div class="brand-text-title">
                MILTEX
                <span class="brand-text-badge">EAJE</span>
            </div>
            <div class="brand-tagline">Audit & Journaux Électroniques</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Vue d'ensemble</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
            Tableau de Bord
        </a>

        @auth
            @if(auth()->user()->canImport())
            <a href="{{ route('journals.import') }}" class="nav-item {{ request()->routeIs('journals.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Importer un Journal
            </a>
            @endif
        @endauth

        <div class="nav-section">Audit & Analyse</div>
        <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Transactions
        </a>
        <a href="{{ route('anomalies.index') }}" class="nav-item {{ request()->routeIs('anomalies.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Anomalies</span>
            @php $unresolvedCount = \App\Models\Anomaly::unresolved()->count(); @endphp
            @if($unresolvedCount > 0)
                <span style="margin-left: auto; background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; font-size: 11px; font-weight: 700; padding: 1px 7px; border-radius: 9999px;">{{ $unresolvedCount }}</span>
            @endif
        </a>

        @auth
            @if(auth()->user()->canExport())
            <a href="{{ route('exports.index') }}" class="nav-item {{ request()->routeIs('exports.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Rapports & Exports
            </a>
            @endif

            @if(auth()->user()->canManagePointsOfSale() || auth()->user()->canManageUsers())
            <div class="nav-section">Configuration</div>
            @endif

            @if(auth()->user()->canManagePointsOfSale())
            <a href="{{ route('points-of-sale.index') }}" class="nav-item {{ request()->routeIs('points-of-sale.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Points de Vente & DEF
            </a>
            @endif

            @if(auth()->user()->canManageUsers())
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Utilisateurs
            </a>
            @endif

            @if(auth()->user()->isSuperAdmin())
            <div class="nav-section" style="color: #D97706; display: flex; align-items: center; justify-content: space-between; margin-top: 18px;">
                <span>Super Administration</span>
                <span style="background: #FEF3C7; color: #92400E; font-size: 9.5px; font-weight: 700; padding: 1px 6px; border-radius: 4px; border: 1px solid #FDE68A;">GLOBAL</span>
            </div>
            <a href="{{ route('superadmin.index') }}" class="nav-item {{ request()->routeIs('superadmin.*') ? 'active' : '' }}" style="{{ request()->routeIs('superadmin.*') ? 'border-color: #D97706; color: #B45309; background: #FFFBEB;' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color: #D97706;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Console SuperAdmin</span>
            </a>
            @endif

            <div class="nav-section" style="margin-top: 18px;">Mon Compte</div>
            <a href="{{ route('profile.show') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Mon Profil</span>
            </a>
        @endauth
    </nav>

    {{-- User Profile Footer --}}
    @auth
    <div class="sidebar-footer">
        <a href="{{ route('profile.show') }}" class="user-card" style="text-decoration: none; padding: 6px; border-radius: 10px; margin: -6px -6px 10px; transition: background 0.15s ease;" onmouseover="this.style.background='rgba(0,0,0,0.03)'" onmouseout="this.style.background='transparent'">
            <div class="user-avatar" style="{{ auth()->user()->isSuperAdmin() ? 'background: linear-gradient(135deg, #D97706 0%, #B45309 100%);' : '' }}">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div style="overflow: hidden; flex: 1;">
                <div style="font-size: 13px; font-weight: 700; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name }}</div>
                <div style="font-size: 11px; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                    <span>{{ auth()->user()->roleBadge() }}</span>
                    <span style="font-size: 10px; color: var(--primary, #2563EB); font-weight: 700;">Profil &rarr;</span>
                </div>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Déconnexion
            </button>
        </form>
    </div>
    @endauth
</aside>

<!-- Main Workspace -->
<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <h1 class="topbar-title">@yield('title', 'Tableau de Bord')</h1>
        </div>
        <div class="topbar-right">
            @auth
            @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('superadmin.index') }}" style="display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 700; color: #92400E; background: #FEF3C7; border: 1px solid #FDE68A; padding: 5px 12px; border-radius: 9999px; text-decoration: none; transition: all 0.15s ease;" onmouseover="this.style.background='#FDE68A'" onmouseout="this.style.background='#FEF3C7'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Console SuperAdmin</span>
            </a>
            @endif
            @endauth

            <div class="date-chip">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ now()->translatedFormat('d M Y') }}</span>
            </div>
            @auth
            <a href="{{ route('profile.show') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; padding: 4px 8px; border-radius: 10px; transition: background 0.15s ease;" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='transparent'">
                <div style="text-align: right; line-height: 1.25;">
                    <div style="font-size: 12.5px; font-weight: 700; color: var(--text-main);">{{ auth()->user()->name }}</div>
                    <div style="font-size: 10.5px; color: var(--text-muted); font-weight: 500;">{{ auth()->user()->roleBadge() }}</div>
                </div>
                <div class="user-avatar" style="width: 32px; height: 32px; font-size: 12px; {{ auth()->user()->isSuperAdmin() ? 'background: linear-gradient(135deg, #D97706 0%, #B45309 100%);' : '' }}">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </a>
            @endauth
        </div>
    </header>

    <main class="content">
        @yield('content')
        {{ $slot ?? '' }}
    </main>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
