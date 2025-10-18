<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Establishment;
use App\Models\EmailConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Log the request data for debugging
            \Log::info('Creating user with data:', [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'has_password' => !empty($request->password),
                'has_password_confirmation' => !empty($request->password_confirmation)
            ]);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|string|in:admin,user,superadmin',
            ]);

            // Generate random lakbay_id
            $lakbayId = 'LAK' . strtoupper(Str::random(8));

            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'lakbay_id' => $lakbayId,
            ]);

            \Log::info('User created successfully:', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin account created successfully!',
                'user' => $user
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error creating user:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            // Flatten validation messages without deprecated helpers
            $flatMessages = [];
            foreach ($e->errors() as $fieldErrors) {
                foreach ($fieldErrors as $message) {
                    $flatMessages[] = $message;
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $flatMessages),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating user:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the user account: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showSettings()
    {
        $user = auth()->user();
        return view('settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
        ]);

        // Update name immediately
        $user->update([
            'name' => $request->name,
        ]);

        // If email is changing, require email confirmation
        if ($request->email !== $user->email) {
            // Create email confirmation
            $confirmation = EmailConfirmation::createConfirmation(
                $user->id,
                EmailConfirmation::TYPE_EMAIL_CHANGE,
                $request->email
            );

            // Send confirmation email
            $confirmationUrl = route('email.confirm', ['token' => $confirmation->token]);
            
            try {
                Mail::send('emails.email-change-confirmation', [
                    'user' => $user,
                    'newEmail' => $request->email,
                    'confirmationUrl' => $confirmationUrl
                ], function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                            ->subject('Confirm Email Change - Lakbay Calamba');
                });

                return redirect()->route('settings')->with('success', 
                    'Profile name updated! Please check your email to confirm the email address change.');
            } catch (\Exception $e) {
                return redirect()->route('settings')->with('error', 
                    'Profile name updated, but failed to send email confirmation. Please try again.');
            }
        }

        return redirect()->route('settings')->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('settings')->with('error', 'Current password is incorrect.');
        }

        // Create password change confirmation
        $confirmation = EmailConfirmation::createConfirmation(
            $user->id,
            EmailConfirmation::TYPE_PASSWORD_CHANGE,
            null,
            Hash::make($request->password)
        );

        // Send confirmation email
        $confirmationUrl = route('password.confirm', ['token' => $confirmation->token]);
        
        try {
            Mail::send('emails.password-change-confirmation', [
                'user' => $user,
                'confirmationUrl' => $confirmationUrl
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Confirm Password Change - Lakbay Calamba');
            });

            return redirect()->route('settings')->with('success', 
                'Please check your email to confirm the password change. Your current password remains active until confirmed.');
        } catch (\Exception $e) {
            return redirect()->route('settings')->with('error', 
                'Failed to send password confirmation email. Please try again.');
        }
    }

    /**
     * Handle email confirmation for email changes
     */
    public function confirmEmailChange(Request $request, $token)
    {
        $confirmation = EmailConfirmation::where('token', $token)
            ->where('type', EmailConfirmation::TYPE_EMAIL_CHANGE)
            ->first();

        if (!$confirmation || !$confirmation->isValid()) {
            return redirect()->route('settings')->with('error', 
                'Invalid or expired confirmation link. Please request a new email change.');
        }

        // Update user email
        $user = $confirmation->user;
        $user->update(['email' => $confirmation->new_email]);
        
        // Mark confirmation as confirmed
        $confirmation->confirm();

        return redirect()->route('settings')->with('success', 
            'Email address has been successfully updated!');
    }

    /**
     * Handle email confirmation for password changes
     */
    public function confirmPasswordChange(Request $request, $token)
    {
        $confirmation = EmailConfirmation::where('token', $token)
            ->where('type', EmailConfirmation::TYPE_PASSWORD_CHANGE)
            ->first();

        if (!$confirmation || !$confirmation->isValid()) {
            return redirect()->route('settings')->with('error', 
                'Invalid or expired confirmation link. Please request a new password change.');
        }

        // Update user password
        $user = $confirmation->user;
        $user->update(['password' => $confirmation->new_password_hash]);
        
        // Mark confirmation as confirmed
        $confirmation->confirm();

        return redirect()->route('settings')->with('success', 
            'Password has been successfully updated!');
    }
}
