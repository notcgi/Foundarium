<?php

namespace App\Policies;

use App\Models\Car;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CarPolicy
{
    use HandlesAuthorization;

    public function take(User $user, Car $car)
    {
        return $car->isFree() and $user->hasNoCars();
    }
    public function give(User $user, Car $car)
    {
        $currentUser = $car->getCurrentUser();
        return ($currentUser or $currentUser->id == $user->id);
    }
}
