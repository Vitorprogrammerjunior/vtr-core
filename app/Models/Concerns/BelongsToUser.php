<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Scope;
use App\Models\User;

/**
 * Aplica isolamento por usuário em todos os modelos que herdam dessa trait.
 *
 * - Global scope: SELECT/UPDATE/DELETE só enxergam linhas do auth()->id().
 * - creating: preenche user_id automaticamente quando ausente.
 *
 * Usar somente em tabelas que tenham coluna `user_id` indexada.
 * Para queries internas (jobs/console) sem auth, use `->withoutGlobalScope(BelongsToUserScope::class)`.
 */
trait BelongsToUser
{
    protected static function bootBelongsToUser(): void
    {
        static::addGlobalScope(new BelongsToUserScope);

        static::creating(function (Model $model) {
            if (auth()->check() && empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

class BelongsToUserScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $builder->where($model->getTable() . '.user_id', auth()->id());
        }
    }
}
