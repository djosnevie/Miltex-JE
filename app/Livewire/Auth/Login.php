<?php

namespace App\Livewire\Auth;

use App\Mail\SuperAdminTwoFactorMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:6')]
    public string $password = '';

    public bool $remember = false;

    public ?string $errorMessage = null;

    public function login(): void
    {
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = [
            'email'    => $this->email,
            'password' => $this->password,
        ];

        if (! Auth::attempt($credentials, $this->remember)) {
            $this->errorMessage = 'Identifiants incorrects. Veuillez réessayer.';
            return;
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $this->errorMessage = 'Votre compte a été désactivé. Contactez un administrateur.';
            return;
        }

        // ── Double Authentification (2FA) par e-mail pour le SuperAdmin ───────
        if ($user->isSuperAdmin()) {
            $code = $user->generateTwoFactorCode();

            try {
                Mail::to($user->email)->send(new SuperAdminTwoFactorMail($user, $code));
            } catch (\Throwable $e) {
                Log::error('Erreur envoi email 2FA: ' . $e->getMessage());
            }

            Log::info("Code 2FA généré pour SuperAdmin [{$user->email}] : {$code}");

            // Store temporary 2FA state in session and de-authenticate user
            session()->put('2fa_user_id', $user->id);
            session()->put('2fa_remember', $this->remember);
            Auth::logout();

            $this->redirect(route('2fa.challenge'), navigate: true);
            return;
        }

        $this->errorMessage = null;
        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login', [
            'errorMessage' => $this->errorMessage,
        ]);
    }
}

