<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('profile_public', true)
            ->whereNotNull('username')
            ->whereNotNull('crp');

        if ($request->filled('approach')) {
            $query->where('approach', 'like', '%' . $request->approach . '%');
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('bio', 'like', '%' . $request->q . '%')
                  ->orWhere('specialty', 'like', '%' . $request->q . '%');
            });
        }

        $psychologists = $query->orderBy('name')->paginate(12);

        $approaches = User::where('profile_public', true)
            ->whereNotNull('approach')
            ->distinct()
            ->pluck('approach');

        return view('home', compact('psychologists', 'approaches'));
    }
}
