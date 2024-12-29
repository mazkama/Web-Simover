<?php
namespace App\Http\Middleware;

use Closure;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Illuminate\Http\Request;
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
        // Check if the Firebase token is stored in the session
        $firebaseToken = session('firebase_token');

        if (!$firebaseToken) {
            // No token found in session, return unauthorized
            return redirect()->route('login');
        }

        try {
            // Verify the token using Firebase Auth
            $verifiedIdToken = $this->auth->verifyIdToken($firebaseToken);

            // Attach the Firebase user to the request if token is valid
            $request->attributes->add(['firebase_user' => $verifiedIdToken]);

        } catch (FailedToVerifyToken $e) {
            // Token invalid or expired
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }

        return $next($request); // Continue the requesta
    }
}
