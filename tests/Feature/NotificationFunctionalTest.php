<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Customer;
use App\Models\DeliveryZone;

class NotificationFunctionalTest extends TestCase
{
    public function test_it_can_list_notifications()
    {
        $zone = DeliveryZone::factory()->create(['is_active' => true]);
        $customer = Customer::factory()->create();

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'delivery_zone_id' => $zone->id,
            'status' => 'nouvelle',
            'subtotal' => 2000,
            'delivery_fee' => $zone->delivery_fee,
            'total' => 2200,
            'order_number' => 'ORD-001',
        ]);

        Notification::factory()->create([
            'message' => 'Commande ORD-001 créée',
            'order_id' => $order->id,
        ]);

        $response = $this->get('/notifications');

        $response->assertStatus(200);
        $response->assertSee('Commande ORD-001 créée');
    }

    public function test_it_can_delete_notification()
    {
        $zone = DeliveryZone::factory()->create(['is_active' => true]);
        $customer = Customer::factory()->create();

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'delivery_zone_id' => $zone->id,
            'status' => 'nouvelle',
            'subtotal' => 1500,
            'delivery_fee' => $zone->delivery_fee,
            'total' => 1600,
            'order_number' => 'ORD-003',
        ]);

        $notification = Notification::factory()->create([
            'message' => 'Commande ORD-003 créée',
            'order_id' => $order->id,
        ]);

        $response = $this->delete("/notifications/{$notification->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }
}
