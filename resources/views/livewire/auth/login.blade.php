<div>
    {{-- Error message --}}
    @if($errorMessage)
        <div class="error-box">
            <span>⚠</span>
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit="login">
        {{-- Email --}}
        <div class="form-group">
            <label for="login-email">Adresse e-mail</label>
            <input
                id="login-email"
                type="email"
                wire:model="email"
                placeholder="vous@miltex.cd"
                autocomplete="email"
                autofocus
            >
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="login-password">Mot de passe</label>
            <input
                id="login-password"
                type="password"
                wire:model="password"
                placeholder="••••••••"
                autocomplete="current-password"
            >
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Remember me --}}
        <label class="remember">
            <input type="checkbox" wire:model="remember" id="remember">
            <span>Se souvenir de moi</span>
        </label>

        {{-- Submit --}}
        <button type="submit" class="btn-login" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="login">Se connecter</span>
            <span wire:loading wire:target="login">
                <span class="spinner"></span> Connexion…
            </span>
        </button>
    </form>
</div>
