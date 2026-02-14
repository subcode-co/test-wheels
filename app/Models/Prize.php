<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prize extends Model
{
    protected $fillable = [
        'name',
        'code',
        'probability_weight',
        'display_order',
        'color',
        'is_winner',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_winner' => 'boolean',
        'probability_weight' => 'integer',
        'display_order' => 'integer',
    ];

    public function spins(): HasMany
    {
        return $this->hasMany(Spin::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('id');
    }
}
