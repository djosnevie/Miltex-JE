<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Miltex EAJE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0A0D14;
            --surface:   #111827;
            --surface-2: #1A2235;
            --border:    rgba(255,255,255,0.08);
            --text:      #F1F5F9;
            --muted:     #6B7280;
            --accent:    #3B82F6;
            --accent-glow: rgba(59,130,246,0.25);
            --success:   #10B981;
            --danger:    #EF4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background orbs */
        body::before {
            content: '';
            position: fixed;
            top: -30%;
            left: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -20%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,0.08) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }

        /* Card */
        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            box-shadow:
                0 25px 60px rgba(0,0,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.05);
            animation: slideUp 0.4s cubic-bezier(0.16,1,0.3,1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Logo / Brand */
        .brand {
            text-align: center;
            margin-bottom: 36px;
        }
        .brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #3B82F6, #10B981);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 24px;
            box-shadow: 0 8px 24px var(--accent-glow);
        }
        .brand h1 {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #F1F5F9, #94A3B8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand p {
            font-size: 13px;
            color: var(--muted);
            margin-top: 4px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            background: var(--surface-2);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }
        input::placeholder { color: #374151; }

        /* Error message */
        .error-box {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: #FCA5A5;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Validation errors */
        .field-error {
            font-size: 12px;
            color: #FCA5A5;
            margin-top: 6px;
        }

        /* Remember me */
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            cursor: pointer;
        }
        .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
        }
        .remember span {
            font-size: 13px;
            color: var(--muted);
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #3B82F6, #2563EB);
            color: white;
            border: none;
            padding: 13px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(59,130,246,0.35);
            position: relative;
            overflow: hidden;
        }
        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(rgba(255,255,255,0), rgba(255,255,255,0.1));
            opacity: 0;
            transition: opacity 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59,130,246,0.45);
        }
        .btn-login:hover::after { opacity: 1; }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 12px;
            color: var(--muted);
        }
        .login-footer strong { color: #4B5563; }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Brand -->
        <div class="brand">
            <div class="brand-icon">⚡</div>
            <h1>Miltex EAJE</h1>
            <p>Analyse des Journaux Électroniques</p>
        </div>

        {{ $slot }}

        <div class="login-footer">
            Système réservé aux utilisateurs autorisés<br>
            <strong>© {{ date('Y') }} Miltex SARL</strong>
        </div>
    </div>

    @livewireScripts
</body>
</html>
