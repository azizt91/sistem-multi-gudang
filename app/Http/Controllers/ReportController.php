<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use App\Exports\StockReportExport;
use App\Exports\TransactionExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function daily(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $date = Carbon::parse($date);
        $warehouseId = $request->input('warehouse_id');

        $query = StockTransaction::with(['item', 'user', 'stockHeader.warehouse'])
            ->whereDate('transaction_date', $date);

        if ($warehouseId) {
            $query->whereHas('stockHeader', fn($q) => $q->where('warehouse_id', $warehouseId));
        }

        $transactions = $query->orderBy('transaction_date')->get();

        $summary = [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
            'transaction_count' => $transactions->count(),
        ];

        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        $warehouse = $warehouseId ? \App\Models\Warehouse::find($warehouseId) : null;
        return view('reports.daily', compact('transactions', 'summary', 'date', 'warehouses', 'warehouse'));
    }

    public function monthly(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $warehouseId = $request->input('warehouse_id');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $query = StockTransaction::with(['item', 'user', 'stockHeader.warehouse'])
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($warehouseId) {
            $query->whereHas('stockHeader', fn($q) => $q->where('warehouse_id', $warehouseId));
        }

        $transactions = $query->orderBy('transaction_date')->get();

        // Group by date
        $dailyData = $transactions->groupBy(function ($transaction) {
            return $transaction->transaction_date->format('Y-m-d');
        })->map(function ($dayTransactions) {
            return [
                'in' => $dayTransactions->where('type', 'in')->sum('quantity'),
                'out' => $dayTransactions->where('type', 'out')->sum('quantity'),
                'count' => $dayTransactions->count(),
            ];
        });

        $summary = [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
            'transaction_count' => $transactions->count(),
        ];

        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        $warehouse = $warehouseId ? \App\Models\Warehouse::find($warehouseId) : null;
        return view('reports.monthly', compact('transactions', 'dailyData', 'summary', 'month', 'year', 'startDate', 'endDate', 'warehouses', 'warehouse'));
    }

    public function stock(Request $request)
    {
        $query = Item::with(['category', 'unit', 'warehouseItems.warehouse']);
        $warehouseId = $request->input('warehouse_id');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('low_stock')) {
            if ($warehouseId) {
                // Low stock specifically in this warehouse
                $query->whereHas('warehouseItems', function ($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId)->whereColumn('stock', '<=', 'minimum_stock');
                });
            } else {
                // Low stock in ANY warehouse
                $query->lowStock();
            }
        }

        $items = $query->orderBy('name')->get();
        
        // If warehouse filtered, we might want to adjust displayed stock? 
        // For simple reports, displaying global stock + specific warehouse stock (if filtered) is best handled in View.

        $summary = [
            'total_items' => $items->count(),
            'total_stock' => $warehouseId 
                ? $items->sum(fn($i) => $i->getStockInWarehouse($warehouseId)) 
                : $items->sum('stock'), // Uses new accessor
            'low_stock_count' => $request->boolean('low_stock') ? $items->count() : ($warehouseId 
                ? $items->filter(fn($i) => $i->warehouseItems->where('warehouse_id', $warehouseId)->where('stock', '<=', 'minimum_stock')->isNotEmpty())->count()
                : $items->filter->isLowStock()->count()),
        ];

        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        return view('reports.stock', compact('items', 'summary', 'warehouses'));
    }

    /**
     * Export daily report to PDF
     */
    public function exportDailyPdf(Request $request)
    {
        $date = Carbon::parse($request->input('date', today()->toDateString()));
        $warehouseId = $request->input('warehouse_id');

        $query = StockTransaction::with(['item', 'user', 'stockHeader.warehouse'])
            ->whereDate('transaction_date', $date);

        if ($warehouseId) {
            $query->whereHas('stockHeader', fn($q) => $q->where('warehouse_id', $warehouseId));
        }

        $transactions = $query->orderBy('transaction_date')->get();

        $summary = [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
            'transaction_count' => $transactions->count(),
        ];

        $warehouse = $warehouseId ? \App\Models\Warehouse::find($warehouseId) : null;
        $pdf = Pdf::loadView('reports.pdf.daily', compact('transactions', 'summary', 'date', 'warehouse'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("laporan-harian-{$date->format('Y-m-d')}.pdf");
    }

    /**
     * Export monthly report to PDF
     */
    public function exportMonthlyPdf(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $warehouseId = $request->input('warehouse_id');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $query = StockTransaction::with(['item', 'user', 'stockHeader.warehouse'])
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($warehouseId) {
            $query->whereHas('stockHeader', fn($q) => $q->where('warehouse_id', $warehouseId));
        }

        $transactions = $query->orderBy('transaction_date')->get();

        $summary = [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
            'transaction_count' => $transactions->count(),
        ];

        $warehouse = $warehouseId ? \App\Models\Warehouse::find($warehouseId) : null;
        $pdf = Pdf::loadView('reports.pdf.monthly', compact('transactions', 'summary', 'month', 'year', 'startDate', 'endDate', 'warehouse'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("laporan-bulanan-{$year}-{$month}.pdf");
    }

    /**
     * Export daily report to Excel
     */
    public function exportDailyExcel(Request $request)
    {
        $date = Carbon::parse($request->input('date', today()->toDateString()));
        $warehouseId = $request->input('warehouse_id');

        return Excel::download(
            new TransactionExport($date->copy()->startOfDay(), $date->copy()->endOfDay(), null, $warehouseId),
            "laporan-harian-{$date->format('Y-m-d')}.xlsx"
        );
    }

    /**
     * Export monthly report to Excel
     */
    public function exportMonthlyExcel(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $warehouseId = $request->input('warehouse_id');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        return Excel::download(
            new TransactionExport($startDate, $endDate, null, $warehouseId),
            "laporan-bulanan-{$year}-{$month}.xlsx"
        );
    }

    /**
     * Export stock report to Excel
     */
    public function exportStockExcel(Request $request)
    {
        return Excel::download(new StockReportExport(
            $request->category_id, 
            $request->boolean('low_stock'), 
            $request->warehouse_id
        ), 'laporan-stok-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export transactions to Excel
     */
    public function exportTransactionsExcel(Request $request)
    {
        return Excel::download(
            new TransactionExport(
                $request->start_date,
                $request->end_date,
                $request->type,
                $request->warehouse_id
            ),
            'transaksi-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
