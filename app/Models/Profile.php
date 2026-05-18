<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $guarded = [];

    protected $casts = [
        'modo_disciplina_on' => 'bool',
        'meta_calorias'     => 'int',
        'meta_proteina_g'   => 'int',
        'meta_carbo_g'      => 'int',
        'meta_gordura_g'    => 'int',
        'meta_agua_litros'  => 'decimal:1',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
