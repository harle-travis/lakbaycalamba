<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{
    use HasFactory;

    protected $fillable = [
        'establishment_name',
        'location',
        'latitude',
        'longitude',
        'maps_data',
        'description',
        'schedule',
        'category',
        'qr_code',
    ];

    protected $casts = [
        'schedule' => 'array',
    ];

    public function pictures()
    {
        return $this->hasMany(EstablishmentPic::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'name', 'establishment_name');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function stamps()
    {
        return $this->hasMany(Stamp::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }

    public function isCurrentlyOpen()
    {
        if (!$this->schedule) {
            return false;
        }

        // Handle case where schedule is stored as JSON string instead of array
        $schedule = $this->schedule;
        if (is_string($schedule)) {
            $schedule = json_decode($schedule, true);
            if (!$schedule) {
                return false;
            }
        }

        $currentDay = now()->format('l'); // Gets current day name (Monday, Tuesday, etc.)
        $currentTime = now()->format('H:i'); // Gets current time in 24-hour format

        if (!isset($schedule[$currentDay])) {
            return false;
        }

        $daySchedule = $schedule[$currentDay];
        
        if ($daySchedule === 'Closed') {
            return false;
        }

        // Parse the schedule (e.g., "9:00 AM - 6:00 PM" or "12:00 AM - 11:59 PM" for 24 hours)
        if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)\s*-\s*(\d{1,2}):(\d{2})\s*(AM|PM)/', $daySchedule, $matches)) {
            $openHour = (int)$matches[1];
            $openMinute = (int)$matches[2];
            $openPeriod = $matches[3];
            $closeHour = (int)$matches[4];
            $closeMinute = (int)$matches[5];
            $closePeriod = $matches[6];

            // Convert to 24-hour format
            if ($openPeriod === 'PM' && $openHour !== 12) {
                $openHour += 12;
            }
            if ($openPeriod === 'AM' && $openHour === 12) {
                $openHour = 0;
            }
            if ($closePeriod === 'PM' && $closeHour !== 12) {
                $closeHour += 12;
            }
            if ($closePeriod === 'AM' && $closeHour === 12) {
                $closeHour = 0;
            }

            $openTime = sprintf('%02d:%02d', $openHour, $openMinute);
            $closeTime = sprintf('%02d:%02d', $closeHour, $closeMinute);

            // Handle 24-hour operations - check for various 24-hour formats
            if (($openTime === '00:00' && $closeTime === '23:59') || 
                ($openTime === '12:00' && $closeTime === '11:59') ||
                ($openHour === 0 && $openMinute === 0 && $closeHour === 23 && $closeMinute === 59) ||
                // Handle the case where it's stored as "12:00 AM - 11:59 PM"
                ($matches[1] === '12' && $matches[2] === '00' && $matches[3] === 'AM' && 
                 $matches[4] === '11' && $matches[5] === '59' && $matches[6] === 'PM')) {
                return true; // Always open for 24-hour establishments
            }

            // For regular hours, check if current time is within the range
            return $currentTime >= $openTime && $currentTime <= $closeTime;
        }

        return false;
    }

    public function getOpeningTime()
    {
        if (!$this->schedule) {
            return null;
        }

        // Handle case where schedule is stored as JSON string instead of array
        $schedule = $this->schedule;
        if (is_string($schedule)) {
            $schedule = json_decode($schedule, true);
            if (!$schedule) {
                return null;
            }
        }

        $currentDay = now()->format('l');
        
        if (!isset($schedule[$currentDay])) {
            return null;
        }

        $daySchedule = $schedule[$currentDay];
        
        if ($daySchedule === 'Closed') {
            return 'Closed';
        }

        // Extract opening time from schedule
        if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)\s*-\s*(\d{1,2}):(\d{2})\s*(AM|PM)/', $daySchedule, $matches)) {
            $openHour = (int)$matches[1];
            $openMinute = (int)$matches[2];
            $openPeriod = $matches[3];
            $closeHour = (int)$matches[4];
            $closeMinute = (int)$matches[5];
            $closePeriod = $matches[6];

            // Convert to 24-hour format for comparison
            if ($openPeriod === 'PM' && $openHour !== 12) {
                $openHour += 12;
            }
            if ($openPeriod === 'AM' && $openHour === 12) {
                $openHour = 0;
            }
            if ($closePeriod === 'PM' && $closeHour !== 12) {
                $closeHour += 12;
            }
            if ($closePeriod === 'AM' && $closeHour === 12) {
                $closeHour = 0;
            }

            $openTime = sprintf('%02d:%02d', $openHour, $openMinute);
            $closeTime = sprintf('%02d:%02d', $closeHour, $closeMinute);

            // Handle 24-hour operations - check for various 24-hour formats
            if (($openTime === '00:00' && $closeTime === '23:59') || 
                ($openTime === '12:00' && $closeTime === '11:59') ||
                ($openHour === 0 && $openMinute === 0 && $closeHour === 23 && $closeMinute === 59) ||
                // Handle the case where it's stored as "12:00 AM - 11:59 PM"
                ($matches[1] === '12' && $matches[2] === '00' && $matches[3] === 'AM' && 
                 $matches[4] === '11' && $matches[5] === '59' && $matches[6] === 'PM')) {
                return '24 Hours';
            }

            // Return the original opening time format from the schedule
            return sprintf('%d:%02d %s', (int)$matches[1], (int)$matches[2], $matches[3]);
        }

        return null;
    }

    public function getClosingTime()
    {
        if (!$this->schedule) {
            return null;
        }

        // Handle case where schedule is stored as JSON string instead of array
        $schedule = $this->schedule;
        if (is_string($schedule)) {
            $schedule = json_decode($schedule, true);
            if (!$schedule) {
                return null;
            }
        }

        $currentDay = now()->format('l');
        
        if (!isset($schedule[$currentDay])) {
            return null;
        }

        $daySchedule = $schedule[$currentDay];
        
        if ($daySchedule === 'Closed') {
            return 'Closed';
        }

        // Extract closing time from schedule
        if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)\s*-\s*(\d{1,2}):(\d{2})\s*(AM|PM)/', $daySchedule, $matches)) {
            $openHour = (int)$matches[1];
            $openMinute = (int)$matches[2];
            $openPeriod = $matches[3];
            $closeHour = (int)$matches[4];
            $closeMinute = (int)$matches[5];
            $closePeriod = $matches[6];

            // Convert to 24-hour format for comparison
            if ($openPeriod === 'PM' && $openHour !== 12) {
                $openHour += 12;
            }
            if ($openPeriod === 'AM' && $openHour === 12) {
                $openHour = 0;
            }
            if ($closePeriod === 'PM' && $closeHour !== 12) {
                $closeHour += 12;
            }
            if ($closePeriod === 'AM' && $closeHour === 12) {
                $closeHour = 0;
            }

            $openTime = sprintf('%02d:%02d', $openHour, $openMinute);
            $closeTime = sprintf('%02d:%02d', $closeHour, $closeMinute);

            // Handle 24-hour operations - check for various 24-hour formats
            if (($openTime === '00:00' && $closeTime === '23:59') || 
                ($openTime === '12:00' && $closeTime === '11:59') ||
                ($openHour === 0 && $openMinute === 0 && $closeHour === 23 && $closeMinute === 59) ||
                // Handle the case where it's stored as "12:00 AM - 11:59 PM"
                ($matches[1] === '12' && $matches[2] === '00' && $matches[3] === 'AM' && 
                 $matches[4] === '11' && $matches[5] === '59' && $matches[6] === 'PM')) {
                return '24 Hours';
            }

            // Return the original closing time format from the schedule
            return sprintf('%d:%02d %s', (int)$matches[4], (int)$matches[5], $matches[6]);
        }

        return null;
    }
}
