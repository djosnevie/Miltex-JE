<?php

namespace App\Services;

use App\Models\Passkey;
use App\Models\User;
use Illuminate\Http\Request;
use lbuchs\WebAuthn\Binary\ByteBuffer;
use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\WebAuthnException;

class WebAuthnService
{
    protected WebAuthn $webAuthn;

    public function __construct(Request $request)
    {
        // Enforce standard Base64Url encoding for all ByteBuffer serialization
        ByteBuffer::$useBase64UrlEncoding = true;

        $rpName = config('app.name', 'Miltex EAJE');
        $host = $request->getHost();

        // Strip port if present
        if (str_contains($host, ':')) {
            $host = explode(':', $host)[0];
        }

        $formats = ['apple', 'android-key', 'android-safetynet', 'fido-u2f', 'none', 'packed', 'tpm'];
        $this->webAuthn = new WebAuthn($rpName, $host, $formats, true);
    }

    public function getRegistrationOptions(User $user): \stdClass
    {
        ByteBuffer::$useBase64UrlEncoding = true;

        $excludeIds = $user->passkeys->map(function ($p) {
            return ByteBuffer::fromBase64Url($p->credential_id);
        })->all();

        $createArgs = $this->webAuthn->getCreateArgs(
            (string) $user->id,
            $user->email,
            $user->name,
            60,
            'preferred', // Enable discoverable resident key for Touch ID / iCloud Keychain
            'preferred',
            null, // both platform (Touch ID/Face ID) and cross-platform (Yubikey)
            $excludeIds
        );

        // Store challenge as Base64Url string (valid UTF-8) to ensure reliable session serialization
        $challengeBase64Url = rtrim(strtr(base64_encode($this->webAuthn->getChallenge()->getBinaryString()), '+/', '-_'), '=');
        session()->put('webauthn_reg_challenge', $challengeBase64Url);
        cache()->put("webauthn_reg_challenge_{$user->id}", $challengeBase64Url, now()->addMinutes(5));
        session()->save();

        return $createArgs;
    }

    public function verifyRegistration(User $user, string $clientDataJSON, string $attestationObject, ?string $keyName = null, ?string $userAgent = null): Passkey
    {
        $challengeBase64Url = session('webauthn_reg_challenge') ?: cache()->get("webauthn_reg_challenge_{$user->id}");
        if (! $challengeBase64Url) {
            throw new WebAuthnException('Défi de sécurité WebAuthn introuvable ou expiré. Veuillez rafraîchir la page et réessayer.');
        }

        // 1. Convert clientDataJSON from Base64Url to raw JSON string
        $clientDataRaw = $this->decodeClientDataJSON($clientDataJSON);

        // 2. Convert attestationObject from Base64Url to binary bytes
        $attestationBinary = ByteBuffer::fromBase64Url($attestationObject)->getBinaryString();

        // 3. Challenge as ByteBuffer from Base64Url
        $challengeBuffer = ByteBuffer::fromBase64Url($challengeBase64Url);

        // 4. Process creation via WebAuthn library
        $data = $this->webAuthn->processCreate(
            $clientDataRaw,
            $attestationBinary,
            $challengeBuffer,
            false, // requireUserVerification (false: Touch ID handles verification natively)
            true,  // requireUserPresent
            false, // failIfRootMismatch (false: Apple / self-signed passkeys don't have public CA roots)
            false  // requireCtsProfileMatch
        );

        session()->forget('webauthn_reg_challenge');
        cache()->forget("webauthn_reg_challenge_{$user->id}");
        session()->save();

        $deviceName = $this->detectDeviceName($keyName, $userAgent);
        $deviceType = $this->detectDeviceType($userAgent);

        // Convert binary credentialId to Base64Url string to store safely in database
        $credentialIdBase64Url = rtrim(strtr(base64_encode($data->credentialId), '+/', '-_'), '=');

        // Format binary AAGUID (16 bytes) into standard UUID string format for MySQL utf8mb4 storage
        $aaguid = null;
        if (! empty($data->AAGUID)) {
            $rawAaguid = $data->AAGUID instanceof ByteBuffer ? $data->AAGUID->getBinaryString() : (string) $data->AAGUID;
            if (strlen($rawAaguid) === 16) {
                $hex = bin2hex($rawAaguid);
                $aaguid = sprintf('%s-%s-%s-%s-%s',
                    substr($hex, 0, 8),
                    substr($hex, 8, 4),
                    substr($hex, 12, 4),
                    substr($hex, 16, 4),
                    substr($hex, 20, 12)
                );
            } else {
                $aaguid = bin2hex($rawAaguid);
            }
        }

        return $user->passkeys()->create([
            'name'                  => $deviceName,
            'credential_id'         => $credentialIdBase64Url,
            'credential_public_key' => $data->credentialPublicKey,
            'aaguid'                => $aaguid,
            'signature_counter'     => $data->signatureCounter ?? 0,
            'device_type'           => $deviceType,
            'last_used_at'          => now(),
        ]);
    }

