<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Accès Refusé | Miltex EAJE</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #F8FAFC;
            color: #0F172A;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
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

        .ambient-glow {
            position: fixed;
            top: 20%;
            left: 30%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.06) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            filter: blur(50px);
        }

        .error-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 24px;
            padding: 48px 36px;
            max-width: 460px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 10;
            box-shadow: 
                0 20px 45px -10px rgba(15, 23, 42, 0.08),
                0 4px 6px -2px rgba(15, 23, 42, 0.03);
        }

        .icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: #FEF2F2;
            color: #DC2626;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 1px solid #FECACA;
        }

        .code-badge {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #DC2626;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            padding: 4px 12px;
            border-radius: 9999px;
            display: inline-block;
            margin-bottom: 12px;
        }

        h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0F172A;
            margin-bottom: 12px;
        }

        p {
            font-size: 13.5px;
            color: #475569;
            margin-bottom: 32px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="ambient-grid"></div>
    <div class="ambient-glow"></div>

    <div class="error-card">
        <div class="icon-wrap">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <div class="code-badge">Erreur HTTP 403</div>
        <h1>Accès Non Autorisé</h1>
        <p>
            Votre compte utilisateur ne dispose pas des privilèges nécessaires pour accéder à cette interface ou exécuter cette action.
        </p>
        <a href="{{ url('/dashboard') }}" class="btn btn-primary" style="width: 100%;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Retourner au Tableau de Bord</span>
        </a>
    </div>
</body>
</html>
