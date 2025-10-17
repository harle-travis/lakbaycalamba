<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Establishment::with(['pictures', 'reviews']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('establishment_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $establishments = $query->take(6)->get();

        // Apply status filter in PHP since we need to check current time
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'open') {
                $establishments = $establishments->filter(function($establishment) {
                    return $establishment->isCurrentlyOpen();
                });
            } elseif ($request->status === 'closed') {
                $establishments = $establishments->filter(function($establishment) {
                    return !$establishment->isCurrentlyOpen();
                });
            }
        }

        // Apply rating filter in PHP for more accurate results
        if ($request->filled('rating') && $request->rating !== 'all') {
            $rating = (int)$request->rating;
            $establishments = $establishments->filter(function($establishment) use ($rating) {
                if ($establishment->reviews->count() === 0) {
                    return $rating === 0; // "No reviews" option
                }
                $avgRating = $establishment->reviews->avg('rating');
                return $avgRating >= $rating;
            });
        }

        // Get unique categories from database for dropdown
        $categories = Establishment::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return view('home', compact('establishments', 'categories'));
    }

    public function allEstablishments(Request $request)
    {
        $query = Establishment::with(['pictures', 'reviews']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('establishment_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $establishments = $query->get();

        // Apply status filter in PHP since we need to check current time
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'open') {
                $establishments = $establishments->filter(function($establishment) {
                    return $establishment->isCurrentlyOpen();
                });
            } elseif ($request->status === 'closed') {
                $establishments = $establishments->filter(function($establishment) {
                    return !$establishment->isCurrentlyOpen();
                });
            }
        }

        // Apply rating filter in PHP for more accurate results
        if ($request->filled('rating') && $request->rating !== 'all') {
            $rating = (int)$request->rating;
            $establishments = $establishments->filter(function($establishment) use ($rating) {
                if ($establishment->reviews->count() === 0) {
                    return $rating === 0; // "No reviews" option
                }
                $avgRating = $establishment->reviews->avg('rating');
                return $avgRating >= $rating;
            });
        }

        // Get unique categories from database for dropdown
        $categories = Establishment::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return view('all-establishments', compact('establishments', 'categories'));
    }

    public function show(Establishment $establishment)
    {
        $establishment->load(['pictures', 'reviews.user']);
        
        return view('establishment.show', compact('establishment'));
    }
}
