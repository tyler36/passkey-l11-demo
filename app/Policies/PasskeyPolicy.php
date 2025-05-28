<?php

namespace App\Policies;

use App\Models\Passkey;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PasskeyPolicy
{

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Passkey $passkey): bool
    {
        return $user->id === $passkey->user_id;
    }
}
