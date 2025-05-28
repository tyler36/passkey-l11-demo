<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Str;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;

class PasskeyController extends Controller
{
    public function registerOptions(Request $request): PublicKeyCredentialCreationOptions
    {
        $options = new PublicKeyCredentialCreationOptions(
            // Relying party defines the app that uses the passkey.
            rp: new PublicKeyCredentialRpEntity(
                name: config('app.name'),
                // This is typically the domain, but we shouldn't include the protocol. All subdomains are automatically included too.
                id: parse_url(config('app.url'), PHP_URL_HOST),
            ),
            // User is the person who is registering the options.
            user: new PublicKeyCredentialUserEntity(
                // A unique ID for the the user, typically their email.
                name: $request->user()->email,
                // The internal id. This should not include any identifiable information.
                id: strval($request->user()->id),
                // A user-friendly name to display.
                displayName: $request->user()->name,
            ),
            // Challenge isn't used in the registration, but is still required.
            challenge: Str::random(),
        );

        return $options;
    }
}
