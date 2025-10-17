<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class AuthController extends Controller
{
    // Show login page
    public function showLogin(Request $request)
    {
        $redirect = $request->get('redirect');
        return view('auth.login', compact('redirect')); // resources/views/auth/login.blade.php
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Check if there's a redirect URL (from QR code scan)
            $redirect = $request->get('redirect');
            if ($redirect) {
                return redirect($redirect);
            }
            
            // Role-based redirect
            $user = Auth::user();
            if ($user->role === 'superadmin') {
                return redirect()->route('superadmin.dashboard');
            } elseif ($user->role === 'admin') {
                return redirect()->route('admin.dash');
            } else {
                return redirect()->route('home');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    // Show signup page
    public function showSignup(Request $request)
    {
        $redirect = $request->get('redirect');
        // Reuse the same page; tabs will switch to signup
        return view('auth.login', ['showTab' => 'signup', 'redirect' => $redirect]);
    }

    // Handle signup
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            // ULID-based ID avoids collisions and scales well
            'lakbay_id' => 'LAK-' . Str::ulid()->toBase32(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'tourist',
        ]);

        Auth::login($user);

        // Check if there's a redirect URL (from QR code scan)
        $redirect = $request->get('redirect');
        if ($redirect) {
            return redirect($redirect)->with('success', 'Account created successfully!');
        }

        return redirect()->route('home')->with('success', 'Account created successfully!');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // Show forgot password form
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    // Send password reset link
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'We can\'t find a user with that email address.'
        ]);

        $token = Str::random(64);
        
        // Store the token in the database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Send email with reset link
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
        
        try {
            Mail::send('emails.password-reset', [
                'resetUrl' => $resetUrl,
                'user' => User::where('email', $request->email)->first()
            ], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Reset Your Password - Lakbay Calamba');
            });

            return back()->with('status', 'Password reset link sent to your email address.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Unable to send reset link. Please try again later.']);
        }
    }

    // Show reset password form
    public function showResetPassword(Request $request, $token)
    {
        $email = $request->get('email');
        
        // Verify token exists and is valid
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$passwordReset || !Hash::check($token, $passwordReset->token)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        // Check if token is not older than 1 hour
        if (now()->diffInMinutes($passwordReset->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Reset token has expired. Please request a new one.']);
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        // Check if token is not older than 1 hour
        if (now()->diffInMinutes($passwordReset->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset token has expired. Please request a new one.']);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete the reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully. You can now log in with your new password.');
    }
}
