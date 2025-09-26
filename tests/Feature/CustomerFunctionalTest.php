<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\DeliveryZone;

class CustomerFunctionalTest extends TestCase
{
    public function test_it_can_create_a_customer()
    {
        $zone = DeliveryZone::factory()->create(['is_active' => true]);

        $response = $this->post('/customers', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Rue Exemple',
            'delivery_zone_id' => $zone->id,
        ]);

        $response->assertRedirect(); // les routes web redirigent souvent
        $this->assertDatabaseHas('customers', ['email' => 'john@example.com']);
    }

    public function test_it_can_show_a_customer()
    {
        $customer = Customer::factory()->create();

        $response = $this->get("/customers/{$customer->id}");

        $response->assertStatus(200);
        $response->assertSee($customer->name);
    }
}
