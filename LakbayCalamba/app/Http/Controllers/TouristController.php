<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stamp;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TouristController extends Controller
{
    /**
     * Display a listing of tourists with their stamp information
     */
    public function index()
    {
        // Get all tourists with their stamp counts and stamps
        $tourists = User::where('role', 'tourist')
            ->withCount('stamps')
            ->with(['stamps.establishment'])
            ->orderBy('stamps_count', 'desc')
            ->get();

        return view('superadmin.manage_tourists', compact('tourists'));
    }

    /**
     * Delete a specific stamp for a tourist
     */
    public function deleteStamp(Request $request, $stampId)
    {
        // Verify the user is a superadmin
        if (!Auth::check() || Auth::user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        try {
            $stamp = Stamp::findOrFail($stampId);
            $touristName = $stamp->user->name;
            $establishmentName = $stamp->establishment->establishment_name;
            $userId = $stamp->user_id;
            $establishmentId = $stamp->establishment_id;
            $visitDate = $stamp->visit_date;
            
            // Delete the stamp
            $stamp->delete();
            
            // Also delete the corresponding visitor record
            Visitor::where('user_id', $userId)
                   ->where('establishment_id', $establishmentId)
                   ->whereDate('visited_at', $visitDate->format('Y-m-d'))
                   ->delete();

            return response()->json([
                'success' => true,
                'message' => "Stamp from '{$establishmentName}' deleted for {$touristName}."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete stamp: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stamps for a specific tourist
     */
    public function getTouristStamps($touristId)
    {
        // Verify the user is a superadmin
        if (!Auth::check() || Auth::user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        try {
            $tourist = User::findOrFail($touristId);
            
            if ($tourist->role !== 'tourist') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a tourist.'
                ], 400);
            }

            $stamps = $tourist->stamps()->with('establishment')->get();

            return response()->json([
                'success' => true,
                'stamps' => $stamps
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stamps: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all stamps for a specific tourist
     */
    public function deleteAllStamps(Request $request, $touristId)
    {
        // Verify the user is a superadmin
        if (!Auth::check() || Auth::user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        try {
            $tourist = User::findOrFail($touristId);
            
            if ($tourist->role !== 'tourist') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a tourist.'
                ], 400);
            }

            $stampCount = $tourist->stamps()->count();
            
            // Delete all stamps for the tourist
            $tourist->stamps()->delete();
            
            // Also delete all corresponding visitor records for this tourist
            Visitor::where('user_id', $touristId)->delete();

            return response()->json([
                'success' => true,
                'message' => "All {$stampCount} stamps deleted for {$tourist->name}."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete stamps: ' . $e->getMessage()
            ], 500);
        }
    }
}
