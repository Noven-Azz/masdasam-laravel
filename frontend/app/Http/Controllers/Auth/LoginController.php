<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Redirect based on role
            $redirectPath = match (strtoupper($user->role ?? 'KSM')) {
                'KSM' => '/laporan-ksm',
                'UPKP' => '/dashboard-upkp',
                'DLH' => '/dashboard-dlh',
                'ADMIN' => '/dashboard-dlh',
                default => '/laporan-ksm',
            };

            return redirect()->intended($redirectPath)->with('success', 'Login berhasil!');
        }

        throw ValidationException::withMessages([
            'email' => ['Email atau password salah.'],
        ]);
    }
}