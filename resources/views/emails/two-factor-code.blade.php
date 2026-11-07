<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de vérification SuperAdmin</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F8FAFC; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0F172A; line-height: 1.6;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #F8FAFC; padding: 40px 16px;">
        <tr>
            <td align="center">
                
                {{-- Main Container Card --}}
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 540px; background-color: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);">
                    
                    {{-- Header with brand --}}
                    <tr>
                        <td style="padding: 32px 36px 24px; background: #0F172A; text-align: center;">
                            <div style="display: inline-block; margin-bottom: 8px;">
                                <table cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td style="background: linear-gradient(135deg, #D97706 0%, #B45309 100%); width: 40px; height: 40px; border-radius: 10px; text-align: center; vertical-align: middle;">
                                            <span style="color: #FFFFFF; font-size: 18px; font-weight: 800; line-height: 40px;">M</span>
                                        </td>
                                        <td style="padding-left: 12px; text-align: left;">
                                            <div style="color: #FFFFFF; font-size: 17px; font-weight: 800; letter-spacing: 0.04em;">
                                                MILTEX <span style="background: rgba(217, 119, 6, 0.25); color: #FBBF24; border: 1px solid rgba(251, 191, 36, 0.4); font-size: 10px; padding: 2px 6px; border-radius: 4px; vertical-align: middle;">EAJE</span>
                                            </div>
                                            <div style="color: #94A3B8; font-size: 11px; font-weight: 500;">Sécurité SuperAdmin Plateforme</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    {{-- Body Content --}}
                    <tr>
                        <td style="padding: 36px 36px 28px;">
                            
                            {{-- Security Pill --}}
                            <div style="margin-bottom: 20px;">
                                <span style="display: inline-block; background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Double Authentification (2FA)
                                </span>
                            </div>

                            <h1 style="margin: 0 0 16px; font-size: 21px; font-weight: 800; color: #0F172A; letter-spacing: -0.02em;">
                                Votre code de vérification de session
                            </h1>

                            <p style="margin: 0 0 18px; font-size: 14.5px; color: #475569;">
                                Bonjour <strong>{{ $user->name }}</strong>,
                            </p>

                            <p style="margin: 0 0 24px; font-size: 14px; color: #475569; line-height: 1.6;">
                                Une tentative de connexion en tant que <strong>Super Administrateur</strong> a été détectée sur votre compte Miltex EAJE. Pour finaliser votre authentification en toute sécurité, veuillez saisir le code de vérification unique ci-dessous :
                            </p>

                            {{-- Code Display Box --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 28px 0; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 12px; text-align: center;">
                                <tr>
                                    <td style="padding: 24px 20px;">
                                        <div style="font-size: 11px; font-weight: 700; color: #B45309; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px;">
                                            Code de sécurité unique (OTP)
                                        </div>
                                        <div style="font-family: 'Courier New', Courier, monospace; font-size: 38px; font-weight: 800; color: #92400E; letter-spacing: 0.35em; padding-left: 0.35em;">
                                            {{ $code }}
                                        </div>
                                        <div style="margin-top: 10px; font-size: 12px; color: #B45309; font-weight: 600;">
                                            Ce code est valable pendant <strong>10 minutes</strong>.
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            {{-- Security Warning Box --}}
                            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-left: 4px solid #D97706; border-radius: 8px; padding: 14px 16px; margin: 24px 0;">
                                <div style="font-size: 12.5px; font-weight: 700; color: #0F172A; margin-bottom: 4px;">
                                    Vous n'êtes pas à l'origine de cette demande ?
                                </div>
                                <div style="font-size: 12px; color: #64748B; line-height: 1.5;">
                                    Si vous n'avez pas tenté de vous connecter, ignorez cet e-mail. Personne ne peut accéder à votre compte sans ce code. Nous vous recommandons de réinitialiser votre mot de passe immédiatement.
                                </div>
                            </div>

                            <p style="margin: 24px 0 0; font-size: 12.5px; color: #64748B;">
                                Cordialement,<br>
                                <strong>L'équipe Sécurité & Conformité Miltex EAJE</strong>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 20px 36px; text-align: center;">
                            <p style="margin: 0; font-size: 11px; color: #94A3B8; line-height: 1.5;">
                                Ceci est un message automatique de sécurité du système Miltex EAJE.<br>
                                Ne partagez jamais ce code de vérification, même avec un agent Miltex.
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>
