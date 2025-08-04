<?php

// app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Synchroniser les produits depuis Strapi (webhook)
     */
    public function syncFromStrapi(Request $request): JsonResponse
    {
        try {
            // Vérifier la signature du webhook si configurée
            $expectedSignature = config('app.strapi_webhook_secret');
            if ($expectedSignature) {
                $signature = $request->header('X-Strapi-Signature');
                if (!hash_equals($expectedSignature, $signature)) {
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }

            $event = $request->input('event');
            $data = $request->input('data');

            Log::info('Webhook Strapi reçu', [
                'event' => $event,
                'product_id' => $data['id'] ?? 'unknown'
            ]);

            // Traiter l'événement selon le type
            switch ($event) {
                case 'entry.create':
                case 'entry.update':
                    $this->handleProductUpdate($data);
                    break;
                    
                case 'entry.delete':
                    $this->handleProductDelete($data);
                    break;
                    
                default:
                    Log::warning('Événement Strapi non géré: ' . $event);
            }

            return response()->json([
                'success' => true,
                'message' => 'Synchronisation effectuée'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur synchronisation Strapi: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation'
            ], 500);
        }
    }

    /**
     * Récupérer les produits depuis Strapi
     */
    public function getProductsFromStrapi(): JsonResponse
    {
        try {
            $strapiUrl = config('app.strapi_url');
            $response = Http::get($strapiUrl . '/api/products?populate=*');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des produits'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Erreur récupération produits Strapi: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur de connexion à Strapi'
            ], 500);
        }
    }

    private function handleProductUpdate(array $data)
    {
        // Ici on pourrait mettre à jour un cache local des produits
        // ou déclencher d'autres actions selon les besoins
        Log::info('Produit mis à jour', ['product_id' => $data['id']]);
    }

    private function handleProductDelete(array $data)
    {
        // Gérer la suppression d'un produit
        Log::info('Produit supprimé', ['product_id' => $data['id']]);
    }
}
