<?php
// database/factories/DeliveryZoneFactory.php

namespace Database\Factories;

use App\Models\DeliveryZone;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryZoneFactory extends Factory
{
    protected $model = DeliveryZone::class;

    public function definition()
    {
        return [
            'name' => $this->faker->city(),
            'description' => $this->faker->sentence(),
            'delivery_fee' => $this->faker->numberBetween(500, 5000), // <== corrigé
            'delivery_time_min' => $this->faker->numberBetween(15, 30),
            'delivery_time_max' => $this->faker->numberBetween(31, 60),
            'is_active' => true,
        ];
    }
}
