<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\DeliveryZone;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 30); // Par défaut 30 jours
        $startDate = Carbon::now()->subDays($period);
        
        // Statistiques générales
        $stats = [
            'orders_count' => Order::where('created_at', '>=', $startDate)->count(),
            'orders_total' => Order::where('created_at', '>=', $startDate)
                                ->where('status', '!=', 'annulee')
                                ->sum('total'),
            'customers_count' => User::whereHas('customerOrders', function($query) use ($startDate) {
                                    $query->where('created_at', '>=', $startDate);
                                })->count(),
            'zones_count' => DeliveryZone::whereHas('orders', function($query) use ($startDate) {
                                $query->where('created_at', '>=', $startDate);
                            })->count()
        ];

        // Données pour le graphique
        $chartData = $this->getOrdersChartData($period);

        // Top zones de livraison
        $topZones = DeliveryZone::withCount(['orders' => function($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }])
        ->with(['orders' => function($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate)
                  ->where('status', '!=', 'annulee');
        }])
        ->whereHas('orders', function($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
        })
        ->orderBy('orders_count', 'desc')
        ->limit(10)
        ->get()
        ->map(function($zone) {
            $zone->orders_total = $zone->orders->sum('total');
            return $zone;
        });

        return view('statistics.index', compact('stats', 'chartData', 'topZones', 'period'));
    }

    private function getOrdersChartData($period)
    {
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        $interval = $period <= 7 ? 'day' : ($period <= 30 ? 'day' : ($period <= 90 ? 'week' : 'month'));
        $format = $period <= 7 ? 'Y-m-d' : ($period <= 30 ? 'Y-m-d' : ($period <= 90 ? 'Y-W' : 'Y-m'));
        $labelFormat = $period <= 7 ? 'd/m' : ($period <= 30 ? 'd/m' : ($period <= 90 ? 'S\emaine W' : 'M/Y'));

        $pgFormats = [
            'Y-m-d' => 'YYYY-MM-DD',
            'Y-W'   => 'IYYY-IW',
            'Y-m'   => 'YYYY-MM'
        ];
        $pgFormat = $pgFormats[$format] ?? $format;

        $orders = Order::select(
            DB::raw("TO_CHAR(created_at, '$pgFormat') as date"),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', $startDate)
        ->where('created_at', '<=', $endDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

        $labels = [];
        $data = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dateKey = $current->format($format);
            $labels[] = $current->format($labelFormat);
            $data[] = $orders->get($dateKey)->count ?? 0;

            switch ($interval) {
                case 'day': $current->addDay(); break;
                case 'week': $current->addWeek(); break;
                case 'month': $current->addMonth(); break;
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:nouvelle,en_cours_livraison,livree,annulee,payee'
        ]);

        // On ne charge que 'items', pas 'items.product'
        $query = Order::with(['customer', 'deliveryZone', 'items'])
                     ->whereBetween('created_at', [$request->start_date, $request->end_date]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'commandes_' . $request->start_date . '_' . $request->end_date . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'ID', 'Numéro', 'Date', 'Client', 'Email', 'Zone',
                'Statut', 'Sous-total', 'Frais livraison', 'Total', 'Produits'
            ], ';');

            foreach ($orders as $order) {
                $products = $order->items->map(function($item) {
                    return $item->product_name . ' (x' . $item->quantity . ')';
                })->join(', ');

                fputcsv($file, [
                    $order->id,
                    $order->order_number,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->customer->name ?? 'N/A',
                    $order->customer->email ?? 'N/A',
                    $order->deliveryZone->name ?? 'N/A',
                    $this->getStatusLabel($order->status),
                    number_format($order->subtotal, 0, ',', ' ') . ' F',
                    number_format($order->delivery_fee, 0, ',', ' ') . ' F',
                    number_format($order->total, 0, ',', ' ') . ' F',
                    $products
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStatusLabel($status)
    {
        $statuses = [
            'nouvelle' => 'Nouvelle',
            'en_cours_livraison' => 'En cours de livraison',
            'livree' => 'Livrée',
            'annulee' => 'Annulée',
            'payee' => 'Payée'
        ];

        return $statuses[$status] ?? $status;
    }
}
