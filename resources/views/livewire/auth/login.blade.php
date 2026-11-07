<div>
    {{-- Error message banner --}}
    @if($errorMessage)
        <div class="error-banner">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif

    <form wire:submit="login" x-data="{ showPassword: false }">
        {{-- Email --}}
        <div class="form-group">
            <label for="login-email">Adresse e-mail</label>
            <div class="input-wrap">
                <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input
                    id="login-email"
                    type="email"
                    wire:model="email"
                    class="auth-input"
                    placeholder="nom@miltex.cd"
                    autocomplete="email"
                    autofocus
                    spellcheck="false"
                >
            </div>
            @error('email')
                <div class="field-error-text">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="login-password">Mot de passe</label>
            <div class="input-wrap">
                <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input
                    id="login-password"
                    :type="showPassword ? 'text' : 'password'"
                    type="password"
                    wire:model="password"
                    class="auth-input auth-input-password"
                    placeholder="••••••••••••"
                    autocomplete="current-password"
                >
                <button
                    type="button"
                    class="password-toggle-btn"
                    @click="showPassword = !showPassword"
                    :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                    tabindex="-1"
                >
                    <svg x-show="!showPassword" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showPassword" x-cloak width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="field-error-text">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remember me --}}
        <label class="remember-row">
            <input type="checkbox" wire:model="remember" id="remember">
            <span>Maintenir la session active</span>
        </label>

        {{-- Submit --}}
        <button type="submit" class="btn-submit" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="login" class="btn-text">
                <span>Accéder à la plateforme</span>
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </span>
            <span wire:loading wire:target="login" class="btn-text">
                <svg class="spinner-icon" width="16" height="16" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Authentification en cours…</span>
            </span>
        </button>
    </form>

    {{-- Biometric Passkey (Touch ID / Face ID / Clé d'accès) --}}
    <div style="margin-top: 20px;" x-data="{
        passkeyLoading: false,
        passkeyError: null,
        async loginWithPasskey() {
            this.passkeyLoading = true;
            this.passkeyError = null;
            try {
                const emailInput = document.getElementById('login-email')?.value || null;
                const result = await window.Passkeys.authenticate(emailInput);
                if (result.success && result.redirect) {
                    window.location.href = result.redirect;
                }
            } catch (err) {
                console.error(err);
                if (err.name === 'NotAllowedError') {
                    // Utilisateur a cliqué sur annuler
                } else if (err.message && (err.message.includes('introuvable') || err.message.includes('inconnue') || err.message.includes('vide'))) {
                    this.passkeyError = 'Aucune clé Touch ID n\'est encore enregistrée sur ce Mac. Connectez-vous d\'abord avec votre mot de passe pour l\'activer en 1 clic.';
                } else {
                    this.passkeyError = 'Aucune clé d\'accès trouvée sur cet appareil. Connectez-vous d\'abord avec votre mot de passe pour enregistrer Touch ID.';
                }
            } finally {
                this.passkeyLoading = false;
            }
        }
    }">
        <div style="display: flex; align-items: center; gap: 12px; margin: 18px 0 14px;">
            <div style="flex: 1; height: 1px; background: var(--border);"></div>
            <span style="font-size: 10.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em;">ou utiliser une clé d'accès</span>
            <div style="flex: 1; height: 1px; background: var(--border);"></div>
        </div>

        <template x-if="passkeyError">
            <div class="error-banner" style="margin-bottom: 12px;" x-text="passkeyError"></div>
        </template>

        <button
            type="button"
            @click="loginWithPasskey"
            :disabled="passkeyLoading"
            style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; background: #FFFFFF; border: 1.5px solid #CBD5E1; color: #0F172A; padding: 11px 16px; border-radius: 12px; font-size: 13.5px; font-weight: 700; cursor: pointer; transition: all 0.18s ease; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);"
            onmouseover="this.style.background='#F8FAFC'; this.style.borderColor='#94A3B8';"
            onmouseout="this.style.background='#FFFFFF'; this.style.borderColor='#CBD5E1';"
        >
            <span x-show="!passkeyLoading" style="display: inline-flex; align-items: center; gap: 10px;">
                <svg width="20" height="20" fill="none" stroke="#DC2626" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.87M12 15a2 2 0 100-4 2 2 0 000 4z"/>
                </svg>
                <span>Touch ID / Face ID (Passkey)</span>
            </span>
            <span x-show="passkeyLoading" x-cloak style="display: inline-flex; align-items: center; gap: 8px; color: #64748B;">
                <svg class="spinner-icon" width="16" height="16" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Utilisez Touch ID pour continuer…</span>
            </span>
        </button>
    </div>
</div>
