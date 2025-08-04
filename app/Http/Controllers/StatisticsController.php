<?php

// app/Http/Controllers/StatisticsController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\DeliveryZone;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
        $this->middleware('permission:statistics.view')->only(['index']);
        $this->middleware('permission:statistics.export')->only(['export']);
    }

    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // 30 jours par défaut
        
        $stats = $this->statisticsService->getGeneralStats($period);
        $chartData = $this->statisticsService->getOrdersChartData($period);
        $topZones = $this->statisticsService->getTopDeliveryZones($period);
        
        return view('statistics.index', compact('stats', 'chartData', 'topZones', 'period'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|in:nouvelle,en_cours_livraison,livree,annulee,payee'
        ]);

        return Excel::download(
            new OrdersExport($request->start_date, $request->end_date, $request->status),
            'commandes_' . $request->start_date . '_' . $request->end_date . '.xlsx'
        );
    }
}