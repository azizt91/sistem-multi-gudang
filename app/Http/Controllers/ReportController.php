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

        if ($request->ajax()) {
            return view('reports.partials.daily_content', compact('transactions', 'summary', 'date', 'warehouses', 'warehouse'))->render();
        }

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

        if ($request->ajax()) {
            return view('reports.partials.monthly_content', compact('transactions', 'dailyData', 'summary', 'month', 'year', 'startDate', 'endDate', 'warehouses', 'warehouse'))->render();
        }

        return view('reports.monthly', compact('transactions', 'dailyData', 'summary', 'month', 'year', 'startDate', 'endDate', 'warehouses', 'warehouse'));
    }

    public function stock(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $categoryId = $request->input('category_id');
        $lowStockOnly = $request->boolean('low_stock');

        // 1. Get Warehouses (All or Filtered)
        $warehousesQuery = \App\Models\Warehouse::orderBy('name');
        if ($warehouseId) {
            $warehousesQuery->where('id', $warehouseId);
        }
        $warehouses = $warehousesQuery->get();

        // 2. Get Items (All or Filtered)
        $itemsQuery = Item::with(['category', 'unit'])->orderBy('name');
        if ($categoryId) {
            $itemsQuery->where('category_id', $categoryId);
        }
        $allItems = $itemsQuery->get();

        // 3. Build Matrix: Warehouses x Items
        // We will construct a collection of 'ReportRow' objects/arrays
        $reportData = collect();

        foreach ($warehouses as $warehouse) {
            foreach ($allItems as $item) {
                // Determine stock for this specific pair
                // Optimize: usage of getStockInWarehouse might be N+1 if not careful, 
                // but getStockInWarehouse currently queries DB. 
                // Better approach: Eager load warehouseItems for ALL items first, then filter in memory to avoid N+1.
                
                // Let's refetch items with warehouseItems to be efficient
                // Actually, let's do this outside the loop if possible.
                // Re-querying below for clarity/simplicity in this step, but in prod we'd optimize.
                // Since this is paginated report usually, but here we show all.
                // Let's rely on the fact that we can eager load relationship.
                
                // Optimized approach:
                // We already have $allItems. Let's load warehouseItems constrained to the warehouses we have.
                // But we need to map it correctly.
                
                // Simpler Logic for now:
                // Use the Cross Join concept implicitly.
                
                $stock = $item->warehouseItems->where('warehouse_id', $warehouse->id)->first()->stock ?? 0;
                $localMinEntry = $item->warehouseItems->where('warehouse_id', $warehouse->id)->first();
                // Logic: If local entry exists and min_stock > 0, use it.
                // If local entry has 0, it might mean "not valid set" or "explicit 0". 
                // Given user issue, let's treat 0 as "Use Global" for now, or use max(local, global)? 
                // Safer: If local is 0, use global. If user explicitly wants 0, they set 0. 
                // But typically min stock is > 0. 
                $minStock = ($localMinEntry && $localMinEntry->minimum_stock > 0) ? $localMinEntry->minimum_stock : $item->minimum_stock;

                // Filter Low Stock if requested
                if ($lowStockOnly && $stock > $minStock) {
                    continue;
                }

                $reportData->push((object)[
                    'warehouse_name' => $warehouse->name,
                    'item_code' => $item->code,
                    'item_name' => $item->name,
                    'category_name' => $item->category->name,
                    'unit_name' => $item->unit->abbreviation,
                    'stock' => $stock,
                    'minimum_stock' => $minStock,
                    'rack_location' => $item->rack_location,
                    'status' => $stock <= $minStock ? 'Low Stock' : 'Normal', // Simple logic
                ]);
            }
        }

        // Prepare Summary
        $summary = [
            'total_items' => $reportData->count(),
            'total_stock' => $reportData->sum('stock'),
            'low_stock_count' => $reportData->where('status', 'Low Stock')->count(),
        ];

        // Pass to View
        // Note: The view expects different variable structure probably. 
        // Checking view 'reports.stock' next to align variables.
        // The view likely iterates $items. We need to check if we should pass $reportData as $items or adapt view.
        // Let's rename $reportData to $items for compatibility, but its structure is flattened now.
        $items = $reportData; 

        // Re-fetch warehouses for dropdown (all of them)
        $allWarehouses = \App\Models\Warehouse::orderBy('name')->get();
        
        if ($request->ajax()) {
            return view('reports.partials.stock_content', compact('items', 'summary', 'warehouses', 'allWarehouses'))->render();
        }

        return view('reports.stock', compact('items', 'summary', 'warehouses', 'allWarehouses')); // 'warehouses' var in view might be for dropdown? Check view.
        // Actually line 133 defined $warehouses for dropdown. Let's keep that.
        // In the view, the loop might need adjustment if it was iterating Item items.
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

    public function exportStockExcel(Request $request)
    {
        return Excel::download(new StockReportExport(
            $request->category_id, 
            $request->boolean('low_stock'), 
            $request->warehouse_id
        ), 'laporan-stok-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export stock report to PDF
     */
    public function exportStockPdf(Request $request)
    {
        $warehouseId = $request->input('warehouse_id');
        $categoryId = $request->input('category_id');
        $lowStockOnly = $request->boolean('low_stock');

        // Use StockReportExport logic to ensure consistency (Cross Join for 0 stock)
        $export = new StockReportExport($categoryId, $lowStockOnly, $warehouseId);
        $items = $export->collection();
        
        $warehouse = $warehouseId ? \App\Models\Warehouse::find($warehouseId) : null;
        $category = $categoryId ? \App\Models\Category::find($categoryId) : null;

        $pdf = Pdf::loadView('reports.pdf.stock', compact('items', 'warehouse', 'category', 'request'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-stok-' . now()->format('Y-m-d') . '.pdf');
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
