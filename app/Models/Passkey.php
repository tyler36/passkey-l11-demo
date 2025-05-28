<?php

namespace App\Models;

use App\Models\User;
use App\Support\JsonSerializer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webauthn\PublicKeyCredentialSource;

class Passkey extends Model
{
    /** @use HasFactory<\Database\Factories\PasskeyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'credential_id',
        'data',
    ];

    /**
     * RELATIONSHIP: Belongs to a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function data(): Attribute
    {
        return new Attribute(
            get: fn (string $value) => JsonSerializer::deserialize($value, PublicKeyCredentialSource::class),
            set: fn (PublicKeyCredentialSource $value) => [
                'credential_id' => $value->publicKeyCredentialId,
                'data' => JsonSerializer::serialize($value),
            ],
        );
    }
}
