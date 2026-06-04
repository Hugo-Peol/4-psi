<?php

namespace App\Http\Controllers;

use App\Models\BillingCycle;
use App\Models\Patient;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $query = BillingCycle::forUser(auth()->id())->with('patient');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $billings  = $query->orderBy('due_date', 'desc')->paginate(15);
        $patients  = Patient::forUser(auth()->id())->active()->orderBy('name')->get();

        return view('billing.index', compact('billings', 'patients'));
    }

    public function create()
    {
        $patients = Patient::forUser(auth()->id())->active()->orderBy('name')->get();
        return view('billing.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'    => 'required|exists:patients,id',
            'amount'        => 'required|numeric|min:0.01',
            'due_date'      => 'required|date',
            'description'   => 'nullable|string|max:255',
            'session_count' => 'nullable|integer|min:1',
        ]);

        // Segurança: garantir que o paciente pertence ao usuário
        $patient = Patient::forUser(auth()->id())->findOrFail($data['patient_id']);

        $data['user_id'] = auth()->id();
        BillingCycle::create($data);

        return redirect()->route('billing.index')
            ->with('success', "Cobrança criada para {$patient->name}!");
    }

    public function show(BillingCycle $billing)
    {
        $this->authorizeBilling($billing);
        return view('billing.show', compact('billing'));
    }

    public function markPaid(BillingCycle $billing)
    {
        $this->authorizeBilling($billing);
        $billing->markAsPaid();

        return back()->with('success', 'Cobrança marcada como paga! ✅');
    }

    public function destroy(BillingCycle $billing)
    {
        $this->authorizeBilling($billing);
        $billing->update(['status' => 'cancelled']);

        return back()->with('success', 'Cobrança cancelada.');
    }

    public function whatsapp(BillingCycle $billing)
    {
        $this->authorizeBilling($billing);

        $message = $billing->whatsappChargeMessage();
        $link    = $billing->patient->whatsappLink($message);

        return redirect($link);
    }

    private function authorizeBilling(BillingCycle $billing): void
    {
        abort_if($billing->user_id !== auth()->id(), 403, 'Acesso negado.');
    }
}
