<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
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
        $this->validate();

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

        $this->errorMessage = null;
        session()->regenerate();

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
