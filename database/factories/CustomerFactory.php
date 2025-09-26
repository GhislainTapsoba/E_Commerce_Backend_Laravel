<?php
// database/factories/CustomerFactory.php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\DeliveryZone;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'delivery_zone_id' => DeliveryZone::factory(),
        ];
    }
}
