<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestResponse extends Model
{
    protected $fillable = [
        'request_id',
        'donor_id',
        'status',
    ];

    public function bloodRequest(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class, 'request_id');
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }
}
