<?php

namespace Tests\Unit;

use App\Models\Car;
use App\Models\User;
use Tests\TestCase;

class CarTest extends TestCase
{
    public function test_model_create()
    {
        $this->assertDatabaseCount('cars', 0);
        /** @var Car $car */
        $car = Car::factory()->create();
        $this->assertDatabaseCount('cars', 1);
        self::assertTrue($car->isFree());
        self::assertEquals('free', $car->status);
    }

    public function test_attached_model_create()
    {
        $this->assertDatabaseCount('cars', 0);
        /** @var Car $car */
        $car = Car::factory()->hasAttached(
            User::factory()->create(),
            [
                'start_date' => now()
            ]
        )->create();
        self::assertCount(1,$cars = Car::all());
        self::assertCount(1, $cars->first()->users);
        self::assertFalse($car->isFree());
        self::assertEquals('busy', $car->status);
    }
}
