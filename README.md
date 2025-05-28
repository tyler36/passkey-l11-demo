# Passkeys with Laravel <!-- omit in toc -->

## Overview

This project is a follow-along of ["Add Passkeys to a Laravel App"](https://laracasts.com/series/add-passkeys-to-a-laravel-app/episodes/1) Laracast course.

The stack is:

- Laravel 11
- AlpineJs
- PHPUnit
- Breeze starter-kit

## Database

Passkey model:

```php
// A passkey belongs to a user
$table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
// The human-friendly name
$table->text('name');
// The unique credential id used to identify the passkey. This *must* be a binary type.
$table->binary('credential_id');
// Data associated with the key.
$table->json('data');
```

## Steps

### Registering

1. Create a backend API route that returns a `PublicKeyCredentialCreationOptions`.

    ```php
    return new PublicKeyCredentialCreationOptions(
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
    ```

    This will automatically be turned to JSON by Laravel.
    Webauthn re-encodes `user->id` as base64-url to keep anonymous.
