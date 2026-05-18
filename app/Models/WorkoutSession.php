<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkoutSession extends Model
{
    use BelongsToUser, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'agendado_em'        => 'datetime',
        'concluido_em'       => 'datetime',
        'percent'            => 'int',
        'exercicios_feitos'  => 'int',
        'exercicios_total'   => 'int',
    ];

    public function workout(): BelongsTo { return $this->belongsTo(Workout::class); }
}
