<?php

// app/Http/Controllers/Api/DeliveryZoneController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DeliveryZoneController extends Controller
{
    /**
     * Liste des zones de livraison actives
     */
    public function index(): JsonResponse
    {
        $zones = DeliveryZone::where('is_active', true)
            ->select('id', 'name', 'description', 'delivery_fee', 'delivery_time_min', 'delivery_time_max')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $zones
        ]);
    }

    /**
     * Détails d'une zone de livraison
     */
    public function show(int $id): JsonResponse
    {
        $zone = DeliveryZone::where('is_active', true)->find($id);

        if (!$zone) {
            return response()->json([
                'success' => false,
                'message' => 'Zone de livraison non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $zone
        ]);
    }

    /**
     * Calculer les frais de livraison
     */
    public function calculateDelivery(Request $request): JsonResponse
    {
        $request->validate([
            'delivery_zone_id' => 'required|exists:delivery_zones,id',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $zone = DeliveryZone::find($request->delivery_zone_id);

        if (!$zone->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Cette zone de livraison n\'est pas disponible'
            ], 400);
        }

        $deliveryFee = $zone->delivery_fee;
        
        // Appliquer des règles de livraison gratuite si nécessaire
        if ($request->subtotal >= config('app.free_delivery_threshold', 50000)) {
            $deliveryFee = 0;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'delivery_fee' => $deliveryFee,
                'delivery_time_min' => $zone->delivery_time_min,
                'delivery_time_max' => $zone->delivery_time_max,
                'zone_name' => $zone->name,
                'subtotal' => $request->subtotal,
                'total' => $request->subtotal + $deliveryFee,
            ]
        ]);
    }
}
