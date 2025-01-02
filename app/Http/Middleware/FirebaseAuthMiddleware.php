<?php

namespace App\Http\Middleware;

use Closure;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseAuthMiddleware
{
    protected $auth;

    public function __construct()
    {
        $this->auth = Firebase::auth();
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        // Periksa apakah token Firebase ada di session
        $firebaseToken = session('firebase_token');
        // Log::info('Current route: ' . $request->path());
        // Log::info('Firebase token: ' . $firebaseToken);

        if (!$firebaseToken) {
            // Jika token tidak ditemukan, periksa apakah permintaan adalah AJAX
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Untuk permintaan biasa, arahkan ke halaman login
            return redirect()->route('login')->with('error', 'Session has expired. Please login again.');
        }

        try {
            // Verifikasi token menggunakan Firebase Auth
            $verifiedIdToken = $this->auth->verifyIdToken($firebaseToken);

            // Tambahkan pengguna Firebase ke atribut permintaan
            $request->attributes->add(['firebase_user' => $verifiedIdToken]);
        } catch (FailedToVerifyToken $e) {
            // Token tidak valid atau kedaluwarsa
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            return redirect()->route('login')->with('error', 'Invalid or expired session. Please login again.');
        }

        // Lanjutkan permintaan
        return $next($request);
    }
}
