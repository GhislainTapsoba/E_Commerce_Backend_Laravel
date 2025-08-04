<?php
// app/Http/Controllers/DeliveryZoneController.php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:delivery-zones.view')->only(['index', 'show']);
        $this->middleware('permission:delivery-zones.create')->only(['create', 'store']);
        $this->middleware('permission:delivery-zones.edit')->only(['edit', 'update']);
        $this->middleware('permission:delivery-zones.delete')->only(['destroy']);
    }

    public function index()
    {
        $zones = DeliveryZone::withCount(['customers', 'orders'])->paginate(15);
        return view('delivery-zones.index', compact('zones'));
    }

    public function create()
    {
        return view('delivery-zones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'delivery_fee' => 'required|numeric|min:0',
            'delivery_time_min' => 'nullable|integer|min:0',
            'delivery_time_max' => 'nullable|integer|min:0|gte:delivery_time_min',
            'is_active' => 'boolean'
        ]);

        DeliveryZone::create($request->all());

        return redirect()->route('delivery-zones.index')
            ->with('success', 'Zone de livraison créée avec succès.');
    }

    public function show(DeliveryZone $deliveryZone)
    {
        $deliveryZone->loadCount(['customers', 'orders']);
        return view('delivery-zones.show', compact('deliveryZone'));
    }

    public function edit(DeliveryZone $deliveryZone)
    {
        return view('delivery-zones.edit', compact('deliveryZone'));
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'delivery_fee' => 'required|numeric|min:0',
            'delivery_time_min' => 'nullable|integer|min:0',
            'delivery_time_max' => 'nullable|integer|min:0|gte:delivery_time_min',
            'is_active' => 'boolean'
        ]);

        $deliveryZone->update($request->all());

        return redirect()->route('delivery-zones.index')
            ->with('success', 'Zone de livraison mise à jour avec succès.');
    }

    public function destroy(DeliveryZone $deliveryZone)
    {
        if ($deliveryZone->orders()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une zone qui contient des commandes.');
        }

        $deliveryZone->delete();

        return redirect()->route('delivery-zones.index')
            ->with('success', 'Zone de livraison supprimée avec succès.');
    }
}