<?php

namespace App\Http\Controllers;

use App\Models\BillingCycle;
use App\Models\Patient;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index()
    {
        $receipts = Receipt::forUser(auth()->id())
            ->with('patient')
            ->latest()
            ->paginate(15);

        return view('receipts.index', compact('receipts'));
    }

    public function create(Request $request)
    {
        $patients = Patient::forUser(auth()->id())->active()->orderBy('name')->get();
        $billings = collect();

        if ($request->filled('billing_id')) {
            $billing = BillingCycle::forUser(auth()->id())->find($request->billing_id);
        }

        return view('receipts.create', compact('patients', 'billing'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'billing_cycle_id' => 'nullable|exists:billing_cycles,id',
            'cid_code'         => 'nullable|string|max:20',
            'session_date'     => 'required|date',
            'amount'           => 'required|numeric|min:0.01',
            'session_count'    => 'nullable|integer|min:1',
            'health_plan'      => 'nullable|string|max:100',
            'observations'     => 'nullable|string',
        ]);

        // Segurança: paciente pertence ao usuário
        Patient::forUser(auth()->id())->findOrFail($data['patient_id']);

        $data['user_id'] = auth()->id();
        $receipt = Receipt::create($data);

        return redirect()->route('receipts.show', $receipt)
            ->with('success', "Recibo {$receipt->receipt_number} criado!");
    }

    public function show(Receipt $receipt)
    {
        $this->authorizeReceipt($receipt);
        $receipt->load('patient', 'user');
        return view('receipts.show', compact('receipt'));
    }

    public function download(Receipt $receipt)
    {
        $this->authorizeReceipt($receipt);
        $receipt->load('patient', 'user');

        $pdf = Pdf::loadView('receipts.pdf', compact('receipt'))
            ->setPaper('a4', 'portrait');

        $filename = "recibo-{$receipt->receipt_number}.pdf";
        return $pdf->download($filename);
    }

    public function destroy(Receipt $receipt)
    {
        $this->authorizeReceipt($receipt);
        $receipt->delete();

        return redirect()->route('receipts.index')
            ->with('success', 'Recibo excluído.');
    }

    private function authorizeReceipt(Receipt $receipt): void
    {
        abort_if($receipt->user_id !== auth()->id(), 403, 'Acesso negado.');
    }
}
