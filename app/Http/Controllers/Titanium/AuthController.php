<?php

namespace App\Http\Controllers\Titanium;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('titanium.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('titanium')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('titanium.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('titanium')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('titanium.login');
    }
}
