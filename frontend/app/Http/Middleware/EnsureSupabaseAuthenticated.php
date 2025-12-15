<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSupabaseAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        $user = session('supabase_user');
        $profile = session('profile');
        $userData = session('user_data');
        
        if (!$user || !$profile) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Attach ke request untuk akses di controller/view
        $request->attributes->set('supabase_user', $user);
        $request->attributes->set('profile', $profile);
        $request->attributes->set('user_data', $userData);
        
        return $next($request);
    }
}