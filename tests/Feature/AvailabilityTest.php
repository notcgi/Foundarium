<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\User;
use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    public function testList()
    {
        /** @var Car[] $cars */
        Car::factory(50)->create();
        $this->get(route('list'))
            ->dump()
            ->assertSuccessful()
            ->assertJsonStructure([
                'data'=>[
                    '*' => [
                        'id',
                        'number',
                        'status',
                    ]
                ]
            ])
            ->assertJsonPath('total',50);
    }
    /**
     * @throws \Exception
     */
    public function testTake()
    {
        /** @var Car $car */
        $car = Car::factory()->create();
        /** @var User $car */
        $user = User::factory()->create();

        auth()->login($user);

        $this->get(route('take', ['car'=>$car->id]))
            ->assertSuccessful()
            ->assertJsonPath('status','busy');
    }

    /**
     * @throws \Exception
     */
    public function testRepeatedTake()
    {
        /** @var Car $car */
        $car = Car::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        $user->takeCar($car);

        auth()->login($user);

        $this->get(route('take', ['car'=>$car->id]))
            ->assertForbidden();
    }

    /**
     * @throws \Exception
     */
    public function testTakeAnotherCar()
    {
        /** @var Car[] $cars */
        $cars = Car::factory(2)->create();
        /** @var User $user */
        $user = User::factory()->create();
        $user->takeCar($cars[0]);

        auth()->login($user);

        $this->get(route('take', ['car'=>$cars[1]->id]))
            ->assertForbidden();
    }

    public function testTakeBusyCar()
    {
        /** @var Car[] $cars */
        $car = Car::factory()->create();
        /** @var User[] $users */
        $users = User::factory(2)->create();
        $users[0]->takeCar($car);

        auth()->login($users[1]);

        $this->get(route('give', ['car'=>$car->id]))
            ->assertForbidden();
    }

    /**
     * @throws \Exception
     */
    public function testGive()
    {
        /** @var Car $car */
        $car = Car::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        $user->takeCar($car);

        auth()->login($user);

        $this->get(route('give', ['car'=>$car->id]))
            ->assertSuccessful()
            ->assertJsonPath('status','free');
    }

    public function testNotFoundCar()
    {
        $this->get(route('give', ['car'=>0]))
            ->assertNotFound();
        $this->get(route('take', ['car'=>0]))
            ->assertNotFound();
    }
}
