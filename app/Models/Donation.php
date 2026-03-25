<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'donor_id',
        'request_id',
        'proof_image',
        'hospital_name',
        'status',
        'points',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class, 'request_id');
    }
}
