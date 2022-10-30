<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\HasApiTokens;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function newFactory()
    {
        return new UserFactory();
    }

    public function cars(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Car::class);

    }

    public function hasNoCars(): bool
    {
        return $this->cars()->whereNull('end_date')->count() == 0;
    }

    public function takeCar(Car $car)
    {
        if (!$this->hasNoCars())
            throw new UnauthorizedHttpException('Another car is took');
        if (!$car->isFree())
            throw new UnauthorizedHttpException('Car is busy');
        $car->users()->attach($this,[
            'start_date' => now()
        ]);
    }

    public function giveCar(Car $car)
    {
        $currentUser = $car->getCurrentUser();
        if (!$currentUser or $currentUser->id !== $this->id)
            throw new UnauthorizedHttpException('Action is not allowed');
        $car->currentUsers()->updateExistingPivot($car->id,['end_date'=>now()]);
    }
}
