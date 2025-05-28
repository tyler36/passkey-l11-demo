<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Passkey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Throwable;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialCreationOptions;

class PasskeyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check 'passkey' contains valid data.
        $data = $request->validateWithBag('createPasskey', [
            'name' => ['required', 'string', 'max:255'],
            'passkey' => ['required', 'json'],
        ]);

        /**
         * De-serialize the passkey as credentials.
         *
         * @var PublicKeyCredential $publicKeyCredential
         */
        $publicKeyCredential = (new WebauthnSerializerFactory(AttestationStatementSupportManager::create()))
            ->create()
            ->deserialize($data['passkey'], PublicKeyCredential::class, 'json');

        // Ensure response is a registration response.
        if (!$publicKeyCredential->response instanceof AuthenticatorAttestationResponse) {
            return to_route('login');
        }

        try {
            /**
             * Get options back from session.
             *
             * @see PasskeyApiController::registerOptions().
             *
             * @var PublicKeyCredentialCreationOptions $passkeyOptions
             */
            $passkeyOptions = Session::get('passkey-registration-options');

            $publicKeyCredentialSource = AuthenticatorAttestationResponseValidator::create()->check(
                authenticatorAttestationResponse: $publicKeyCredential->response,
                publicKeyCredentialCreationOptions: $passkeyOptions,
                request: $request->getHost(),
            );

        } catch (\Throwable $th) {
            throw ValidationException::withMessages([
                'name' => 'The given passkey is invalid',
            ])->errorBag(errorBag: 'createPasskey');
        }

        // Save passkey
        $request->user()->passkeys()->create([
            'name' => $data['name'],
            'data' => $publicKeyCredentialSource,
        ]);

        return to_route('profile.edit')->withFragment('managePasskeys');
    }


    // Authenticate Passkey with previously stored Passkey
    public function authenticate(Request $request) {
        // Check 'passkey' contains valid data.
        $data = $request->validate([
            'answer' => ['required', 'json'],
        ]);

        /**
         * De-serialize the passkey as credentials.
         *
         * @var PublicKeyCredential $publicKeyCredential
         */
        $publicKeyCredential = (new WebauthnSerializerFactory(AttestationStatementSupportManager::create()))
            ->create()
            ->deserialize($data['answer'], PublicKeyCredential::class, 'json');

        $passkey = Passkey::firstWhere('credential_id', $publicKeyCredential->rawId);
        if (!$passkey) {
            throw ValidationException::withMessages(['answer' => 'This passkey is not valid']);
        }

        // Ensure response is a registration response.
        if (!$publicKeyCredential->response instanceof AuthenticatorAssertionResponse) {
            return to_route('profile.edit')->withFragment('managePasskeys');
        }

        try {
            $publicKeyCredentialSource = AuthenticatorAssertionResponseValidator::create()->check(
                credentialId: $passkey->data,
                authenticatorAssertionResponse: $publicKeyCredential->response,
                publicKeyCredentialRequestOptions: Session::get('passkey-authentication-options'),
                request: $request->getHost(),
                userHandle: null
            );

        } catch (Throwable $th) {
            throw ValidationException::withMessages([
                'name' => 'This passkey is not valid',
            ]);
        }

        Auth::loginUsingId($passkey->user_id);

        $request->session()->regenerate();

        return to_route('dashboard');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Passkey $passkey): RedirectResponse
    {
        Gate::authorize('delete', $passkey);

        $passkey->delete();

        return to_route('profile.edit')->withFragment('managePasskeys');

    }
}
