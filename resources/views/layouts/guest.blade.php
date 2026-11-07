<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion — Miltex EAJE</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #F8FAFC;
            --surface: #FFFFFF;
            --surface-elevated: #F8FAFC;
            --border: #E2E8F0;
            --border-hover: #CBD5E1;
            --text: #0F172A;
            --text-secondary: #475569;
            --text-muted: #64748B;
            --accent: #2563EB;
            --accent-glow: rgba(37, 99, 235, 0.18);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            padding: 24px;
        }

        /* Ambient background glow & grid */
        .ambient-glow-1 {
            position: fixed;
            top: -20%;
            left: 20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            filter: blur(50px);
        }

        .ambient-glow-2 {
            position: fixed;
            bottom: -20%;
            right: 15%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.07) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            filter: blur(50px);
        }

        .ambient-grid {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(15, 23, 42, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(15, 23, 42, 0.03) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
        }

        /* Login Card */
        .auth-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 44px 38px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
            box-shadow: 
                0 20px 45px -10px rgba(15, 23, 42, 0.08),
                0 4px 6px -2px rgba(15, 23, 42, 0.03);
            animation: cardAppear 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Brand Header */
        .auth-brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-icon-box {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            color: #FFFFFF;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.25);
        }

        .brand-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .brand-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 6px;
            background: #EFF6FF;
            color: #2563EB;
            border: 1px solid #BFDBFE;
            letter-spacing: 0.04em;
        }

        .brand-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 6px;
            font-weight: 500;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            width: 18px;
            height: 18px;
            pointer-events: none;
            z-index: 5;
            transition: color 0.18s ease;
        }

        .input-wrap:focus-within .input-icon {
            color: #2563EB;
        }

        .auth-card input.auth-input,
        .input-wrap input.auth-input,
        .auth-input {
            width: 100% !important;
            height: 48px !important;
            background: #FFFFFF !important;
            border: 1.5px solid #CBD5E1 !important;
            color: #0F172A !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            padding-left: 44px !important;
            padding-right: 14px !important;
            border-radius: 12px !important;
            font-size: 14.5px !important;
            font-weight: 500 !important;
            font-family: inherit !important;
            outline: none !important;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
            line-height: normal !important;
        }

        .auth-card input.auth-input-password,
        .input-wrap input.auth-input-password {
            padding-right: 44px !important;
        }

        .auth-card input.auth-input:focus,
        .input-wrap input.auth-input:focus,
        .auth-input:focus {
            background: #FFFFFF !important;
            border-color: #2563EB !important;
            box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.15) !important;
        }

        .auth-card input.auth-input::placeholder,
        .auth-input::placeholder {
            color: #94A3B8 !important;
            font-weight: 400 !important;
        }

        /* Password toggle button */
        .password-toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: transparent;
            border: none;
            color: #64748B;
            border-radius: 8px;
            cursor: pointer;
            z-index: 5;
            transition: color 0.15s ease, background 0.15s ease;
            padding: 0;
        }

        .password-toggle-btn:hover {
            color: #0F172A;
            background: #F1F5F9;
        }

        /* Chrome / Safari / Edge autofill fix */
        .auth-card input:-webkit-autofill,
        .auth-card input:-webkit-autofill:hover, 
        .auth-card input:-webkit-autofill:focus, 
        .auth-card input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #FFFFFF inset !important;
            -webkit-text-fill-color: #0F172A !important;
            font-size: 14.5px !important;
            font-weight: 500 !important;
            font-family: inherit !important;
            border-radius: 12px !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        [x-cloak] {
            display: none !important;
        }

        .error-banner {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            color: #991B1B;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .field-error-text {
            font-size: 12px;
            color: #DC2626;
            margin-top: 6px;
            font-weight: 600;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 24px;
            cursor: pointer;
            user-select: none;
        }

        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
            border-radius: 4px;
        }

        .remember-row span {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .btn-submit {
            width: 100%;
            background: #2563EB;
            color: #FFFFFF;
            border: none;
            padding: 13px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-text {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        [wire\:loading], [wire\:loading\.delay], [wire\:loading\.flex] {
            display: none !important;
        }

        .btn-submit:hover {
            background: #1D4ED8;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .spinner-icon {
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        .auth-footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            font-size: 11.5px;
            color: var(--text-muted);
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="ambient-grid"></div>
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="auth-card">
        <div class="auth-brand">
            <div class="brand-icon-box">
                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h1 class="brand-title">
                MILTEX
                <span class="brand-badge">EAJE</span>
            </h1>
            <p class="brand-subtitle">Analyse et Audit des Journaux Électroniques</p>
        </div>

        {{ $slot }}

        <div class="auth-footer">
            Système réservé aux opérateurs et auditeurs autorisés<br>
            © {{ date('Y') }} Miltex SARL · Plateforme de conformité fiscale
        </div>
    </div>

    @livewireScripts
</body>
</html>
