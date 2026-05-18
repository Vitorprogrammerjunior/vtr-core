<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealLog extends Model
{
    use BelongsToUser;

    protected $guarded = [];

    protected $casts = [
        'data'  => 'date',
        'feita' => 'bool',
        'kcal'  => 'int',
    ];

    public function meal(): BelongsTo { return $this->belongsTo(Meal::class); }
}
