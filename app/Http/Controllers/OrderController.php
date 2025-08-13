<?php

// app/Http/Controllers/OrderController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\DeliveryZone;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

   public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->middleware('permission:orders.view')->only(['index', 'show']);
        $this->middleware('permission:orders.create')->only(['create', 'store']);
        $this->middleware('permission:orders.edit')->only(['edit', 'update', 'updateStatus']);
        $this->middleware('permission:orders.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'deliveryZone']);

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('zone')) {
            $query->where('delivery_zone_id', $request->zone);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(15);
        $deliveryZones = DeliveryZone::where('is_active', true)->get();

        return view('orders.index', compact('orders', 'deliveryZones'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'deliveryZone', 'items', 'notifications']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $deliveryZones = DeliveryZone::where('is_active', true)->get();
        return view('orders.edit', compact('order', 'deliveryZones'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:nouvelle,en_cours_livraison,livree,annulee,payee',
            'delivery_zone_id' => 'required|exists:delivery_zones,id',
            'remarks' => 'nullable|string'
        ]);

        $oldStatus = $order->status;
        
        $order->update([
            'status' => $request->status,
            'delivery_zone_id' => $request->delivery_zone_id,
            'remarks' => $request->remarks,
            'delivered_at' => $request->status === 'livree' ? now() : null,
        ]);

        // Envoyer une notification si le statut a changé
        if ($oldStatus !== $request->status) {
            $this->orderService->sendStatusUpdateNotification($order);
        }

        return redirect()->route('orders.index')
            ->with('success', 'Commande mise à jour avec succès.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')
            ->with('success', 'Commande supprimée avec succès.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:nouvelle,en_cours_livraison,livree,annulee,payee'
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Logique métier pour les transitions de statut
        $allowedTransitions = [
            'nouvelle' => ['en_cours_livraison', 'annulee'],
            'en_cours_livraison' => ['livree', 'nouvelle', 'annulee'],
            'livree' => ['payee', 'en_cours_livraison'],
            'payee' => [], // Statut final
            'annulee' => ['nouvelle'] // Peut être réactivée
        ];

        if (!in_array($newStatus, $allowedTransitions[$oldStatus] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => 'Transition de statut non autorisée'
            ], 400);
        }

        // Mettre à jour le statut avec la logique appropriée
        $updateData = [
            'status' => $newStatus,
        ];

        // Ajouter des champs spécifiques selon le statut
        switch ($newStatus) {
            case 'livree':
                $updateData['delivered_at'] = $updateData['delivered_at'] ?? now();
                break;
            case 'payee':
                $updateData['paid_at'] = now();
                break;
            case 'annulee':
                $updateData['cancelled_at'] = now();
                $updateData['delivered_at'] = null;
                $updateData['paid_at'] = null;
                break;
            case 'nouvelle':
                // Reset des timestamps si on remet en "nouvelle"
                $updateData['delivered_at'] = null;
                $updateData['paid_at'] = null;
                $updateData['cancelled_at'] = null;
                break;
        }

        $order->update($updateData);

        // Envoyer une notification si le statut a changé
        if ($oldStatus !== $newStatus) {
            $this->orderService->sendStatusUpdateNotification($order);
        }

        // Si c'est une requête AJAX, retourner JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'data' => [
                    'order_number' => $order->order_number,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'status_badge' => $order->status_badge,
                    'status_label' => ucfirst(str_replace('_', ' ', $newStatus))
                ]
            ]);
        }

        // Sinon rediriger avec message de succès
        $statusLabels = [
            'nouvelle' => 'Nouvelle',
            'en_cours_livraison' => 'En cours de livraison',
            'livree' => 'Livrée',
            'annulee' => 'Annulée',
            'payee' => 'Payée'
        ];

        $message = "Commande #{$order->order_number} marquée comme '{$statusLabels[$newStatus]}'";

        return redirect()->back()->with('success', $message);
    }
}
