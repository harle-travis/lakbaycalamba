<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Establishment;
use App\Models\Visitor;
use App\Models\User;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Get current date and time
        $now = Carbon::now();
        
        // Sorting for Visitors Tracking
        $sort = $request->get('sort'); // e.g., establishment, today, week, month, custom
        $order = strtolower($request->get('order', 'desc')) === 'asc' ? 'asc' : 'desc';

        // Handle custom date range from request
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : $now->copy()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : $now->copy()->endOfDay();
        // Ensure startDate is not after endDate; if so, swap
        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }
        
        // Calculate date ranges
        $today = $now->copy()->startOfDay();
        $weekStart = $now->copy()->startOfWeek();
        $monthStart = $now->copy()->startOfMonth();
        
        // Get total visitors (stamps + guests) for different periods
        $todayStamps = \App\Models\Stamp::whereDate('visit_date', $today)->count();
        $todayGuests = \App\Models\Visitor::where('is_guest', true)->whereDate('visited_at', $today)->count();
        $todayVisitors = $todayStamps + $todayGuests;
        
        $weekStamps = \App\Models\Stamp::where('visit_date', '>=', $weekStart)->count();
        $weekGuests = \App\Models\Visitor::where('is_guest', true)->where('visited_at', '>=', $weekStart)->count();
        $weekVisitors = $weekStamps + $weekGuests;
        
        $monthStamps = \App\Models\Stamp::where('visit_date', '>=', $monthStart)->count();
        $monthGuests = \App\Models\Visitor::where('is_guest', true)->where('visited_at', '>=', $monthStart)->count();
        $monthVisitors = $monthStamps + $monthGuests;
        
        // Calculate previous period for comparison
        $yesterdayStamps = \App\Models\Stamp::whereDate('visit_date', $today->copy()->subDay())->count();
        $yesterdayGuests = \App\Models\Visitor::where('is_guest', true)->whereDate('visited_at', $today->copy()->subDay())->count();
        $yesterdayVisitors = $yesterdayStamps + $yesterdayGuests;
        
        $lastWeekStamps = \App\Models\Stamp::whereBetween('visit_date', [
            $weekStart->copy()->subWeek(),
            $weekStart->copy()->subDay()
        ])->count();
        $lastWeekGuests = \App\Models\Visitor::where('is_guest', true)->whereBetween('visited_at', [
            $weekStart->copy()->subWeek(),
            $weekStart->copy()->subDay()
        ])->count();
        $lastWeekVisitors = $lastWeekStamps + $lastWeekGuests;
        
        $lastMonthStamps = \App\Models\Stamp::whereBetween('visit_date', [
            $monthStart->copy()->subMonth(),
            $monthStart->copy()->subDay()
        ])->count();
        $lastMonthGuests = \App\Models\Visitor::where('is_guest', true)->whereBetween('visited_at', [
            $monthStart->copy()->subMonth(),
            $monthStart->copy()->subDay()
        ])->count();
        $lastMonthVisitors = $lastMonthStamps + $lastMonthGuests;
        
        // Calculate percentage changes
        $todayChange = $yesterdayVisitors > 0 ? 
            round((($todayVisitors - $yesterdayVisitors) / $yesterdayVisitors) * 100, 1) : 0;
        $weekChange = $lastWeekVisitors > 0 ? 
            round((($weekVisitors - $lastWeekVisitors) / $lastWeekVisitors) * 100, 1) : 0;
        $monthChange = $lastMonthVisitors > 0 ? 
            round((($monthVisitors - $lastMonthVisitors) / $lastMonthVisitors) * 100, 1) : 0;
        
        // Get active establishments count
        $activeEstablishments = Establishment::count();
        
        // Get visitor data per establishment for the table (stamps + guests)
        $establishmentStats = Establishment::withCount([
            'stamps as today_stamps' => function ($query) use ($today) {
                $query->whereDate('visit_date', $today);
            },
            'stamps as week_stamps' => function ($query) use ($weekStart) {
                $query->where('visit_date', '>=', $weekStart);
            },
            'stamps as month_stamps' => function ($query) use ($monthStart) {
                $query->where('visit_date', '>=', $monthStart);
            },
            'stamps as custom_range_stamps' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('visit_date', [$startDate, $endDate]);
            },
            'visitors as today_guests' => function ($query) use ($today) {
                $query->where('is_guest', true)->whereDate('visited_at', $today);
            },
            'visitors as week_guests' => function ($query) use ($weekStart) {
                $query->where('is_guest', true)->where('visited_at', '>=', $weekStart);
            },
            'visitors as month_guests' => function ($query) use ($monthStart) {
                $query->where('is_guest', true)->where('visited_at', '>=', $monthStart);
            },
            'visitors as custom_range_guests' => function ($query) use ($startDate, $endDate) {
                $query->where('is_guest', true)->whereBetween('visited_at', [$startDate, $endDate]);
            }
        ])->orderBy('establishment_name')->get();

        // Calculate total visitors for each establishment
        foreach ($establishmentStats as $establishment) {
            $establishment->today_visitors = $establishment->today_stamps + $establishment->today_guests;
            $establishment->week_visitors = $establishment->week_stamps + $establishment->week_guests;
            $establishment->month_visitors = $establishment->month_stamps + $establishment->month_guests;
            $establishment->custom_range_visitors = $establishment->custom_range_stamps + $establishment->custom_range_guests;
        }

        // Apply sorting based on request
        $isCustomRange = $request->filled('start_date') || $request->filled('end_date');
        $defaultSortKey = $isCustomRange ? 'custom_range_visitors' : 'today_visitors';
        $sortKeyMap = [
            'establishment' => 'establishment_name',
            'today' => 'today_visitors',
            'week' => 'week_visitors',
            'month' => 'month_visitors',
            'custom' => 'custom_range_visitors',
        ];
        $sortKey = $sortKeyMap[$sort] ?? $defaultSortKey;
        $establishmentStats = $order === 'asc'
            ? $establishmentStats->sortBy($sortKey)->values()
            : $establishmentStats->sortByDesc($sortKey)->values();
        
        // Get visitor trends for the selected range (inclusive), grouped by day (stamps + guests)
        $visitorTrends = [];
        $cursorDate = $startDate->copy()->startOfDay();
        $endOfRange = $endDate->copy()->endOfDay();
        while ($cursorDate->lte($endOfRange)) {
            $stampCount = \App\Models\Stamp::whereDate('visit_date', $cursorDate)->count();
            $guestCount = \App\Models\Visitor::where('is_guest', true)->whereDate('visited_at', $cursorDate)->count();
            $visitorTrends[] = [
                'date' => $cursorDate->format('M d'),
                'visitors' => $stampCount + $guestCount
            ];
            $cursorDate->addDay();
        }

        // Chart title to reflect range
        $visitorTrendsTitle = 'Visitor Trends (' . $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y') . ')';
        
        return view('superadmin.dashboard', compact(
            'todayVisitors',
            'weekVisitors', 
            'monthVisitors',
            'todayStamps',
            'todayGuests',
            'weekStamps',
            'weekGuests',
            'monthStamps',
            'monthGuests',
            'activeEstablishments',
            'todayChange',
            'weekChange',
            'monthChange',
            'establishmentStats',
            'visitorTrends',
            'startDate',
            'endDate',
            'visitorTrendsTitle',
            'sort',
            'order'
        ));
    }

    public function manageRewards()
    {
        // Get users with 4 or more stamps (temporary threshold)
        $rewardEligibleUsers = User::where('role', 'tourist')
            ->withCount('stamps')
            ->having('stamps_count', '>=', 4)
            ->with(['stamps.establishment'])
            ->orderBy('stamps_count', 'desc')
            ->get();

        return view('superadmin.manage_rewards', compact('rewardEligibleUsers'));
    }

    public function sendRewardNotifications(Request $request)
    {
        // Log all request data for debugging
        \Log::info('=== SEND REWARD NOTIFICATIONS REQUEST ===');
        \Log::info('All request data:', $request->all());
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request URL: ' . $request->fullUrl());
        
        $userIds = $request->input('user_ids', []);
        $emailSubject = $request->input('email_subject', '🎉 Reward Eligibility - Tourism Monitoring System');
        $emailContent = $request->input('email_content', '');
        $useCustomContent = $request->has('use_custom_content');
        
        // Debug logging
        \Log::info('Send reward notifications request', [
            'user_ids' => $userIds,
            'email_subject' => $emailSubject,
            'use_custom_content' => $useCustomContent,
            'mail_driver' => config('mail.default')
        ]);
        
        if (empty($userIds)) {
            return back()->with('error', 'No users selected for notification.');
        }

        $users = User::whereIn('id', $userIds)->get();
        
        // Debug: Log all users being processed
        \Log::info('Users found for notification:', $users->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'lakbay_id' => $user->lakbay_id
            ];
        })->toArray());
        
        $sentCount = 0;

        foreach ($users as $user) {
            try {
                // Validate user email
                if (empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    \Log::warning('Skipping user with invalid email', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'user_name' => $user->name
                    ]);
                    continue;
                }
                
                \Log::info('Attempting to send email to user', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_name' => $user->name,
                    'use_custom_content' => $useCustomContent
                ]);
                
                if ($useCustomContent && !empty($emailContent)) {
                    // Replace placeholders in custom content
                    $processedContent = $this->replaceEmailPlaceholders($emailContent, $user);
                    \Mail::raw($processedContent, function ($message) use ($user, $emailSubject) {
                        $message->to($user->email, $user->name)
                                ->from(config('mail.from.address'), config('mail.from.name'))
                                ->subject($emailSubject);
                    });
                } else {
                    // Send default template
                    \Mail::send('emails.reward-notification', ['user' => $user], function ($message) use ($user, $emailSubject) {
                        $message->to($user->email, $user->name)
                                ->from(config('mail.from.address'), config('mail.from.name'))
                                ->subject($emailSubject);
                    });
                }
                $sentCount++;
                \Log::info('Successfully sent email to user', ['user_id' => $user->id]);
            } catch (\Exception $e) {
                \Log::error('Failed to send reward notification to user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        $message = "🎉 Success! Reward notifications sent to {$sentCount} user(s) successfully.";
        if (config('mail.default') === 'log') {
            $message .= " (Emails logged to storage/logs/laravel.log)";
        }
        
        return back()->with('success', $message);
    }

    public function previewEmail(Request $request)
    {
        $user = User::find($request->input('user_id'));
        $emailSubject = $request->input('email_subject', '🎉 Reward Eligibility - Tourism Monitoring System');
        $emailContent = $request->input('email_content', '');
        $useCustomContent = $request->has('use_custom_content');
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        try {
            if ($useCustomContent && !empty($emailContent)) {
                // Replace placeholders in custom content
                $processedContent = $this->replaceEmailPlaceholders($emailContent, $user);
                $htmlContent = nl2br(e($processedContent));
                return response()->json([
                    'success' => true,
                    'subject' => $emailSubject,
                    'content' => $htmlContent,
                    'type' => 'custom'
                ]);
            } else {
                // Render default template
                $htmlContent = view('emails.reward-notification', ['user' => $user])->render();
                return response()->json([
                    'success' => true,
                    'subject' => $emailSubject,
                    'content' => $htmlContent,
                    'type' => 'template'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to preview email: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Replace email placeholders with user data
     */
    private function replaceEmailPlaceholders($content, $user)
    {
        $placeholders = [
            '{user_name}' => $user->name,
            '{user_email}' => $user->email,
            '{lakbay_id}' => $user->lakbay_id,
            '{stamps_count}' => $user->stamps_count ?? 0,
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $content);
    }
}
