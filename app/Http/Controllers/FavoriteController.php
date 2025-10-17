<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Establishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Toggle favorite status for an establishment
     */
    public function toggle(Request $request, $establishmentId)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to add favorites.',
                'redirect' => route('login')
            ], 401);
        }

        // Check if user is a tourist
        if (Auth::user()->role !== 'tourist') {
            return response()->json([
                'success' => false,
                'message' => 'Only tourists can add favorites.'
            ], 403);
        }

        try {
            $user = Auth::user();
            $establishment = Establishment::findOrFail($establishmentId);

            // Check if already favorited
            $existingFavorite = Favorite::where('user_id', $user->id)
                ->where('establishment_id', $establishmentId)
                ->first();

            if ($existingFavorite) {
                // Remove from favorites
                $existingFavorite->delete();
                
                return response()->json([
                    'success' => true,
                    'action' => 'removed',
                    'message' => "Removed {$establishment->establishment_name} from favorites.",
                    'isFavorited' => false
                ]);
            } else {
                // Add to favorites
                Favorite::create([
                    'user_id' => $user->id,
                    'establishment_id' => $establishmentId,
                ]);
                
                return response()->json([
                    'success' => true,
                    'action' => 'added',
                    'message' => "Added {$establishment->establishment_name} to favorites!",
                    'isFavorited' => true
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update favorites: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if an establishment is favorited by the current user
     */
    public function check(Request $request, $establishmentId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'isFavorited' => false,
                'redirect' => route('login')
            ]);
        }

        try {
            $isFavorited = Favorite::where('user_id', Auth::id())
                ->where('establishment_id', $establishmentId)
                ->exists();

            return response()->json([
                'success' => true,
                'isFavorited' => $isFavorited
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check favorite status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's favorites
     */
    public function getUserFavorites()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to view favorites.'
            ], 401);
        }

        try {
            $favorites = Auth::user()->favorites()->with('establishment')->get();

            return response()->json([
                'success' => true,
                'favorites' => $favorites
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch favorites: ' . $e->getMessage()
            ], 500);
        }
    }
}
