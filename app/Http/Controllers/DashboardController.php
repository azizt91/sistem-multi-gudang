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

    public function index()
    {
        $stats = $this->stockService->getDashboardStats();
        $lowStockItems = $this->stockService->getLowStockItems();
        $recentTransactions = $this->stockService->getRecentTransactions(10);

        // Get chart data for last 7 days
        $chartData = $this->getChartData();

        return view('dashboard.index', compact(
            'stats',
            'lowStockItems',
            'recentTransactions',
            'chartData'
        ));
    }

    private function getChartData(): array
    {
        $days = [];
        $stockIn = [];
        $stockOut = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('d M');

            $stockIn[] = StockTransaction::whereDate('transaction_date', $date)
                ->stockIn()
                ->sum('quantity');

            $stockOut[] = StockTransaction::whereDate('transaction_date', $date)
                ->stockOut()
                ->sum('quantity');
        }

        return [
            'labels' => $days,
            'stockIn' => $stockIn,
            'stockOut' => $stockOut,
        ];
    }
}
