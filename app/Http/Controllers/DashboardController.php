<?php

//app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'nouvelle')->count(),
            'delivered_orders' => Order::where('status', 'livree')->count(),
            'total_customers' => Customer::count(),
            'active_zones' => DeliveryZone::where('is_active', true)->count(),
            'recent_orders' => Order::with(['customer', 'deliveryZone'])
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}