<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Models\EstablishmentPic;
use App\Models\Visitor;
use App\Models\Review;
use App\Models\Stamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminEstablishmentController extends Controller
{
    public function index()
    {
        // Get the establishment assigned to the logged-in admin user via mapping table; fallback to name match
        $mappedEstablishmentId = \DB::table('admin_establishments')
            ->where('user_id', auth()->id())
            ->value('establishment_id');
        $establishment = $mappedEstablishmentId
            ? Establishment::with('pictures')->find($mappedEstablishmentId)
            : Establishment::where('establishment_name', auth()->user()->name)->with('pictures')->first();

        if (!$establishment) {
            return redirect()->back()->with('error', 'No establishment found for this admin account.');
        }

        return view('admin.manage', compact('establishment'));
    }

    public function dashboard()
    {
        // Get the establishment assigned to the logged-in admin user via mapping table; fallback to name match
        $mappedEstablishmentId = \DB::table('admin_establishments')
            ->where('user_id', auth()->id())
            ->value('establishment_id');
        $establishment = $mappedEstablishmentId
            ? Establishment::find($mappedEstablishmentId)
            : Establishment::where('establishment_name', auth()->user()->name)->first();

        if (!$establishment) {
            return redirect()->back()->with('error', 'No establishment found for this admin account.');
        }

        // Get visitor statistics (stamps + guests)
        $todayStamps = Stamp::where('establishment_id', $establishment->id)
            ->whereDate('visit_date', today())
            ->count();
        $todayGuests = Visitor::where('establishment_id', $establishment->id)
            ->where('is_guest', true)
            ->whereDate('visited_at', today())
            ->count();
        $todayVisitors = $todayStamps + $todayGuests;

        $weekStamps = Stamp::where('establishment_id', $establishment->id)
            ->whereBetween('visit_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $weekGuests = Visitor::where('establishment_id', $establishment->id)
            ->where('is_guest', true)
            ->whereBetween('visited_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $weekVisitors = $weekStamps + $weekGuests;

        $monthStamps = Stamp::where('establishment_id', $establishment->id)
            ->whereMonth('visit_date', now()->month)
            ->whereYear('visit_date', now()->year)
            ->count();
        $monthGuests = Visitor::where('establishment_id', $establishment->id)
            ->where('is_guest', true)
            ->whereMonth('visited_at', now()->month)
            ->whereYear('visited_at', now()->year)
            ->count();
        $monthVisitors = $monthStamps + $monthGuests;

        // Get reviews for this establishment
        $reviews = Review::where('establishment_id', $establishment->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate average rating
        $averageRating = $reviews->avg('rating') ?? 0;
        $totalReviews = $reviews->count();

        // Get rating distribution
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $reviews->where('rating', $i)->count();
            $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
            $ratingDistribution[$i] = $percentage;
        }

        // Get weekly visitor data for the last 4 weeks
        $weeklyData = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            $stampCount = Stamp::where('establishment_id', $establishment->id)
                ->whereBetween('visit_date', [$weekStart, $weekEnd])
                ->count();
            $guestCount = Visitor::where('establishment_id', $establishment->id)
                ->where('is_guest', true)
                ->whereBetween('visited_at', [$weekStart, $weekEnd])
                ->count();
            $weeklyData[] = [
                'week' => 'Week ' . (4 - $i),
                'count' => $stampCount + $guestCount
            ];
        }

        // Get monthly visitor data for the last 4 months
        $monthlyData = [];
        for ($i = 3; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $stampCount = Stamp::where('establishment_id', $establishment->id)
                ->whereMonth('visit_date', $month->month)
                ->whereYear('visit_date', $month->year)
                ->count();
            $guestCount = Visitor::where('establishment_id', $establishment->id)
                ->where('is_guest', true)
                ->whereMonth('visited_at', $month->month)
                ->whereYear('visited_at', $month->year)
                ->count();
            $monthlyData[] = [
                'month' => $month->format('M'),
                'count' => $stampCount + $guestCount
            ];
        }

        return view('admin.dash', compact(
            'establishment',
            'todayVisitors',
            'weekVisitors', 
            'monthVisitors',
            'todayStamps',
            'todayGuests',
            'weekStamps',
            'weekGuests',
            'monthStamps',
            'monthGuests',
            'reviews',
            'averageRating',
            'totalReviews',
            'ratingDistribution',
            'weeklyData',
            'monthlyData'
        ));
    }

    public function update(Request $request)
    {
        // Get the establishment that belongs to the logged-in admin user
        $establishment = Establishment::where('establishment_name', auth()->user()->name)->first();

        if (!$establishment) {
            return response()->json([
                'success' => false,
                'message' => 'No establishment found for this admin account.'
            ], 404);
        }

        $request->validate([
            'establishment_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'maps_data' => 'nullable|string',
            'description' => 'required|string',
            'schedule' => 'required|string',
            'category' => 'required|string|max:255',
            'pictures.*' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240'
        ]);

        // Update establishment details
        $establishment->update([
            'establishment_name' => $request->establishment_name,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'maps_data' => $request->maps_data,
            'description' => $request->description,
            'schedule' => $request->schedule,
            'category' => $request->category,
        ]);

        // Handle new picture uploads
        if ($request->hasFile('pictures')) {
            foreach ($request->file('pictures') as $picture) {
                $path = $picture->store('establishments', 'public');
                
                EstablishmentPic::create([
                    'establishment_id' => $establishment->id,
                    'image_path' => $path
                ]);
            }
        }

        // Update admin user name if establishment name changed
        if (auth()->user()->name !== $request->establishment_name) {
            auth()->user()->update(['name' => $request->establishment_name]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Establishment updated successfully!',
            'establishment' => $establishment->load('pictures')
        ]);
    }

    public function deletePicture($pictureId)
    {
        $picture = EstablishmentPic::findOrFail($pictureId);
        
        // Ensure the picture belongs to the admin's establishment
        $establishment = Establishment::where('establishment_name', auth()->user()->name)->first();
        
        if (!$establishment || $picture->establishment_id !== $establishment->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this picture.'
            ], 403);
        }

        // Delete from storage
        if (Storage::disk('public')->exists($picture->image_path)) {
            Storage::disk('public')->delete($picture->image_path);
        }

        // Delete from database
        $picture->delete();

        return response()->json([
            'success' => true,
            'message' => 'Picture deleted successfully!'
        ]);
    }

    public function logGuest(Request $request)
    {
        // Get the establishment that belongs to the logged-in admin user
        $establishment = Establishment::where('establishment_name', auth()->user()->name)->first();

        if (!$establishment) {
            return response()->json([
                'success' => false,
                'message' => 'No establishment found for this admin account.'
            ], 404);
        }

        $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_contact' => 'nullable|string|max:255'
        ]);

        try {
            Visitor::create([
                'establishment_id' => $establishment->id,
                'user_id' => null, // No user for guests
                'visited_at' => now(),
                'is_guest' => true,
                'guest_name' => $request->guest_name,
                'guest_contact' => $request->guest_contact
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Guest logged successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error logging guest: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getVisitorData(Request $request)
    {
        // Get the establishment that belongs to the logged-in admin user
        $establishment = Establishment::where('establishment_name', auth()->user()->name)->first();

        if (!$establishment) {
            return response()->json([
                'success' => false,
                'message' => 'No establishment found for this admin account.'
            ], 404);
        }

        $period = $request->get('period', 'week'); // week or month
        $weeks = $request->get('weeks', 4); // number of weeks/months to show

        if ($period === 'week') {
            $data = [];
            for ($i = $weeks - 1; $i >= 0; $i--) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $weekEnd = now()->subWeeks($i)->endOfWeek();
                $stampCount = Stamp::where('establishment_id', $establishment->id)
                    ->whereBetween('visit_date', [$weekStart, $weekEnd])
                    ->count();
                $guestCount = Visitor::where('establishment_id', $establishment->id)
                    ->where('is_guest', true)
                    ->whereBetween('visited_at', [$weekStart, $weekEnd])
                    ->count();
                $data[] = [
                    'label' => 'Week ' . ($weeks - $i),
                    'count' => $stampCount + $guestCount
                ];
            }
        } else {
            $data = [];
            for ($i = $weeks - 1; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $stampCount = Stamp::where('establishment_id', $establishment->id)
                    ->whereMonth('visit_date', $month->month)
                    ->whereYear('visit_date', $month->year)
                    ->count();
                $guestCount = Visitor::where('establishment_id', $establishment->id)
                    ->where('is_guest', true)
                    ->whereMonth('visited_at', $month->month)
                    ->whereYear('visited_at', $month->year)
                    ->count();
                $data[] = [
                    'label' => $month->format('M'),
                    'count' => $stampCount + $guestCount
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function reports(Request $request)
    {
        // Get the establishment that belongs to the logged-in admin user
        $establishment = Establishment::where('establishment_name', auth()->user()->name)->first();

        if (!$establishment) {
            return response()->json([
                'success' => false,
                'message' => 'No establishment found for this admin account.'
            ], 404);
        }

        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfMonth();
        // Use startOfDay to avoid an extra day being included in DatePeriod
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->startOfDay() : now()->startOfDay();

        // Aggregate daily counts for registered users (stamps) and guests
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->copy()->addDay());

        $daily = [];
        $totalsRegistered = 0;
        $totalsGuests = 0;

        foreach ($period as $date) {
            $dateStr = Carbon::instance($date)->format('Y-m-d');

            $registeredCount = Stamp::where('establishment_id', $establishment->id)
                ->whereDate('visit_date', $dateStr)
                ->count();
            $guestCount = Visitor::where('establishment_id', $establishment->id)
                ->where('is_guest', true)
                ->whereDate('visited_at', $dateStr)
                ->count();

            $totalsRegistered += $registeredCount;
            $totalsGuests += $guestCount;

            $daily[] = [
                'date' => Carbon::instance($date)->format('M d, Y'),
                'registered' => $registeredCount,
                'guests' => $guestCount,
                'total' => $registeredCount + $guestCount,
            ];
        }

        return response()->json([
            'success' => true,
            'summary' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'registered' => $totalsRegistered,
                'guests' => $totalsGuests,
                'total' => $totalsRegistered + $totalsGuests,
            ],
            'daily' => $daily,
        ]);
    }

    public function exportReportsCsv(Request $request)
    {
        // Get the establishment that belongs to the logged-in admin user
        $establishment = Establishment::where('establishment_name', auth()->user()->name)->first();

        if (!$establishment) {
            return response()->json([
                'success' => false,
                'message' => 'No establishment found for this admin account.'
            ], 404);
        }

        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfMonth();
        // Use startOfDay to avoid an extra day being included in DatePeriod
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->startOfDay() : now()->startOfDay();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="admin-report-'.now()->format('Ymd_His').'.csv"',
        ];

        $callback = function () use ($establishment, $startDate, $endDate) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Date', 'Registered Visitors', 'Guest Visitors', 'Total']);

            $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->copy()->addDay());

            foreach ($period as $date) {
                $dateStr = Carbon::instance($date)->format('Y-m-d');
                $registeredCount = Stamp::where('establishment_id', $establishment->id)
                    ->whereDate('visit_date', $dateStr)
                    ->count();
                $guestCount = Visitor::where('establishment_id', $establishment->id)
                    ->where('is_guest', true)
                    ->whereDate('visited_at', $dateStr)
                    ->count();

                fputcsv($output, [
                    Carbon::instance($date)->format('M d, Y'),
                    $registeredCount,
                    $guestCount,
                    $registeredCount + $guestCount,
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
