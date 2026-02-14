<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spin extends Model
{
    protected $fillable = [
        'spin_participant_id',
        'prize_id',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(SpinParticipant::class, 'spin_participant_id');
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }
}
