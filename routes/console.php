<?php

// routes/console.php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Commande pour nettoyer les anciennes notifications
Artisan::command('notifications:cleanup', function () {
    $deletedCount = \App\Models\Notification::where('created_at', '<', now()->subDays(30))->delete();
    $this->info("Supprimé $deletedCount notifications anciennes.");
})->purpose('Nettoyer les anciennes notifications');

// Commande pour générer des données de test
Artisan::command('generate:test-data', function () {
    $this->info('Génération de données de test...');
    
    // Créer quelques clients de test
    $zones = \App\Models\DeliveryZone::all();
    
    for ($i = 1; $i <= 20; $i++) {
        $customer = \App\Models\Customer::create([
            'name' => 'Client Test ' . $i,
            'phone' => '70' . str_pad($i, 6, '0', STR_PAD_LEFT),
            'email' => 'client' . $i . '@test.com',
            'address' => 'Adresse du client ' . $i,
            'delivery_zone_id' => $zones->random()->id,
        ]);

        // Créer quelques commandes pour ce client
        for ($j = 1; $j <= rand(1, 3); $j++) {
            $zone = $zones->random();
            $subtotal = rand(5000, 50000);
            
            $order = \App\Models\Order::create([
                'customer_id' => $customer->id,
                'delivery_zone_id' => $zone->id,
                'status' => collect(['nouvelle', 'en_cours_livraison', 'livree', 'payee'])->random(),
                'subtotal' => $subtotal,
                'delivery_fee' => $zone->delivery_fee,
                'total' => $subtotal + $zone->delivery_fee,
                'remarks' => 'Commande de test',
                'created_at' => now()->subDays(rand(0, 30)),
            ]);

            // Créer des items pour cette commande
            for ($k = 1; $k <= rand(1, 5); $k++) {
                $unitPrice = rand(1000, 10000);
                $quantity = rand(1, 3);
                
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => rand(1, 100),
                    'product_name' => 'Produit Test ' . $k,
                    'product_sku' => 'SKU-' . $k,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'total_price' => $unitPrice * $quantity,
                ]);
            }
        }
    }
    
    $this->info('Données de test générées avec succès !');
})->purpose('Générer des données de test');