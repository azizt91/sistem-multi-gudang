<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockHeader;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockHeaderController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display list of transaction documents
     */
    public function index(Request $request)
    {
        $query = StockHeader::with(['user', 'transactions']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date . ' 23:59:59']);
        }

        $headers = $query->orderBy('transaction_date', 'desc')->paginate(20);

        return view('stock-headers.index', compact('headers'));
    }

    /**
     * Show form to create new stock in
     */
    public function createStockIn()
    {
        $items = Item::with('unit')->orderBy('name')->get();
        $type = 'in';
        return view('stock-headers.create', compact('items', 'type'));
    }

    /**
     * Show form to create new stock out
     */
    public function createStockOut()
    {
        $items = Item::with('unit')->where('stock', '>', 0)->orderBy('name')->get();
        $type = 'out';
        return view('stock-headers.create', compact('items', 'type'));
    }

    /**
     * Store new transaction with multiple items
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        try {
            $header = $this->stockService->createTransaction(
                $validated['type'],
                $validated['items'],
                $validated['notes']
            );

            $typeLabel = $validated['type'] === 'in' ? 'masuk' : 'keluar';
            return redirect()->route('stock-headers.show', $header)
                ->with('success', "Transaksi stok {$typeLabel} berhasil dicatat. Dokumen: {$header->document_number}");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show document detail
     */
    public function show(StockHeader $stockHeader)
    {
        $stockHeader->load(['transactions.item.unit', 'user']);
        return view('stock-headers.show', compact('stockHeader'));
    }

    /**
     * Show receipt form
     */
    public function receipt(StockHeader $stockHeader)
    {
        $stockHeader->load(['transactions.item.unit', 'user']);
        return view('stock-headers.receipt', compact('stockHeader'));
    }

    /**
     * Save signatures
     */
    public function saveSignatures(Request $request, StockHeader $stockHeader)
    {
        // Check if receipt is locked
        if ($stockHeader->isReceiptLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Tanda terima sudah dikunci, tidak dapat diubah.',
            ], 403);
        }

        // Staff cannot modify after initial submission if signatures exist
        if (!auth()->user()->isAdmin() && $stockHeader->hasCompleteSignatures()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengubah tanda tangan.',
            ], 403);
        }

        $data = [];

        // Save sender name
        if ($request->filled('sender_name')) {
            $data['sender_name'] = $request->input('sender_name');
        }

        // Save receiver name
        if ($request->filled('receiver_name')) {
            $data['receiver_name'] = $request->input('receiver_name');
        }

        // Ensure signatures directory exists
        $signaturesPath = storage_path('app/public/signatures');
        if (!file_exists($signaturesPath)) {
            mkdir($signaturesPath, 0755, true);
        }

        // Save sender signature
        if ($request->hasFile('sender_signature')) {
            $file = $request->file('sender_signature');
            $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png';
            $filename = 'signatures/sender_' . $stockHeader->id . '_' . time() . '.' . $extension;
            
            // Use Storage facade explicitly
            \Storage::disk('public')->put($filename, file_get_contents($file->getRealPath()));
            $data['sender_signature'] = $filename;
        }

        // Save receiver signature
        if ($request->hasFile('receiver_signature')) {
            $file = $request->file('receiver_signature');
            $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png';
            $filename = 'signatures/receiver_' . $stockHeader->id . '_' . time() . '.' . $extension;
            
            // Use Storage facade explicitly
            \Storage::disk('public')->put($filename, file_get_contents($file->getRealPath()));
            $data['receiver_signature'] = $filename;
        }

        // Lock receipt if requested
        if ($request->boolean('lock_receipt')) {
            $data['receipt_locked'] = true;
        }

        $stockHeader->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Tanda tangan berhasil disimpan.',
        ]);
    }

    /**
     * Download receipt as PDF
     */
    public function downloadPdf(StockHeader $stockHeader)
    {
        $stockHeader->load(['transactions.item.unit', 'user']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('stock-headers.pdf.receipt', compact('stockHeader'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('tanda-terima-' . $stockHeader->document_number . '.pdf');
    }

    /**
     * Delete transaction document (Admin only)
     */
    public function destroy(StockHeader $stockHeader)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Hanya admin yang dapat menghapus transaksi.');
        }

        // Check if locked
        if ($stockHeader->isReceiptLocked()) {
            return back()->with('error', 'Transaksi sudah dikunci, tidak dapat dihapus.');
        }

        // Revert stock for all items
        foreach ($stockHeader->transactions as $transaction) {
            $this->stockService->revertTransaction($transaction);
        }

        $stockHeader->delete();

        return redirect()->route('stock-headers.index')
            ->with('success', 'Transaksi berhasil dihapus dan stok telah dikembalikan.');
    }
    /**
     * Quick stock in via barcode
     */
    public function quickStockIn(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = Item::with(['unit'])->where('code', $validated['code'])->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        try {
            // Create Transaction with Header
            $itemsData = [
                [
                    'item_id' => $item->id,
                    'quantity' => $validated['quantity'],
                    'notes' => $validated['notes']
                ]
            ];
            
            $this->stockService->createTransaction(
                'in', 
                $itemsData, 
                'Quick Scan via Barcode'
            );

            return response()->json([
                'success' => true,
                'message' => "Stok masuk berhasil: +{$validated['quantity']} {$item->unit->abbreviation}",
                'item' => [
                    'name' => $item->name,
                    'stock' => $item->fresh()->stock,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Quick stock out via barcode
     */
    public function quickStockOut(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = Item::with(['unit'])->where('code', $validated['code'])->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        try {
            // Create Transaction with Header
            $itemsData = [
                [
                    'item_id' => $item->id,
                    'quantity' => $validated['quantity'],
                    'notes' => $validated['notes']
                ]
            ];
            
            $this->stockService->createTransaction(
                'out', 
                $itemsData, 
                'Quick Scan via Barcode'
            );

            return response()->json([
                'success' => true,
                'message' => "Stok keluar berhasil: -{$validated['quantity']} {$item->unit->abbreviation}",
                'item' => [
                    'name' => $item->name,
                    'stock' => $item->fresh()->stock,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
