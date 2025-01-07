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
        $firebaseToken = session('firebase_token');
        Log::info('Firebase token from session: ' . $firebaseToken); // Log token status

        if (!$firebaseToken) {
            // Log that token is missing
            Log::warning('Firebase token missing from session.');

            // Periksa apakah permintaan adalah AJAX atau JSON
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized: Token not found'], 401);
            }

            // Arahkan ke halaman login untuk permintaan biasa
            return redirect()->route('login')->with('error', 'Session has expired. Please login again.');
        }

        try {
            // Verifikasi token menggunakan Firebase Auth
            $verifiedIdToken = $this->auth->verifyIdToken($firebaseToken);
            Log::info('Verified Firebase token: ', (array) $verifiedIdToken);

            // Tambahkan informasi pengguna Firebase ke dalam request
            $request->attributes->add(['firebase_user' => $verifiedIdToken]);
        } catch (FailedToVerifyToken $e) {
            // Log kesalahan verifikasi token
            Log::error('Firebase token verification failed: ' . $e->getMessage());

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized: Token is invalid or expired'], 401);
            }

            return redirect()->route('login')->with('error', 'Invalid or expired session. Please login again.');
        }

        return $next($request);
    }
}
