<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workout extends Model
{
    use BelongsToUser, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'ativo'      => 'bool',
        'dia_semana' => 'int',
        'ordem'      => 'int',
    ];

    public function exercises(): HasMany { return $this->hasMany(Exercise::class)->orderBy('ordem'); }
    public function sessions(): HasMany  { return $this->hasMany(WorkoutSession::class); }
}
