<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseSet extends Model
{
    use BelongsToUser;

    protected $guarded = [];

    protected $casts = [
        'data'       => 'date',
        'feita'      => 'bool',
        'serie_num'  => 'int',
        'carga'      => 'float',
        'reps'       => 'int',
        'segundos'   => 'int',
    ];

    public function exercise(): BelongsTo { return $this->belongsTo(Exercise::class); }
}
