<?php

// routes/api.php

use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\Api\DeliveryZoneController as ApiDeliveryZoneController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('api')->group(function () {
    
    // Routes publiques (pour le frontend Next.js)
    Route::prefix('v1')->group(function () {
        
        // Zones de livraison
        Route::get('delivery-zones', [ApiDeliveryZoneController::class, 'index']);
        Route::get('delivery-zones/{id}', [ApiDeliveryZoneController::class, 'show']);
        
        // Calculer les frais de livraison
        Route::post('calculate-delivery', [ApiDeliveryZoneController::class, 'calculateDelivery']);
        
        // Créer une commande depuis le frontend
        Route::post('orders', [ApiOrderController::class, 'store']);
        
        // Vérifier le statut d'une commande
        Route::get('orders/{orderNumber}/status', [ApiOrderController::class, 'checkStatus']);
        
        // Synchronisation avec Strapi (webhook)
        Route::post('sync/products', [ApiProductController::class, 'syncFromStrapi']);
    });

    // Routes authentifiées (pour l'administration)
    Route::middleware('auth:sanctum')->prefix('v1/admin')->group(function () {
        
        // Commandes
        Route::apiResource('orders', ApiOrderController::class);
        Route::patch('orders/{order}/status', [ApiOrderController::class, 'updateStatus']);
        
        // Zones de livraison
        Route::apiResource('delivery-zones', ApiDeliveryZoneController::class);
        
        // Statistiques
        Route::get('statistics/dashboard', function () {
            return response()->json([
                'total_orders' => \App\Models\Order::count(),
                'pending_orders' => \App\Models\Order::where('status', 'nouvelle')->count(),
                'delivered_orders' => \App\Models\Order::where('status', 'livree')->count(),
                'total_revenue' => \App\Models\Order::where('status', '!=', 'annulee')->sum('total'),
            ]);
        });
    });
});
