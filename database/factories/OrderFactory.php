<?php
// database/factories/OrderFactory.php
// database/factories/OrderFactory.php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Customer;
use App\Models\DeliveryZone;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $zone = DeliveryZone::factory()->create();
        $subtotal = $this->faker->numberBetween(1000, 10000);

        return [
            'customer_id' => Customer::factory(),
            'delivery_zone_id' => $zone->id,
            'status' => 'nouvelle',
            'subtotal' => $subtotal,
            'delivery_fee' => $zone->delivery_fee,
            'total' => $subtotal + $zone->delivery_fee,
            'order_number' => 'ORD-' . strtoupper($this->faker->bothify('????###')),
        ];
    }
}
