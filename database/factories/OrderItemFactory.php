<?php

// database/factories/OrderItemFactory.php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        $order = Order::factory()->create();
        $price = $this->faker->numberBetween(1000, 10000);
        $quantity = $this->faker->numberBetween(1, 5);

        return [
            'order_id' => $order->id,
            'product_name' => $this->faker->word(),
            'quantity' => $quantity,
            'price' => $price,
            'total' => $price * $quantity,
        ];
    }
}
