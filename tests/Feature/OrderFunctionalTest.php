<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Customer;
use App\Models\DeliveryZone;

class OrderFunctionalTest extends TestCase
{
    public function test_it_can_create_order()
    {
        $customer = Customer::factory()->create();
        $zone = DeliveryZone::factory()->create(['is_active' => true]);

        $response = $this->post('/orders', [
            'customer_id' => $customer->id,
            'delivery_zone_id' => $zone->id,
            'status' => 'nouvelle',
            'subtotal' => 20000,
            'delivery_fee' => $zone->delivery_fee,
            'total' => 21500,
            'order_number' => 'ORD-001',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', ['customer_id' => $customer->id]);
    }

    public function test_it_can_update_order_status()
    {
        $zone = DeliveryZone::factory()->create();
        $customer = Customer::factory()->create();

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'delivery_zone_id' => $zone->id,
            'status' => 'nouvelle',
            'subtotal' => 1000,
            'delivery_fee' => $zone->delivery_fee,
            'total' => 1200,
        ]);

        // Étape 1 : nouvelle → en_cours_livraison
        $response = $this->patch("/orders/{$order->id}/status", [
            'status' => 'en_cours_livraison',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'en_cours_livraison']);

        // Étape 2 : en_cours_livraison → livree
        $response = $this->patch("/orders/{$order->id}/status", [
            'status' => 'livree',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'livree']);
    }
}
