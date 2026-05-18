<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use BelongsToUser, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'numero' => 'int',
        'ordem'  => 'int',
    ];

    public function book(): BelongsTo { return $this->belongsTo(Book::class); }
}
