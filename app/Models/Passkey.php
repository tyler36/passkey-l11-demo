<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passkey extends Model
{
    /** @use HasFactory<\Database\Factories\PasskeyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'credential_id',
        'data'
    ];

    /**
     * RELATIONSHIP: Belongs to a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'data' => 'json',
        ];
    }
}
