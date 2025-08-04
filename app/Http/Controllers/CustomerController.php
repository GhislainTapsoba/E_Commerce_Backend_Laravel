<?php

// app/Http/Controllers/CustomerController.php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:customers.view')->only(['index', 'show']);
        $this->middleware('permission:customers.create')->only(['create', 'store']);
        $this->middleware('permission:customers.edit')->only(['edit', 'update']);
        $this->middleware('permission:customers.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Customer::with(['deliveryZone', 'orders']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('zone')) {
            $query->where('delivery_zone_id', $request->zone);
        }

        $customers = $query->withCount('orders')->paginate(15);
        $deliveryZones = DeliveryZone::where('is_active', true)->get();

        return view('customers.index', compact('customers', 'deliveryZones'));
    }

    public function create()
    {
        $deliveryZones = DeliveryZone::where('is_active', true)->get();
        return view('customers.create', compact('deliveryZones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'delivery_zone_id' => 'required|exists:delivery_zones,id'
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Client créé avec succès.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['deliveryZone', 'orders.items']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $deliveryZones = DeliveryZone::where('is_active', true)->get();
        return view('customers.edit', compact('customer', 'deliveryZones'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'delivery_zone_id' => 'required|exists:delivery_zones,id'
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->orders()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer un client qui a des commandes.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}
