<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Services\StockService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        
        $stats = $this->stockService->getDashboardStats($warehouseId);
        $recentTransactions = $this->stockService->getRecentTransactions(5, $warehouseId);
        $lowStockItems = $this->stockService->getLowStockItems(5, $warehouseId);
        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        $chartData = $this->getChartData($warehouseId);

        return view('dashboard.index', compact(
            'stats',
            'recentTransactions',
            'lowStockItems',
            'warehouses',
            'chartData'
        ));
    }

    private function getChartData(?int $warehouseId = null): array
    {
        $days = [];
        $stockIn = [];
        $stockOut = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('d M');

            $inQuery = StockTransaction::whereDate('transaction_date', $date)->stockIn();
            $outQuery = StockTransaction::whereDate('transaction_date', $date)->stockOut();

            if ($warehouseId) {
                $inQuery->whereHas('stockHeader', fn($q) => $q->where('warehouse_id', $warehouseId));
                $outQuery->whereHas('stockHeader', fn($q) => $q->where('warehouse_id', $warehouseId));
            }

            $stockIn[] = $inQuery->sum('quantity');
            $stockOut[] = $outQuery->sum('quantity');
        }

        return [
            'labels' => $days,
            'stockIn' => $stockIn,
            'stockOut' => $stockOut,
        ];
    }
}
