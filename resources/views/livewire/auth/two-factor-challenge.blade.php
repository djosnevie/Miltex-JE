<div>
    {{-- Security Header Badge --}}
    <div style="text-align: center; margin-bottom: 22px;">
        <div style="width: 52px; height: 52px; margin: 0 auto 12px; border-radius: 14px; background: #FEF3C7; border: 1px solid #FDE68A; display: flex; align-items: center; justify-content: center; color: #D97706; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.15);">
            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h2 style="font-size: 19px; font-weight: 800; color: var(--text); margin-bottom: 6px; letter-spacing: -0.02em;">
            Double Authentification Requise
        </h2>
        <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.5; margin: 0;">
            Un code de sécurité à 6 chiffres a été envoyé par e-mail à :<br>
            <strong style="color: var(--text); font-weight: 700;">{{ $user?->email ?? 'votre adresse e-mail' }}</strong>
        </p>
    </div>

    {{-- Success Notification --}}
    @if($successMessage)
        <div style="margin-bottom: 20px; background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 16px; border-radius: 10px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $successMessage }}</span>
        </div>
    @endif

    {{-- Error Notification --}}
    @if($errorMessage)
        <div class="error-banner" style="margin-bottom: 20px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif

    <form wire:submit="verify">
        {{-- Code Input Field --}}
        <div class="form-group" style="margin-bottom: 22px;">
            <label for="two-factor-code" style="text-align: center; display: block; font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); letter-spacing: 0.05em; margin-bottom: 8px;">
                Code de vérification (6 chiffres)
            </label>
            <div style="position: relative;">
                <input
                    id="two-factor-code"
                    type="text"
                    wire:model="code"
                    maxlength="6"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    autocomplete="one-time-code"
                    autofocus
                    placeholder="000000"
                    style="width: 100%; height: 56px; text-align: center; font-size: 26px; font-weight: 800; font-family: 'Courier New', Courier, monospace; letter-spacing: 0.35em; padding-left: 0.35em; background: #FFFFFF; border: 2px solid var(--border); border-radius: 12px; color: #0F172A; outline: none; transition: all 0.18s ease; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);"
                    onfocus="this.style.borderColor='#D97706'; this.style.boxShadow='0 0 0 4px rgba(217, 119, 6, 0.12)';"
                    onblur="this.style.borderColor='var(--border)'; this.style.boxShadow='none';"
                >
            </div>
            @error('code')
                <div class="field-error-text" style="text-align: center;">{{ $message }}</div>
            @enderror
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; font-size: 11.5px; color: var(--text-muted);">
                <span>Validité : 10 minutes</span>
                <span>Tentatives max : 5</span>
            </div>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #D97706 0%, #B45309 100%); margin-bottom: 12px;" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="verify" class="btn-text">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Confirmer et se connecter</span>
            </span>
            <span wire:loading wire:target="verify" class="btn-text">
                <svg class="spinner-icon" width="16" height="16" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Vérification en cours…</span>
            </span>
        </button>

        @if($user && $user->hasPasskeys())
        {{-- Alternative: Touch ID / Face ID validation --}}
        <div style="margin-bottom: 12px;" x-data="{
            passkeyLoading: false,
            passkeyError: null,
            async verifyWithPasskey() {
                this.passkeyLoading = true;
                this.passkeyError = null;
                try {
                    const result = await window.Passkeys.authenticate('{{ $user?->email }}');
                    if (result.success && result.redirect) {
                        window.location.href = result.redirect;
                    }
                } catch (err) {
                    console.error(err);
                    if (err.name !== 'NotAllowedError') {
                        this.passkeyError = err.message || 'Validation biométrique échouée.';
                    }
                } finally {
                    this.passkeyLoading = false;
                }
            }
        }">
            <template x-if="passkeyError">
                <div class="error-banner" style="margin-bottom: 10px;" x-text="passkeyError"></div>
            </template>

            <button
                type="button"
                @click="verifyWithPasskey"
                :disabled="passkeyLoading"
                style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: #FFFBEB; border: 1.5px solid #FDE68A; color: #92400E; padding: 10px 14px; border-radius: 11px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.18s ease;"
                onmouseover="this.style.background='#FEF3C7';"
                onmouseout="this.style.background='#FFFBEB';"
            >
                <span x-show="!passkeyLoading" style="display: inline-flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004.07 9.87M12 15a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                    <span>Valider avec Touch ID / Face ID</span>
                </span>
                <span x-show="passkeyLoading" x-cloak style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg class="spinner-icon" width="15" height="15" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>En attente de Touch ID…</span>
                </span>
            </button>
        </div>
        @else
        <div style="background: #FFFBEB; border: 1px dashed #FDE68A; border-radius: 10px; padding: 10px 14px; margin-bottom: 12px; text-align: center; font-size: 11.5px; color: #92400E; display: flex; align-items: center; justify-content: center; gap: 6px;">
            <svg width="15" height="15" fill="none" stroke="#D97706" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Saisissez le code reçu par e-mail ci-dessous pour configurer votre Touch ID.</span>
        </div>
        @endif

        {{-- Actions: Resend & Cancel --}}
        <div style="display: flex; flex-direction: column; gap: 10px; text-align: center; margin-top: 8px;">
            <button
                type="button"
                wire:click="resend"
                wire:loading.attr="disabled"
                style="background: none; border: none; font-size: 12.5px; font-weight: 600; color: #D97706; cursor: pointer; text-decoration: underline; padding: 4px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;"
            >
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Renvoyer un nouveau code par e-mail</span>
            </button>

            <button
                type="button"
                wire:click="cancel"
                style="background: none; border: none; font-size: 12px; font-weight: 500; color: var(--text-muted); cursor: pointer; padding: 4px;"
            >
                ← Annuler et revenir à la page de connexion
            </button>
        </div>
    </form>
</div>
