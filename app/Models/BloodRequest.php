<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloodRequest extends Model
{
    protected $table = 'blood_requests';

    protected $fillable = [
        'patient_name',
        'user_id',
        'blood_group',
        'city_id',
        'hospital',
        'message',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function requestResponses(): HasMany
    {
        return $this->hasMany(RequestResponse::class, 'request_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'request_id');
    }
}
