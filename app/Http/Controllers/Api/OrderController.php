<?php
// app/Http/Controllers/Api/OrderController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Créer une commande depuis le frontend Next.js
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'customer.name' => 'required|string|max:255',
                'customer.phone' => 'required|string|max:20',
                'customer.email' => 'nullable|email|max:255',
                'customer.address' => 'required|string',
                'delivery_zone_id' => 'required|exists:delivery_zones,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer',
                'items.*.product_name' => 'required|string',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.total_price' => 'required|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'delivery_fee' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'remarks' => 'nullable|string',
            ]);

            $order = $this->orderService->createOrderFromFrontend($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'data' => [
                    'order_number' => $order->order_number,
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'total' => $order->total,
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier le statut d'une commande
     */
    public function checkStatus(string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['customer', 'deliveryZone'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'total' => $order->total,
                'customer_name' => $order->customer->name,
                'delivery_zone' => $order->deliveryZone->name,
                'delivered_at' => $order->delivered_at?->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Liste des commandes (pour admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['customer', 'deliveryZone']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('zone')) {
            $query->where('delivery_zone_id', $request->zone);
        }

        $orders = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Détails d'une commande (pour admin)
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['customer', 'deliveryZone', 'items']);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:nouvelle,en_cours_livraison,livree,annulee,payee'
        ]);

        $this->orderService->updateOrderStatus($order, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'data' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
            ]
        ]);
    }
}
