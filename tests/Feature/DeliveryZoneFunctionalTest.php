<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\DeliveryZone;

class DeliveryZoneFunctionalTest extends TestCase
{
    public function test_it_can_create_a_delivery_zone()
    {
        $response = $this->post('/delivery-zones', [
            'name' => 'Zone A',
            'description' => 'Description de Zone A',
            'delivery_fee' => 1000,
            'delivery_time_min' => 15,
            'delivery_time_max' => 30,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('delivery_zones', ['name' => 'Zone A']);
    }

    public function test_it_can_show_a_delivery_zone()
    {
        $zone = DeliveryZone::factory()->create(['is_active' => true]);

        $response = $this->get("/delivery-zones/{$zone->id}");

        $response->assertStatus(200);
        $response->assertSee($zone->name);
    }
}