    public function getAuthenticationOptions(?string $email = null): \stdClass
    {
        ByteBuffer::$useBase64UrlEncoding = true;

        $credentialIds = [];
        if (!empty($email)) {
            $user = User::where('email', $email)->first();
            if ($user && $user->passkeys()->exists()) {
                $credentialIds = $user->passkeys->map(fn($p) => ByteBuffer::fromBase64Url($p->credential_id))->all();
            }
        }

        $getArgs = $this->webAuthn->getGetArgs($credentialIds, 60, true, true, true, true, true, 'preferred');

        $challengeBase64Url = rtrim(strtr(base64_encode($this->webAuthn->getChallenge()->getBinaryString()), '+/', '-_'), '=');
        session()->put('webauthn_auth_challenge', $challengeBase64Url);
        if (!empty($email)) {
            cache()->put("webauthn_auth_challenge_" . md5($email), $challengeBase64Url, now()->addMinutes(5));
        }
        session()->save();

        return $getArgs;
    }

    public function verifyAuthentication(string $credentialId, string $clientDataJSON, string $authenticatorData, string $signature, ?string $userHandle = null): User
    {
        $challengeBase64Url = session('webauthn_auth_challenge');

        // Lookup passkey by credential_id (handle both raw and clean base64url)
        $cleanCredId = rtrim(strtr($credentialId, '+/', '-_'), '=');
        $passkey = Passkey::with('user')
            ->where('credential_id', $cleanCredId)
            ->orWhere('credential_id', $credentialId)
            ->first();

        // If not found by credential_id, try userHandle (resident key fallback)
        if (! $passkey && ! empty($userHandle)) {
            try {
                $userId = ByteBuffer::fromBase64Url($userHandle)->getBinaryString();
                if ($userId) {
                    $passkey = Passkey::with('user')->where('user_id', $userId)->latest()->first();
                }
            } catch (\Throwable $e) {}
        }

        if (! $challengeBase64Url && $passkey && $passkey->user) {
            $challengeBase64Url = cache()->get("webauthn_auth_challenge_" . md5($passkey->user->email));
        }

        if (! $challengeBase64Url) {
            throw new WebAuthnException('Défi d\'authentification WebAuthn introuvable ou expiré. Veuillez réessayer.');
        }

        if (! $passkey || ! $passkey->user) {
            throw new WebAuthnException('Clé biométrique inconnue ou non enregistrée sur ce serveur.');
        }

        if (! $passkey->user->is_active) {
            throw new WebAuthnException('Ce compte utilisateur a été désactivé.');
        }

        // 1. Decode clientDataJSON to raw JSON string
        $clientDataRaw = $this->decodeClientDataJSON($clientDataJSON);

        // 2. Decode authenticatorData and signature from Base64Url to binary bytes
        $authenticatorDataBinary = ByteBuffer::fromBase64Url($authenticatorData)->getBinaryString();
        $signatureBinary = ByteBuffer::fromBase64Url($signature)->getBinaryString();

        // 3. Challenge as ByteBuffer from Base64Url
        $challengeBuffer = ByteBuffer::fromBase64Url($challengeBase64Url);

        // 4. Process assertion validation via WebAuthn library
        $this->webAuthn->processGet(
            $clientDataRaw,
            $authenticatorDataBinary,
            $signatureBinary,
            $passkey->credential_public_key,
            $challengeBuffer,
            $passkey->signature_counter,
            false, // requireUserVerification
            true   // requireUserPresent
        );

        $passkey->update([
            'signature_counter' => $this->webAuthn->getSignatureCounter() ?? ($passkey->signature_counter + 1),
            'last_used_at'      => now(),
        ]);

        session()->forget('webauthn_auth_challenge');
        cache()->forget("webauthn_auth_challenge_" . md5($passkey->user->email));
        session()->save();

        return $passkey->user;
    }

    private function decodeClientDataJSON(string $clientData): string
    {
        $trimmed = trim($clientData);
        if (str_starts_with($trimmed, '{')) {
            return $trimmed;
        }

        try {
            return ByteBuffer::fromBase64Url($trimmed)->getBinaryString();
        } catch (\Throwable $e) {
            return base64_decode(strtr($trimmed, '-_', '+/')) ?: $trimmed;
        }
    }

    private function detectDeviceName(?string $customName, ?string $userAgent): string
    {
        if (!empty($customName)) {
            return $customName;
        }

        $ua = $userAgent ?? request()->userAgent() ?? '';
        if (str_contains($ua, 'Macintosh')) {
            return 'MacBook / Mac (Touch ID)';
        }
        if (str_contains($ua, 'iPhone')) {
            return 'iPhone (Face ID / Touch ID)';
        }
        if (str_contains($ua, 'iPad')) {
            return 'iPad (Face ID / Touch ID)';
        }
        if (str_contains($ua, 'Windows')) {
            return 'PC Windows (Windows Hello)';
        }
        if (str_contains($ua, 'Android')) {
            return 'Appareil Android (Biométrie)';
        }

        return 'Clé biométrique ' . now()->format('d/m/Y');
    }

    private function detectDeviceType(?string $userAgent): string
    {
        $ua = $userAgent ?? request()->userAgent() ?? '';
        if (str_contains($ua, 'Macintosh') || str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) {
            return 'apple';
        }
        if (str_contains($ua, 'Windows')) {
            return 'windows';
        }
        if (str_contains($ua, 'Android')) {
            return 'android';
        }

        return 'platform';
    }
}

