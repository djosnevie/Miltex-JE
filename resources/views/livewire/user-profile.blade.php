@section('title', 'Mon Profil')

<div>
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- HERO EN-TÊTE DU PROFIL                                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="card" style="margin-bottom: 24px; padding: 24px 28px; background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%); border: 1px solid var(--border-subtle); position: relative; overflow: hidden;">
        <div style="position: absolute; right: -20px; top: -20px; width: 180px; height: 180px; background: radial-gradient(circle, rgba(37, 99, 235, 0.05) 0%, rgba(255, 255, 255, 0) 70%); border-radius: 50%; pointer-events: none;"></div>

        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; position: relative; z-index: 1;">
            <div style="display: flex; align-items: center; gap: 18px;">
                {{-- Avatar --}}
                <div style="width: 64px; height: 64px; border-radius: 16px; {{ $user->isSuperAdmin() ? 'background: linear-gradient(135deg, #D97706 0%, #B45309 100%); box-shadow: 0 4px 14px rgba(217, 119, 6, 0.28);' : 'background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%); box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);' }} display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800; color: #FFFFFF; flex-shrink: 0; border: 2px solid #FFFFFF;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <div>
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <h2 style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-main); letter-spacing: -0.02em;">
                            {{ $user->name }}
                        </h2>
                        <span style="font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 6px; {{ $user->isSuperAdmin() ? 'background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;' : ($user->isAdmin() ? 'background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE;' : 'background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;') }}">
                            {{ $user->roleBadge() }}
                        </span>
                        @if($user->is_active)
                        <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: #059669; background: #ECFDF5; padding: 3px 8px; border-radius: 6px; border: 1px solid #A7F3D0;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981;"></span>
                            Compte Actif
                        </span>
                        @endif
                    </div>
                    <div style="margin-top: 5px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 12.5px; color: var(--text-muted);">
                        <span style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $user->email }}
                        </span>
                        @if($user->tenant)
                        <span style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $user->tenant->name }}
                        </span>
                        @endif
                        <span style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Membre depuis {{ $user->created_at ? $user->created_at->translatedFormat('F Y') : '2026' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Quick action / 2FA status --}}
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="text-align: right; background: #FFFFFF; border: 1px solid var(--border-subtle); padding: 8px 14px; border-radius: 10px;">
                    <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Double Authentification</div>
                    <div style="font-size: 12px; font-weight: 700; color: #059669; display: flex; align-items: center; justify-content: flex-end; gap: 5px; margin-top: 2px;">
                        <svg width="14" height="14" fill="none" stroke="#059669" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Protection 2FA Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- NAVIGATION DES ONGLETS                                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div style="display: flex; gap: 8px; margin-bottom: 22px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 2px; overflow-x: auto;">
        <button
            type="button"
            wire:click="setActiveTab('profile')"
            style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border: none; background: transparent; font-size: 13.5px; font-weight: 700; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'profile' ? 'var(--primary, #2563EB)' : 'transparent' }}; color: {{ $activeTab === 'profile' ? 'var(--primary, #2563EB)' : 'var(--text-muted)' }}; transition: all 0.15s ease;"
        >
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Informations Personnelles</span>
        </button>

        <button
            type="button"
            wire:click="setActiveTab('security')"
            style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border: none; background: transparent; font-size: 13.5px; font-weight: 700; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'security' ? 'var(--primary, #2563EB)' : 'transparent' }}; color: {{ $activeTab === 'security' ? 'var(--primary, #2563EB)' : 'var(--text-muted)' }}; transition: all 0.15s ease;"
        >
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span>Mot de Passe & Sécurité</span>
        </button>

        <button
            type="button"
            wire:click="setActiveTab('passkeys')"
            style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border: none; background: transparent; font-size: 13.5px; font-weight: 700; cursor: pointer; border-bottom: 2px solid {{ $activeTab === 'passkeys' ? 'var(--primary, #2563EB)' : 'transparent' }}; color: {{ $activeTab === 'passkeys' ? 'var(--primary, #2563EB)' : 'var(--text-muted)' }}; transition: all 0.15s ease;"
        >
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.87M12 15a2 2 0 100-4 2 2 0 000 4z"/></svg>
            <span>Clés d'Accès Biométriques (Passkeys)</span>
            <span style="font-size: 11px; font-weight: 700; background: {{ $passkeys->count() > 0 ? '#EFF6FF' : '#F1F5F9' }}; color: {{ $passkeys->count() > 0 ? '#2563EB' : '#64748B' }}; padding: 2px 7px; border-radius: 9999px; border: 1px solid {{ $passkeys->count() > 0 ? '#BFDBFE' : '#E2E8F0' }};">
                {{ $passkeys->count() }}
            </span>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ONGLET 1 : INFORMATIONS PERSONNELLES                                      --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'profile')
    <div style="display: grid; grid-template-columns: 1.8fr 1fr; gap: 24px; align-items: start;">
        {{-- Formulaire profil --}}
        <div class="card">
            <div style="margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-subtle);">
                <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: var(--text-main);">
                    Modifier mes Coordonnées
                </h3>
                <p style="margin: 4px 0 0; font-size: 12.5px; color: var(--text-muted);">
                    Mettez à jour votre nom complet et votre adresse e-mail de connexion.
                </p>
            </div>

            @if($profileSuccess)
                <div class="alert alert-success" style="margin-bottom: 18px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $profileSuccess }}</span>
                </div>
            @endif

            @if($profileError)
                <div class="alert alert-error" style="margin-bottom: 18px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ $profileError }}</span>
                </div>
            @endif

            <form wire:submit.prevent="updateProfile">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    {{-- Nom complet --}}
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            Nom Complet <span style="color: #DC2626;">*</span>
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); display: flex;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <input
                                type="text"
                                wire:model="name"
                                style="width: 100%; background: var(--surface-input); border: 1.5px solid {{ $errors->has('name') ? '#DC2626' : 'var(--border-subtle)' }}; color: var(--text-main); padding: 10px 14px 10px 38px; border-radius: 10px; font-size: 13.5px; font-weight: 600; outline: none; transition: border-color 0.15s ease;"
                                onfocus="this.style.borderColor='var(--primary, #2563EB)'"
                                onblur="this.style.borderColor='var(--border-subtle)'"
                                placeholder="Ex. Jean Dupont"
                            >
                        </div>
                        @error('name')
                            <span style="display: block; font-size: 11.5px; color: #DC2626; margin-top: 5px; font-weight: 600;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Adresse e-mail --}}
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            Adresse E-mail <span style="color: #DC2626;">*</span>
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); display: flex;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <input
                                type="email"
                                wire:model="email"
                                style="width: 100%; background: var(--surface-input); border: 1.5px solid {{ $errors->has('email') ? '#DC2626' : 'var(--border-subtle)' }}; color: var(--text-main); padding: 10px 14px 10px 38px; border-radius: 10px; font-size: 13.5px; font-weight: 600; outline: none; transition: border-color 0.15s ease;"
                                onfocus="this.style.borderColor='var(--primary, #2563EB)'"
                                onblur="this.style.borderColor='var(--border-subtle)'"
                                placeholder="nom@miltex.cd"
                            >
                        </div>
                        @error('email')
                            <span style="display: block; font-size: 11.5px; color: #DC2626; margin-top: 5px; font-weight: 600;">{{ $message }}</span>
                        @enderror
                        <p style="margin: 5px 0 0; font-size: 11.5px; color: var(--text-muted);">
                            Les codes de double authentification (2FA) seront acheminés vers cette adresse.
                        </p>
                    </div>

                    {{-- Bouton de soumission --}}
                    <div style="margin-top: 10px; display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" style="display: inline-flex; align-items: center; gap: 8px;">
                            <span wire:loading.remove wire:target="updateProfile" style="display: inline-flex; align-items: center; gap: 8px;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>Enregistrer les Modifications</span>
                            </span>
                            <span wire:loading wire:target="updateProfile" style="display: inline-flex; align-items: center; gap: 8px;">
                                <svg class="spinner-icon" width="15" height="15" fill="none" viewBox="0 0 24 24"><circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Mise à jour en cours…</span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Carte récapitulative compte & permissions --}}
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div class="card" style="padding: 20px;">
                <h4 style="margin: 0 0 14px; font-size: 13px; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 7px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Détails du Compte</span>
                </h4>

                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 12.5px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid var(--border-subtle);">
                        <span style="color: var(--text-muted);">Rôle Système</span>
                        <span style="font-weight: 700; color: var(--text-main);">{{ $user->roleBadge() }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid var(--border-subtle);">
                        <span style="color: var(--text-muted);">Organisation</span>
                        <span style="font-weight: 700; color: var(--text-main);">{{ $user->tenant ? $user->tenant->name : 'Miltex Plateforme' }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid var(--border-subtle);">
                        <span style="color: var(--text-muted);">Statut du compte</span>
                        <span style="font-weight: 700; color: #059669;">Actif & Autorisé</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; border-bottom: 1px solid var(--border-subtle);">
                        <span style="color: var(--text-muted);">Identifiant UID</span>
                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 11.5px; font-weight: 600; color: var(--text-secondary);">#{{ $user->id }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-muted);">Clés Biométriques</span>
                        <span style="font-weight: 700; color: {{ $passkeys->count() > 0 ? '#059669' : '#D97706' }};">
                            {{ $passkeys->count() }} configurée(s)
                        </span>
                    </div>
                </div>
            </div>

            {{-- Conseils de sécurité --}}
            <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 12px; padding: 16px 18px;">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12.5px; font-weight: 700; color: #166534; margin-bottom: 6px;">
                    <svg width="16" height="16" fill="none" stroke="#166534" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Conformité Fiscale & Sécurité</span>
                </div>
                <p style="margin: 0; font-size: 11.5px; color: #15803D; line-height: 1.5;">
                    Conformément aux normes d'audit des journaux électroniques, vos actions d'audit et d'export sont journalisées avec votre identifiant utilisateur.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ONGLET 2 : MOT DE PASSE & SÉCURITÉ                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'security')
    <div style="max-width: 680px;">
        <div class="card">
            <div style="margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-subtle);">
                <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: var(--text-main);">
                    Changer de Mot de Passe
                </h3>
                <p style="margin: 4px 0 0; font-size: 12.5px; color: var(--text-muted);">
                    Assurez-vous d'utiliser un mot de passe robuste d'au moins 8 caractères pour protéger l'accès à la plateforme Miltex EAJE.
                </p>
            </div>

            @if($passwordSuccess)
                <div class="alert alert-success" style="margin-bottom: 18px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $passwordSuccess }}</span>
                </div>
            @endif

            @if($passwordError)
                <div class="alert alert-error" style="margin-bottom: 18px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ $passwordError }}</span>
                </div>
            @endif

            <form wire:submit.prevent="updatePassword">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    {{-- Mot de passe actuel --}}
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            Mot de passe actuel <span style="color: #DC2626;">*</span>
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); display: flex;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input
                                type="password"
                                wire:model="current_password"
                                style="width: 100%; background: var(--surface-input); border: 1.5px solid {{ $errors->has('current_password') ? '#DC2626' : 'var(--border-subtle)' }}; color: var(--text-main); padding: 10px 14px 10px 38px; border-radius: 10px; font-size: 13.5px; outline: none; transition: border-color 0.15s ease;"
                                placeholder="••••••••"
                            >
                        </div>
                        @error('current_password')
                            <span style="display: block; font-size: 11.5px; color: #DC2626; margin-top: 5px; font-weight: 600;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Nouveau mot de passe --}}
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            Nouveau mot de passe <span style="color: #DC2626;">*</span>
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); display: flex;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </span>
                            <input
                                type="password"
                                wire:model="new_password"
                                style="width: 100%; background: var(--surface-input); border: 1.5px solid {{ $errors->has('new_password') ? '#DC2626' : 'var(--border-subtle)' }}; color: var(--text-main); padding: 10px 14px 10px 38px; border-radius: 10px; font-size: 13.5px; outline: none; transition: border-color 0.15s ease;"
                                placeholder="Minimum 8 caractères"
                            >
                        </div>
                        @error('new_password')
                            <span style="display: block; font-size: 11.5px; color: #DC2626; margin-top: 5px; font-weight: 600;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Confirmation nouveau mot de passe --}}
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            Confirmer le nouveau mot de passe <span style="color: #DC2626;">*</span>
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); display: flex;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <input
                                type="password"
                                wire:model="new_password_confirmation"
                                style="width: 100%; background: var(--surface-input); border: 1.5px solid {{ $errors->has('new_password_confirmation') ? '#DC2626' : 'var(--border-subtle)' }}; color: var(--text-main); padding: 10px 14px 10px 38px; border-radius: 10px; font-size: 13.5px; outline: none; transition: border-color 0.15s ease;"
                                placeholder="Confirmez le nouveau mot de passe"
                            >
                        </div>
                        @error('new_password_confirmation')
                            <span style="display: block; font-size: 11.5px; color: #DC2626; margin-top: 5px; font-weight: 600;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Recommandations de sécurité --}}
                    <div style="background: #F8FAFC; border: 1px solid var(--border-subtle); border-radius: 8px; padding: 12px 14px; font-size: 11.5px; color: var(--text-muted); line-height: 1.5;">
                        <div style="font-weight: 700; color: var(--text-main); margin-bottom: 4px;">Exigences de sécurité :</div>
                        <ul style="margin: 0; padding-left: 18px;">
                            <li>Au moins 8 caractères de longueur</li>
                            <li>Doit être différent de votre mot de passe actuel</li>
                            <li>Il est recommandé de combiner majuscules, minuscules, chiffres et caractères spéciaux</li>
                        </ul>
                    </div>

                    {{-- Bouton de soumission --}}
                    <div style="margin-top: 10px; display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" style="display: inline-flex; align-items: center; gap: 8px;">
                            <span wire:loading.remove wire:target="updatePassword" style="display: inline-flex; align-items: center; gap: 8px;">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Mettre à Jour le Mot de Passe</span>
                            </span>
                            <span wire:loading wire:target="updatePassword" style="display: inline-flex; align-items: center; gap: 8px;">
                                <svg class="spinner-icon" width="15" height="15" fill="none" viewBox="0 0 24 24"><circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Validation en cours…</span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- ONGLET 3 : CLÉS D'ACCÈS BIOMÉTRIQUES (TOUCH ID / FACE ID / PASSKEYS)      --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'passkeys')
    <div class="card" x-data="{
        regLoading: false,
        regError: null,
        regSuccess: null,
        async registerPasskey() {
            this.regLoading = true;
            this.regError = null;
            this.regSuccess = null;
            try {
                const defaultName = navigator.userAgent.includes('Macintosh') ? 'Mon MacBook (Touch ID)' : (navigator.userAgent.includes('iPhone') ? 'Mon iPhone (Face ID)' : 'Clé Biométrique');
                const keyName = prompt('Nom descriptif pour cet appareil :', defaultName);
                if (!keyName) {
                    this.regLoading = false;
                    return;
                }
                const res = await window.Passkeys.register(keyName);
                this.regSuccess = res.message || 'Clé biométrique enregistrée avec succès.';
                setTimeout(() => window.location.reload(), 1400);
            } catch (err) {
                console.error(err);
                if (err.name !== 'NotAllowedError') {
                    this.regError = err.message || 'Erreur lors de l\'enregistrement de la clé.';
                }
            } finally {
                this.regLoading = false;
            }
        }
    }">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-subtle); flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" fill="none" stroke="#DC2626" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.87M12 15a2 2 0 100-4 2 2 0 000 4z"/></svg>
                    <span>Mes Clés d'Accès Biométriques (Passkeys)</span>
                </h3>
                <p style="margin: 4px 0 0; font-size: 12.5px; color: var(--text-muted);">
                    Connectez-vous à Miltex EAJE d'un simple toucher de doigt avec Touch ID ou le capteur biométrique de votre appareil, sans mot de passe ni code e-mail.
                </p>
            </div>
            <div>
                <button
                    type="button"
                    @click="registerPasskey"
                    :disabled="regLoading"
                    class="btn btn-primary"
                    style="display: inline-flex; align-items: center; gap: 8px;"
                >
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span x-show="!regLoading">Enregistrer cet appareil (Touch ID / Face ID)</span>
                    <span x-show="regLoading" x-cloak>Configuration Touch ID en cours…</span>
                </button>
            </div>
        </div>

        <template x-if="regSuccess">
            <div class="alert alert-success" style="margin-bottom: 18px;" x-text="regSuccess"></div>
        </template>

        <template x-if="regError">
            <div class="alert alert-error" style="margin-bottom: 18px;" x-text="regError"></div>
        </template>

        @if(session('passkey_success'))
            <div class="alert alert-success" style="margin-bottom: 18px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('passkey_success') }}</span>
            </div>
        @endif

        {{-- Readiness card --}}
        <div style="background: #F8FAFC; border: 1px solid var(--border-subtle); border-radius: 12px; padding: 14px 18px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: 9px; background: #FEF3C7; border: 1px solid #FDE68A; display: flex; align-items: center; justify-content: center; color: #D97706; flex-shrink: 0;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <div style="font-size: 13px; font-weight: 700; color: var(--text-main);">Support Biométrique WebAuthn Prêt</div>
                    <div style="font-size: 11.5px; color: var(--text-muted);">Votre navigateur et votre système d'exploitation prennent en charge l'authentification biométrique Touch ID, Face ID et Windows Hello.</div>
                </div>
            </div>
            <div>
                <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 700; color: #059669; background: #ECFDF5; border: 1px solid #A7F3D0; padding: 4px 10px; border-radius: 9999px;">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981;"></span>
                    Prêt pour Touch ID / Face ID
                </span>
            </div>
        </div>

        {{-- Table des clés --}}
        @if($passkeys->count() > 0)
        <div class="table-wrap" style="border: 1px solid var(--border-subtle); border-radius: 10px; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid var(--border-subtle); text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">
                        <th style="padding: 12px 16px;">Appareil / Clé d'Accès</th>
                        <th style="padding: 12px 16px;">Identifiant (Credential)</th>
                        <th style="padding: 12px 16px;">Type Authentificateur</th>
                        <th style="padding: 12px 16px;">Date d'Ajout</th>
                        <th style="padding: 12px 16px;">Dernière Utilisation</th>
                        <th style="padding: 12px 16px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($passkeys as $pk)
                    <tr style="border-bottom: 1px solid var(--border-subtle); font-size: 12.5px;">
                        <td style="padding: 14px 16px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: #EFF6FF; border: 1px solid #BFDBFE; display: flex; align-items: center; justify-content: center; color: #2563EB;">
                                    @if($pk->iconName() === 'apple')
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.42c.6-.74 1.01-1.77.9-2.8-1.01.04-2.24.67-2.96 1.51-.55.63-.98 1.68-.85 2.68 1.13.09 2.31-.65 2.91-1.39z"/></svg>
                                    @elseif($pk->iconName() === 'windows')
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M0 3.449L9.75 2.1v9.451H0m10.949-9.602L24 0v11.4H10.949M0 12.6h9.75v9.451L0 20.699M10.949 12.6H24V24l-12.951-1.801"/></svg>
                                    @else
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.87M12 15a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main);">{{ $pk->name }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">{{ ucfirst($pk->device_type ?? 'Plateforme') }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="font-family: 'JetBrains Mono', monospace; font-size: 11.5px; color: var(--text-muted);" title="{{ $pk->credential_id }}">
                                {{ substr($pk->credential_id, 0, 16) }}…{{ substr($pk->credential_id, -8) }}
                            </span>
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; background: #F1F5F9; color: #475569; padding: 2px 8px; border-radius: 5px;">
                                Touch ID / Passkey FIDO2
                            </span>
                        </td>
                        <td style="padding: 14px 16px; color: var(--text-secondary); font-size: 12px;">
                            {{ $pk->created_at ? $pk->created_at->translatedFormat('d M Y H:i') : '—' }}
                        </td>
                        <td style="padding: 14px 16px; font-size: 12px;">
                            @if($pk->last_used_at)
                                <span style="font-weight: 600; color: #059669;">{{ $pk->last_used_at->diffForHumans() }}</span>
                            @else
                                <span style="color: var(--text-muted);">Jamais utilisée</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: right;">
                            <button
                                type="button"
                                wire:click="deletePasskey({{ $pk->id }})"
                                wire:confirm="Êtes-vous sûr de vouloir révoquer cette clé d'accès ?"
                                style="background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626; padding: 5px 10px; border-radius: 7px; font-size: 11.5px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;"
                                onmouseover="this.style.background='#FEE2E2'"
                                onmouseout="this.style.background='#FEF2F2'"
                            >
                                Révoquer
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding: 40px 20px; text-align: center; border: 1.5px dashed var(--border-subtle); border-radius: 12px; background: #FAFAFA;">
            <div style="width: 52px; height: 52px; border-radius: 14px; background: #FEF3C7; border: 1px solid #FDE68A; display: flex; align-items: center; justify-content: center; color: #D97706; margin: 0 auto 12px;">
                <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.87M12 15a2 2 0 100-4 2 2 0 000 4z"/></svg>
            </div>
            <div style="font-size: 14px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">Aucune clé biométrique configurée</div>
            <p style="margin: 0 0 16px; font-size: 12px; color: var(--text-muted); max-width: 420px; margin-left: auto; margin-right: auto;">
                Enregistrez le capteur Touch ID de votre Mac ou Face ID pour vous connecter instantanément en 1 clic sans mot de passe.
            </p>
            <button
                type="button"
                @click="registerPasskey"
                :disabled="regLoading"
                class="btn btn-primary"
                style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px;"
            >
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Configurer Touch ID / Face ID</span>
            </button>
        </div>
        @endif
    </div>
    @endif
</div>
