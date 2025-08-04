<?php

// app/Services/StatisticsService.php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\DeliveryZone;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function getGeneralStats(int $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return [
            'total_orders' => Order::where('created_at', '>=', $startDate)->count(),
            'total_revenue' => Order::where('created_at', '>=', $startDate)
                ->where('status', '!=', 'annulee')
                ->sum('total'),
            'pending_orders' => Order::where('status', 'nouvelle')->count(),
            'delivered_orders' => Order::where('created_at', '>=', $startDate)
                ->where('status', 'livree')
                ->count(),
            'new_customers' => Customer::where('created_at', '>=', $startDate)->count(),
            'average_order_value' => Order::where('created_at', '>=', $startDate)
                ->where('status', '!=', 'annulee')
                ->avg('total'),
        ];
    }

    public function getOrdersChartData(int $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('d/m'),
                    'orders' => $item->total_orders,
                    'revenue' => $item->total_revenue,
                ];
            });
    }

    public function getTopDeliveryZones(int $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return DeliveryZone::select('delivery_zones.*')
            ->withCount(['orders as orders_count' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->withSum(['orders as total_revenue' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)
                      ->where('status', '!=', 'annulee');
            }], 'total')
            ->orderBy('orders_count', 'desc')
            ->limit(10)
            ->get();
    }

    public function getOrdersByStatus(int $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return Order::select('status', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });
    }
}