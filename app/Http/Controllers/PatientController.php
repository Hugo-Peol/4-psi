<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::forUser(auth()->id())
            ->orderBy('name')
            ->paginate(15);

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:255',
            'session_value'    => 'nullable|numeric|min:0',
            'package_sessions' => 'nullable|integer|min:1',
            'notes'            => 'nullable|string',
            'cpf'              => 'nullable|string|max:14',
            'birth_date'       => 'nullable|date',
        ]);

        $data['user_id'] = auth()->id();

        Patient::create($data);

        return redirect()->route('patients.index')
            ->with('success', "Paciente {$data['name']} cadastrado com sucesso!");
    }

    public function show(Patient $patient)
    {
        $this->authorizePatient($patient);

        $billings = $patient->billingCycles()->latest()->paginate(10);
        $receipts = $patient->receipts()->latest()->take(5)->get();

        return view('patients.show', compact('patient', 'billings', 'receipts'));
    }

    public function edit(Patient $patient)
    {
        $this->authorizePatient($patient);
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $this->authorizePatient($patient);

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:255',
            'session_value'    => 'nullable|numeric|min:0',
            'package_sessions' => 'nullable|integer|min:1',
            'notes'            => 'nullable|string',
            'cpf'              => 'nullable|string|max:14',
            'birth_date'       => 'nullable|date',
            'active'           => 'boolean',
        ]);

        $patient->update($data);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Paciente atualizado com sucesso!');
    }

    public function destroy(Patient $patient)
    {
        $this->authorizePatient($patient);
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Paciente removido.');
    }

    private function authorizePatient(Patient $patient): void
    {
        abort_if($patient->user_id !== auth()->id(), 403, 'Acesso negado.');
    }
}
