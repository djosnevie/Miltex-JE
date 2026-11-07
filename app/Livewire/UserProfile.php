<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserProfile extends Component
{
    public string $name = '';
    public string $email = '';

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public ?string $profileSuccess = null;
    public ?string $profileError = null;

    public ?string $passwordSuccess = null;
    public ?string $passwordError = null;

    public string $activeTab = 'profile';

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
        }
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->profileSuccess = null;
        $this->profileError = null;
        $this->passwordSuccess = null;
        $this->passwordError = null;
    }

    public function updateProfile(): void
    {
        $this->profileSuccess = null;
        $this->profileError = null;

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $validated = $this->validate([
            'name'  => 'required|string|min:2|max:100',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ], [
            'name.required'  => 'Le nom complet est obligatoire.',
            'name.min'       => 'Le nom doit comporter au moins 2 caractères.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email'    => 'Veuillez saisir une adresse e-mail valide.',
            'email.unique'   => 'Cette adresse e-mail est déjà attribuée à un autre compte.',
        ]);

        $user->update([
            'name'  => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
        ]);

        $this->profileSuccess = 'Vos informations personnelles ont été mises à jour avec succès.';
    }

    public function updatePassword(): void
    {
        $this->passwordSuccess = null;
        $this->passwordError = null;

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $validated = $this->validate([
            'current_password'          => 'required|current_password',
            'new_password'              => 'required|string|min:8|different:current_password|confirmed',
            'new_password_confirmation' => 'required',
        ], [
            'current_password.required'         => 'Le mot de passe actuel est requis.',
            'current_password.current_password' => 'Le mot de passe actuel saisi est incorrect.',
            'new_password.required'             => 'Le nouveau mot de passe est obligatoire.',
            'new_password.min'                  => 'Le nouveau mot de passe doit comporter au moins 8 caractères.',
            'new_password.different'            => 'Le nouveau mot de passe doit être différent de votre mot de passe actuel.',
            'new_password.confirmed'            => 'La confirmation du mot de passe ne correspond pas.',
            'new_password_confirmation.required'=> 'Veuillez confirmer votre nouveau mot de passe.',
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->passwordSuccess = 'Votre mot de passe a été modifié avec succès.';
    }

    public function deletePasskey(int $id): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $passkey = $user->passkeys()->find($id);

        if ($passkey) {
            $name = $passkey->name;
            $passkey->delete();
            session()->flash('passkey_success', "La clé d'accès \"{$name}\" a été révoquée avec succès.");
        }
    }

    public function render()
    {
        $user = Auth::user();
        $passkeys = $user ? $user->passkeys()->latest()->get() : collect();

        return view('livewire.user-profile', array_merge(get_object_vars($this), [
            'user'     => $user,
            'passkeys' => $passkeys,
            'errors'   => session('errors', new \Illuminate\Support\ViewErrorBag),
        ]))->layout('layouts.app');
    }
}
