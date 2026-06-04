<?php

namespace App\Http\Controllers;

use App\Models\User;

class PublicProfileController extends Controller
{
    public function show(string $username)
    {
        $psychologist = User::where('username', $username)
            ->where('profile_public', true)
            ->firstOrFail();

        return view('profile.public', compact('psychologist'));
    }
}
