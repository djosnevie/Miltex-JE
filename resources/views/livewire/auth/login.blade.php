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
</div>
