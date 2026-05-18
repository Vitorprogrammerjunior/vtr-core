<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    use BelongsToUser, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'prazo'                  => 'date',
        'ativo'                  => 'bool',
        'progresso'              => 'int',
        'meta_valor'             => 'decimal:2',
        'valor_atual'            => 'decimal:2',
        'total_marcadores'       => 'int',
        'marcadores_concluidos'  => 'int',
        'ordem'                  => 'int',
    ];
}
