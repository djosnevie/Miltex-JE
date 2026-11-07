/**
 * Miltex EAJE — WebAuthn & Passkeys Client Helper
 * Gère l'authentification biométrique (Touch ID, Face ID, Windows Hello, FIDO2)
 */

function base64urlToBuffer(base64url) {
    if (!base64url) return new ArrayBuffer(0);
    if (base64url instanceof ArrayBuffer) return base64url;
    if (base64url.buffer instanceof ArrayBuffer) return base64url.buffer;

    let str = String(base64url).trim();
    if (str.startsWith('=?BINARY?B?') && str.endsWith('?=')) {
        str = str.slice(11, -2);
    }
    let base64 = str.replace(/-/g, '+').replace(/_/g, '/');
    while (base64.length % 4) {
        base64 += '=';
    }
    const binary = atob(base64);
    const bytes = new Uint8Array(binary.length);
    for (let i = 0; i < binary.length; i++) {
        bytes[i] = binary.charCodeAt(i);
    }
    return bytes.buffer;
}

function bufferToBase64url(buffer) {
    if (!buffer) return '';
    const bytes = new Uint8Array(buffer);
    let binary = '';
    const len = bytes.byteLength;
    for (let i = 0; i < len; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    return btoa(binary)
        .replace(/\+/g, '-')
        .replace(/\//g, '_')
        .replace(/=/g, '');
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
        || document.querySelector('input[name="_token"]')?.value 
        || '';
}

window.Passkeys = {
    /**
     * Vérifie si le navigateur supporte WebAuthn et les biométries (Touch ID / Face ID)
     */
    isSupported() {
        return !!(window.PublicKeyCredential && navigator.credentials && navigator.credentials.create && navigator.credentials.get);
    },

    async isPlatformAuthenticatorAvailable() {
        if (!this.isSupported() || !window.PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable) {
            return false;
        }
        try {
            return await window.PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
        } catch (e) {
            return false;
        }
    },

    /**
     * Enregistre un nouvel appareil / clé d'accès (Touch ID / Face ID)
     */
    async register(name = null) {
        if (!this.isSupported()) {
            throw new Error('L\'authentification biométrique n\'est pas supportée par ce navigateur.');
        }

        // 1. Récupération des options depuis le serveur
        const optResponse = await fetch('/passkeys/register-options', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        if (!optResponse.ok) {
            const err = await optResponse.json().catch(() => ({}));
            throw new Error(err.error || 'Erreur lors de la préparation de l\'enregistrement.');
        }

        const options = await optResponse.json();

        // 2. Conversion des champs base64url en ArrayBuffer pour le navigateur
        options.publicKey.challenge = base64urlToBuffer(options.publicKey.challenge);
        options.publicKey.user.id = base64urlToBuffer(options.publicKey.user.id);

        if (Array.isArray(options.publicKey.excludeCredentials)) {
            options.publicKey.excludeCredentials.forEach(cred => {
                cred.id = base64urlToBuffer(cred.id);
            });
        }

        // 3. Appel à l'API native du navigateur (Déclenche Touch ID / Face ID)
        const credential = await navigator.credentials.create({
            publicKey: options.publicKey
        });

        if (!credential) {
            throw new Error('Aucun identifiant biométrique n\'a été généré.');
        }

        // 4. Envoi de l'attestation au serveur pour validation
        const payload = {
            name: name,
            clientDataJSON: bufferToBase64url(credential.response.clientDataJSON),
            attestationObject: bufferToBase64url(credential.response.attestationObject)
        };

        const verifyResponse = await fetch('/passkeys/register', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(payload)
        });

        const result = await verifyResponse.json();

        if (!verifyResponse.ok) {
            throw new Error(result.error || 'Validation de la clé biométrique échouée.');
        }

        return result;
    },

    /**
     * Authentification avec Touch ID / Face ID / Clé d'accès
     */
    async authenticate(email = null) {
        if (!this.isSupported()) {
            throw new Error('L\'authentification biométrique n\'est pas supportée par ce navigateur.');
        }

        // 1. Récupération des options du défi d'authentification
        const optResponse = await fetch('/passkeys/login-options', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ email: email })
        });

        if (!optResponse.ok) {
            const err = await optResponse.json().catch(() => ({}));
            throw new Error(err.error || 'Erreur lors de l\'initialisation de la connexion biométrique.');
        }

        const options = await optResponse.json();

        // 2. Conversion base64url -> ArrayBuffer
        options.publicKey.challenge = base64urlToBuffer(options.publicKey.challenge);

        if (Array.isArray(options.publicKey.allowCredentials)) {
            options.publicKey.allowCredentials.forEach(cred => {
                cred.id = base64urlToBuffer(cred.id);
            });
        }

        // 3. Appel natif navigator.credentials.get (Affiche la boîte de dialogue Touch ID / Face ID)
        const assertion = await navigator.credentials.get({
            publicKey: options.publicKey
        });

        if (!assertion) {
            throw new Error('Authentification biométrique annulée ou introuvable.');
        }

        // 4. Envoi de l'assertion au serveur pour validation et connexion de session
        const payload = {
            id: assertion.id,
            clientDataJSON: bufferToBase64url(assertion.response.clientDataJSON),
            authenticatorData: bufferToBase64url(assertion.response.authenticatorData),
            signature: bufferToBase64url(assertion.response.signature),
            userHandle: assertion.response.userHandle ? bufferToBase64url(assertion.response.userHandle) : null
        };

        const loginResponse = await fetch('/passkeys/login', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(payload)
        });

        const result = await loginResponse.json();

        if (!loginResponse.ok) {
            throw new Error(result.error || 'Échec de la validation de la clé d\'accès.');
        }

        return result;
    }
};
