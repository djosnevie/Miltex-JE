<?php

namespace App\Http\Controllers;

use App\Models\Passkey;
use App\Services\WebAuthnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class PasskeyController extends Controller
{
    public function __construct(
        protected WebAuthnService $webAuthnService
    ) {}

    /**
     * Options de création d'une clé d'accès (Enregistrement Touch ID / Face ID)
     */
    public function registerOptions(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Non authentifié.'], 401);
        }

        try {
            $options = $this->webAuthnService->getRegistrationOptions($user);
            return response()->json($options);
        } catch (Throwable $e) {
            Log::error('Erreur Passkey registerOptions: ' . $e->getMessage());
            return response()->json(['error' => 'Impossible de préparer l\'enregistrement biométrique.'], 500);
        }
    }

    /**
     * Validation et enregistrement de la clé d'accès sur le compte utilisateur
     */
    public function register(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Non authentifié.'], 401);
        }

        $validated = $request->validate([
            'clientDataJSON'    => 'required|string',
            'attestationObject' => 'required|string',
            'name'              => 'nullable|string|max:100',
        ]);

        try {
            $passkey = $this->webAuthnService->verifyRegistration(
                $user,
                $validated['clientDataJSON'],
                $validated['attestationObject'],
                $validated['name'] ?? null,
                $request->userAgent()
            );

            return response()->json([
                'success' => true,
                'message' => "Clé d'accès \"{$passkey->name}\" enregistrée avec succès.",
                'passkey' => [
                    'id'   => $passkey->id,
                    'name' => $passkey->name,
                ],
            ]);
        } catch (Throwable $e) {
            $msg = mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8');
            Log::error('Erreur Passkey register: ' . $msg);
            return response()->json(['error' => 'Échec de l\'enregistrement de la clé biométrique : ' . $msg], 422);
        }
    }

    /**
     * Options pour la connexion via Passkey (Touch ID / Face ID)
     */
    public function loginOptions(Request $request): JsonResponse
    {
        $email = $request->input('email');

        // Check if there is an in-flight 2FA user
        if (empty($email) && session('2fa_user_id')) {
            $twoFaUser = \App\Models\User::find(session('2fa_user_id'));
            if ($twoFaUser) {
                $email = $twoFaUser->email;
            }
        }

        try {
            $options = $this->webAuthnService->getAuthenticationOptions($email);
            return response()->json($options);
        } catch (Throwable $e) {
            Log::error('Erreur Passkey loginOptions: ' . $e->getMessage());
            return response()->json(['error' => 'Impossible d\'initialiser l\'authentification biométrique.'], 500);
        }
    }

    /**
     * Vérification de l'assertion biométrique et connexion
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id'                => 'required|string',
            'clientDataJSON'    => 'required|string',
            'authenticatorData' => 'required|string',
            'signature'         => 'required|string',
            'userHandle'        => 'nullable|string',
        ]);

        try {
            $user = $this->webAuthnService->verifyAuthentication(
                $validated['id'],
                $validated['clientDataJSON'],
                $validated['authenticatorData'],
                $validated['signature'],
                $validated['userHandle'] ?? null
            );

            // Connect user
            Auth::login($user, true);
            $request->session()->regenerate();

            // Clear any pending 2FA session
            session()->forget(['2fa_user_id', '2fa_remember']);

            $redirectUrl = $user->isSuperAdmin() ? route('superadmin.index') : route('dashboard');

            return response()->json([
                'success'     => true,
                'redirect'    => $redirectUrl,
                'user'        => [
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
            ]);
        } catch (Throwable $e) {
            $msg = mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8');
            Log::error('Erreur Passkey login: ' . $msg);
            return response()->json(['error' => 'Échec de la validation biométrique : ' . $msg], 422);
        }
    }

    /**
     * Suppression d'une clé d'accès
     */
    public function destroy(Request $request, Passkey $passkey): JsonResponse
    {
        $user = $request->user();

        if (! $user || ($passkey->user_id !== $user->id && ! $user->isSuperAdmin())) {
            return response()->json(['error' => 'Action non autorisée.'], 403);
        }

        $name = $passkey->name;
        $passkey->delete();

        return response()->json([
            'success' => true,
            'message' => "Clé d'accès \"{$name}\" révoquée avec succès.",
        ]);
    }
}
