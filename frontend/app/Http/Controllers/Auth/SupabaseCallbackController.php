s<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Ksm;
use App\Models\Upkp;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SupabaseCallbackController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $request->validate(['access_token' => 'required|string']);
            $token = $request->input('access_token');

            $supabaseUser = $this->getSupabaseUser($token);
            if (!$supabaseUser) {
                return response()->json(['error' => 'Invalid Supabase token'], 401);
            }

            $role = $supabaseUser['user_metadata']['role'] ?? $supabaseUser['app_metadata']['role'] ?? null;

            $profile = Profile::where('user_id', $supabaseUser['id'])->first();
            if (!$profile) {
                return response()->json(['error' => 'Profile not found'], 404);
            }

            $userData = null;
            if ($role === 'ksm' && $profile->id_ksm) {
                $userData = Ksm::find($profile->id_ksm);
            } elseif ($role === 'upkp' && $profile->id_upkp) {
                $userData = Upkp::find($profile->id_upkp);
            }

            session([
                'supabase_user' => $supabaseUser,
                'supabase_token' => $token,
                'profile' => $profile->toArray(),
                'user_data' => $userData ? $userData->toArray() : null,
            ]);

            $redirect = '/';
            if ($role === 'ksm') $redirect = route('ksm.laporan');
            elseif ($role === 'upkp') $redirect = route('upkp.dashboard');
            elseif ($role === 'dlh') $redirect = route('dlh.dashboard');

            return response()->json(['redirect' => $redirect], 200);
        } catch (\Throwable $e) {
            Log::error('Supabase callback error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Server error: '.$e->getMessage()], 500);
        }
    }

    protected function getSupabaseUser(string $token): ?array
    {
        try {
            $client = new Client([
                'base_uri' => rtrim(env('SUPABASE_URL'), '/'),
                'timeout' => 10,
            ]);

            $response = $client->get('/auth/v1/user', [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'apikey' => env('VITE_SUPABASE_ANON'),
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            Log::error('Supabase user fetch failed: '.$e->getMessage());
            return null;
        }
    }
}