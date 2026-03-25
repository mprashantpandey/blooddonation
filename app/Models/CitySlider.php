<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CitySlider extends Model
{
    protected $fillable = [
        'city_id',
        'title',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }
}

