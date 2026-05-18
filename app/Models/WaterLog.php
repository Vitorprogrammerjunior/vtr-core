<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class WaterLog extends Model
{
    use BelongsToUser;

    protected $guarded = [];

    protected $casts = [
        'data'   => 'date',
        'copos'  => 'int',
        'litros' => 'decimal:2',
    ];
}
