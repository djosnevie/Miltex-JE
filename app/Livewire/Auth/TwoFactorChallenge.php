<?php

namespace App\Livewire\Auth;

use App\Mail\SuperAdminTwoFactorMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('layouts.guest')]
class TwoFactorChallenge extends Component
{
    #[Rule('required|digits:6', message: [
        'required' => 'Veuillez saisir le code de vérification à 6 chiffres.',
        'digits'   => 'Le code de vérification doit comporter exactement 6 chiffres.',
    ])]
    public string $code = '';

    public ?string $errorMessage = null;
    public ?string $successMessage = null;
    public int $attempts = 0;

    public function mount(): void
    {
        $userId = session('2fa_user_id');

        if (! $userId) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $user = User::find($userId);

        if (! $user || ! $user->is_active || ! $user->isSuperAdmin()) {
            session()->forget(['2fa_user_id', '2fa_remember']);
            $this->redirect(route('login'), navigate: true);
            return;
        }

        // If code has expired, notify
        if ($user->two_factor_expires_at && now()->isAfter($user->two_factor_expires_at)) {
            $this->errorMessage = 'Votre code a expiré. Cliquez sur "Renvoyer un nouveau code".';
        }
    }

    public function verify(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        $this->validate([
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'Veuillez saisir le code de vérification à 6 chiffres.',
            'code.digits'   => 'Le code de vérification doit comporter exactement 6 chiffres.',
        ]);

        $userId = session('2fa_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user || ! $user->is_active || ! $user->isSuperAdmin()) {
            session()->forget(['2fa_user_id', '2fa_remember']);
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $this->attempts++;

        // Maximum 5 failed attempts
        if ($this->attempts >= 5) {
            $user->resetTwoFactorCode();
            session()->forget(['2fa_user_id', '2fa_remember']);
            session()->flash('error', 'Nombre maximal de tentatives de code dépassé. Veuillez vous reconnecter.');
            $this->redirect(route('login'), navigate: true);
            return;
        }

        if (! $user->verifyTwoFactorCode($this->code)) {
            $remaining = 5 - $this->attempts;
            $this->errorMessage = "Code de vérification incorrect ou expiré. ({$remaining} tentative(s) restante(s)).";
            $this->code = '';
            return;
        }

        // Verification successful
        $user->resetTwoFactorCode();
        $remember = (bool) session('2fa_remember', false);
        session()->forget(['2fa_user_id', '2fa_remember']);

        Auth::login($user, $remember);
        session()->regenerate();

        session()->flash('success', "Authentification à deux facteurs réussie. Bienvenue, {$user->name}.");
        $this->redirect(route('superadmin.index'), navigate: true);
    }

    public function resend(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        $userId = session('2fa_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $newCode = $user->generateTwoFactorCode();

        try {
            Mail::to($user->email)->send(new SuperAdminTwoFactorMail($user, $newCode));
        } catch (\Throwable $e) {
            Log::error("Erreur renvoi email 2FA: " . $e->getMessage());
        }

        Log::info("Nouveau code 2FA généré pour SuperAdmin [{$user->email}] : {$newCode}");

        $this->code = '';
        $this->attempts = 0;
        $this->successMessage = "Un nouveau code de vérification à 6 chiffres a été envoyé à {$user->email}.";
    }

    public function cancel(): void
    {
        $userId = session('2fa_user_id');
        if ($userId) {
            User::find($userId)?->resetTwoFactorCode();
        }

        session()->forget(['2fa_user_id', '2fa_remember']);
        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        $userId = session('2fa_user_id');
        $user = $userId ? User::find($userId) : null;

        return view('livewire.auth.two-factor-challenge', array_merge(get_object_vars($this), [
            'user' => $user,
        ]));
    }
}
