<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Refeição planejada (template). A execução por dia fica em MealLog.
 */
class Meal extends Model
{
    use BelongsToUser, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'horario'    => 'string',
        'ativo'      => 'bool',
        'ordem'      => 'int',
        'dia_semana' => 'int',
        'proteina_g' => 'int',
    ];

    public function logs(): HasMany { return $this->hasMany(MealLog::class); }
}
