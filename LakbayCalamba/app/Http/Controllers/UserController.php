<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

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

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('settings')->with('success', 'Password updated successfully!');
    }
}
