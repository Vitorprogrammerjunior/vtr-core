<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use BelongsToUser, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'series'        => 'int',
        'ordem'         => 'int',
        'rep_min'       => 'int',
        'rep_max'       => 'int',
        'segundos_min'  => 'int',
        'segundos_max'  => 'int',
        'por_lado'      => 'bool',
    ];

    public function workout(): BelongsTo { return $this->belongsTo(Workout::class); }
    public function sets(): HasMany      { return $this->hasMany(ExerciseSet::class); }
}
