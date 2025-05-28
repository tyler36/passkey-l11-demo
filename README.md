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
