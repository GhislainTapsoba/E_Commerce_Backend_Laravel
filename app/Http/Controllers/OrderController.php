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
        $this->middleware('permission:orders.edit')->only(['edit', 'update']);
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
}
