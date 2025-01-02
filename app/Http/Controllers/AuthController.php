<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\EmailExists;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Kreait\Firebase\Exception\Auth\RevokedIdToken;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AuthController extends Controller
{
    protected $firebaseAuth;

    public function __construct()
    {
        // Inisialisasi Firebase Auth
        $this->firebaseAuth = (new Factory)
            ->withServiceAccount(base_path('service-account-simover.json'))
            ->createAuth();
    }

    /**
     * Register a new user with additional fields.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|min:10|max:15',
            'password' => 'required|min:6',
        ]);

        try {
            // Proses pendaftaran user baru di Firebase
            $userProperties = [
                'displayName' => $request->name,
                'email' => $request->email,
                'phoneNumber' => $request->phone,
                'password' => $request->password,
            ];

            $user = $this->firebaseAuth->createUser($userProperties);

            // Kirim email verifikasi
            $this->firebaseAuth->sendEmailVerificationLink($user->email);

            // return response()->json([
            //     'message' => 'User registered successfully. Please verify your email.',
            //     'user' => $user,
            // ], 201);

            return redirect('/login')->with('status', 'You have been registered out successfully.');
        } catch (EmailExists $e) {
            return response()->json(['error' => 'Email already exists.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Login user with email verification check.
     */
    public function login(Request $request)
    {
        $auth = Firebase::auth();

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        try {
            $user = $auth->getUserByEmail($request->email);

            if (!$user->emailVerified) {
                return redirect('/login')->withErrors(['error' => 'Email not verified. Please verify your email first.']);
            }

            $login = $auth->signInWithEmailAndPassword(
                $request->email,
                $request->password
            );

            session([
                'user_name' => $user->displayName,
                'user_email' => $user->email,
                'firebase_token' => $login->idToken()
            ]);

            return redirect('/dashboard')->with('status', 'Login successful.');
        } catch (UserNotFound $e) {
            return redirect('/login')->withErrors(['error' => 'User not found.']);
        } catch (InvalidPassword $e) {
            return redirect('/login')->withErrors(['error' => 'Invalid credentials.']);
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'An error occurred.']);
        }
    }

    /**
     * Logout user and invalidate session.
     */


    public function logout(Request $request)
    {
        try {
            // Clear session data related to Firebase (e.g., the auth token)
            Session::forget('firebase_token');  // or whatever session key you're using

            // Optionally, invalidate Laravel's session to destroy the session data
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // return response()->json([
            //     'status' => 'You have been logged out successfully'
            // ], 200);

            // Redirect user to login page or another page
            return redirect('/login')->with('status', 'You have been logged out successfully.');
        } catch (Exception $e) {
            // Handle errors during logout
            return response()->json(['Error signing out' => $e->getMessage()], 400);
        }
    }

    // public function logout(Request $request)
    // {
    //     // Mengambil token dari request
    //     $token = $request->bearerToken();
    //     if (!$token) {
    //         return response()->json(['error' => 'Token not provided'], 400);
    //     }

    //     try {
    //         // Revokasi refresh token
    //         $this->firebaseAuth->revokeRefreshTokens($token);

    //         // Hapus session pengguna
    //         session()->forget(['user_name', 'user_email', 'firebase_token']);
    //         session()->flush();

    //         return response()->json(['message' => 'User logged out successfully!'], 200);
    //     } catch (RevokedIdToken $e) {
    //         return response()->json(['error' => 'Invalid token'], 400);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    // }

    /**
     * Resend email verification link to the user.
     */
    public function resendEmailVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            // Mendapatkan user berdasarkan email
            $user = $this->firebaseAuth->getUserByEmail($request->email);

            // Kirim ulang email verifikasi
            $this->firebaseAuth->sendEmailVerificationLink($user->email);

            return response()->json([
                'message' => 'Verification email sent successfully.',
            ], 200);
        } catch (UserNotFound $e) {
            return response()->json(['error' => 'User not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
