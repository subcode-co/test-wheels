<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SpinParticipant extends Model
{
    protected $fillable = [
        'phone',
        'country_code',
        'is_verified',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function spin(): HasOne
    {
        return $this->hasOne(Spin::class);
    }

    public function hasSpun(): bool
    {
        return $this->spin()->exists();
    }
}
