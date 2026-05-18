<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use BelongsToUser, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'ordem' => 'int',
    ];

    public function pages(): HasMany { return $this->hasMany(Page::class)->orderBy('ordem'); }
}
