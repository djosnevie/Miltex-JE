<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Accès Refusé | Miltex EAJE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0A0D14;
            color: #F1F5F9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            padding: 40px;
            max-width: 480px;
        }
        .code {
            font-size: 96px;
            font-weight: 700;
            background: linear-gradient(135deg, #EF4444, #F97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 16px;
        }
        h1 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        p {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #3B82F6, #2563EB);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(59,130,246,0.3);
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59,130,246,0.45);
        }
        .icon {
            font-size: 48px;
            margin-bottom: 16px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <span class="icon">🚫</span>
        <div class="code">403</div>
        <h1>Accès Refusé</h1>
        <p>
            Vous n'avez pas les permissions nécessaires pour accéder à cette ressource.<br>
            Contactez votre administrateur si vous pensez qu'il s'agit d'une erreur.
        </p>
        <a href="{{ url('/dashboard') }}" class="btn">← Retour au tableau de bord</a>
    </div>
</body>
</html>
