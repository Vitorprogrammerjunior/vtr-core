<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Streak extends Model
{
    use BelongsToUser;

    protected $guarded = [];
    protected $casts = ['ultimo_dia' => 'date', 'dias' => 'int'];
}
