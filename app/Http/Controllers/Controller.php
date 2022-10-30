<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Gate;

class Controller extends BaseController
{

    public function list()
    {
        return Car::query()->paginate();
    }

    public function take(Car $car)
    {
        Gate::authorize('take', $car);
        auth()->user()->takeCar($car);
        return $car;
    }

    public function give(Car $car)
    {
        Gate::authorize('give', $car);
        auth()->user()->giveCar($car);
        return $car;
    }
}
