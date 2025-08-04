<?php

// database/seeders/DeliveryZoneSeeder.php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    public function run()
    {
        $zones = [
            [
                'name' => 'Centre-ville',
                'description' => 'Zone du centre-ville de Ouagadougou',
                'delivery_fee' => 500,
                'delivery_time_min' => 30,
                'delivery_time_max' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Périphérie Nord',
                'description' => 'Zones périphériques au nord de la ville',
                'delivery_fee' => 1000,
                'delivery_time_min' => 60,
                'delivery_time_max' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Périphérie Sud',
                'description' => 'Zones périphériques au sud de la ville',
                'delivery_fee' => 1000,
                'delivery_time_min' => 60,
                'delivery_time_max' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Banlieue',
                'description' => 'Zones de banlieue éloignées',
                'delivery_fee' => 2000,
                'delivery_time_min' => 120,
                'delivery_time_max' => 180,
                'is_active' => false,
            ],
        ];

        foreach ($zones as $zone) {
            DeliveryZone::create($zone);
        }
    }
}