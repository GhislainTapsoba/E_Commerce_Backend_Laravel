<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['email', 'sms', 'whatsapp']),
            'category' => $this->faker->randomElement(['order','customer','system']),
            'recipient' => $this->faker->safeEmail,
            'subject' => $this->faker->sentence,
            'message' => $this->faker->paragraph,
            'status' => 'pending',
            'order_id' => Order::factory(),
            'metadata' => null,
            'sent_at' => null,
        ];
    }
}
