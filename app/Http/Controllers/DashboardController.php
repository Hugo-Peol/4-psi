<?php

namespace App\Http\Controllers;

use App\Models\BillingCycle;
use App\Models\Patient;
use App\Models\Receipt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total_patients'   => Patient::forUser($user->id)->active()->count(),
            'monthly_revenue'  => $user->monthlyRevenue(),
            'pending_amount'   => $user->pendingAmount(),
            'overdue_count'    => $user->overdueCount(),
        ];

        $recentBillings = BillingCycle::forUser($user->id)
            ->with('patient')
            ->latest()
            ->take(5)
            ->get();

        $overdueList = BillingCycle::forUser($user->id)
            ->late()
            ->with('patient')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recentPatients = Patient::forUser($user->id)
            ->active()
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'stats', 'recentBillings', 'overdueList', 'recentPatients'
        ));
    }
}
