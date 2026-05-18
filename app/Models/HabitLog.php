<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitLog extends Model
{
    use BelongsToUser;

    protected $guarded = [];

    protected $casts = [
        'data'  => 'date',
        'feito' => 'bool',
    ];

    public function habit(): BelongsTo { return $this->belongsTo(Habit::class); }
}
