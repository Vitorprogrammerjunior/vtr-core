<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ============== Relations ==============
    public function profile(): HasOne   { return $this->hasOne(Profile::class); }
    public function streak(): HasOne    { return $this->hasOne(Streak::class); }

    public function goals(): HasMany           { return $this->hasMany(Goal::class); }
    public function habits(): HasMany          { return $this->hasMany(Habit::class); }
    public function workouts(): HasMany        { return $this->hasMany(Workout::class); }
    public function exercises(): HasMany       { return $this->hasMany(Exercise::class); }
    public function workoutSessions(): HasMany { return $this->hasMany(WorkoutSession::class); }
    public function meals(): HasMany           { return $this->hasMany(Meal::class); }
    public function mealLogs(): HasMany        { return $this->hasMany(MealLog::class); }
    public function nutritionLogs(): HasMany   { return $this->hasMany(NutritionLog::class); }
    public function waterLogs(): HasMany       { return $this->hasMany(WaterLog::class); }
    public function books(): HasMany           { return $this->hasMany(Book::class); }
    public function pages(): HasMany           { return $this->hasMany(Page::class); }
}
