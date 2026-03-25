<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    protected $fillable = [
        'user_id',
        'blood_group',
        'age',
        'last_donation_date',
        'is_available',
        'is_enabled',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'last_donation_date' => 'date',
            'age' => 'integer',
            'is_available' => 'boolean',
            'is_enabled' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requestResponses(): HasMany
    {
        return $this->hasMany(RequestResponse::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
