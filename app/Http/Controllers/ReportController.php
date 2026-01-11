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

        $transactions = StockTransaction::with(['item', 'user'])
            ->whereDate('transaction_date', $date)
            ->orderBy('transaction_date')
            ->get();

        $summary = [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
            'transaction_count' => $transactions->count(),
        ];

        return view('reports.daily', compact('transactions', 'summary', 'date'));
    }

    public function monthly(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $transactions = StockTransaction::with(['item', 'user'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date')
            ->get();

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

        return view('reports.monthly', compact('transactions', 'dailyData', 'summary', 'month', 'year', 'startDate', 'endDate'));
    }

    public function stock(Request $request)
    {
        $query = Item::with(['category', 'unit']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('low_stock')) {
            $query->lowStock();
        }

        $items = $query->orderBy('name')->get();

        $summary = [
            'total_items' => $items->count(),
            'total_stock' => $items->sum('stock'),
            'low_stock_count' => $items->filter->isLowStock()->count(),
        ];

        return view('reports.stock', compact('items', 'summary'));
    }

    /**
     * Export daily report to PDF
     */
    public function exportDailyPdf(Request $request)
    {
        $date = Carbon::parse($request->input('date', today()->toDateString()));

        $transactions = StockTransaction::with(['item', 'user'])
            ->whereDate('transaction_date', $date)
            ->orderBy('transaction_date')
            ->get();

        $summary = [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
            'transaction_count' => $transactions->count(),
        ];

        $pdf = Pdf::loadView('reports.pdf.daily', compact('transactions', 'summary', 'date'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("laporan-harian-{$date->format('Y-m-d')}.pdf");
    }

    /**
     * Export monthly report to PDF
     */
    public function exportMonthlyPdf(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $transactions = StockTransaction::with(['item', 'user'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date')
            ->get();

        $summary = [
            'total_in' => $transactions->where('type', 'in')->sum('quantity'),
            'total_out' => $transactions->where('type', 'out')->sum('quantity'),
            'transaction_count' => $transactions->count(),
        ];

        $pdf = Pdf::loadView('reports.pdf.monthly', compact('transactions', 'summary', 'month', 'year', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("laporan-bulanan-{$year}-{$month}.pdf");
    }

    /**
     * Export stock report to Excel
     */
    public function exportStockExcel(Request $request)
    {
        return Excel::download(new StockReportExport($request->category_id, $request->boolean('low_stock')), 'laporan-stok-' . now()->format('Y-m-d') . '.xlsx');
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
                $request->type
            ),
            'transaksi-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
