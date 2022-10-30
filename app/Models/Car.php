<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\CarFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Car extends Model
{
    use HasFactory;

    protected $visible = [
        'id',
        'number',
        'model',
        'status'
    ];

    protected $appends = [
        'status'
    ];

    protected static function newFactory(): CarFactory
    {
        return new CarFactory();
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function currentUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->users()->wherePivotNull('end_date');
    }

    public function getCurrentUser(): User|null
    {
        return $this->currentUsers()?->first() ?? null;
    }

    public function getStatusAttribute(): string
    {
        return $this->isFree() ? 'free' : 'busy';
    }

    public function isFree(): bool
    {
        return $this->users()->whereNull('end_date')->count() == 0;
    }
}
