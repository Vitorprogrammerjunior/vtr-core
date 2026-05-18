<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class NutritionLog extends Model
{
    use BelongsToUser;

    protected $guarded = [];

    protected $casts = [
        'data'           => 'date',
        'kcal_consumido' => 'int',
        'proteina_g'     => 'int',
        'carbo_g'        => 'int',
        'gordura_g'      => 'int',
    ];
}
