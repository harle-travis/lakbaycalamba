<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')->get();
        return view('superadmin.manage_admins', compact('admins'));
    }

    public function show(User $admin)
    {
        // Ensure only admin users can be viewed
        if ($admin->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'User is not an admin'
            ], 404);
        }

        return response()->json($admin);
    }

    public function update(Request $request, User $admin)
    {
        // Ensure only admin users can be updated
        if ($admin->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'User is not an admin'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $admin->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Admin updated successfully!',
            'admin' => $admin
        ]);
    }

    public function destroy(User $admin)
    {
        // Ensure only admin users can be deleted
        if ($admin->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'User is not an admin'
            ], 404);
        }

        // Prevent deleting the current user
        if ($admin->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 400);
        }

        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin deleted successfully!'
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Get the superadmin user account (shared account for all collaborators)
        $superadminUser = User::where('email', 'superadmin@lakbay.com')->first();

        if (!$superadminUser) {
            return response()->json([
                'success' => false,
                'message' => 'Superadmin account not found'
            ], 404);
        }

        // Check if current password is correct for the superadmin account
        if (!Hash::check($request->current_password, $superadminUser->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Update the superadmin account password
        $superadminUser->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Superadmin password changed successfully! All collaborators will need to use the new password.'
        ]);
    }
}
