<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    /**
     * Show the weather page for Calamba, Laguna with Open-Meteo data.
     */
    public function show()
    {
        $latitude = 14.2117;
        $longitude = 121.1653;

        $weatherData = Cache::remember('open_meteo_calamba_10m', 600, function () use ($latitude, $longitude) {
            $params = [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m,visibility',
                'hourly' => 'temperature_2m,precipitation_probability,weather_code',
                'daily' => 'temperature_2m_max,temperature_2m_min,weather_code,sunrise,sunset',
                'timezone' => 'Asia/Manila',
            ];

            $response = Http::get('https://api.open-meteo.com/v1/forecast', $params);
            if (!$response->ok()) {
                \Log::error('Open-Meteo API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                abort(502, 'Weather data unavailable');
            }

            $data = $response->json();

            // Helpers to map weather codes to labels + icons
            $mapCodeTo = function (int $code): array {
                if ($code === 0) {
                    return ['desc' => 'Clear sky', 'icon_class' => 'wi wi-day-sunny'];
                }
                if (in_array($code, [1, 2], true)) {
                    return ['desc' => 'Partly cloudy', 'icon_class' => 'wi wi-day-cloudy'];
                }
                if (in_array($code, [3, 45, 48], true)) {
                    return ['desc' => 'Cloudy', 'icon_class' => 'wi wi-cloud'];
                }
                if (in_array($code, [51, 53, 55, 61, 63, 65, 80, 81, 82], true)) {
                    return ['desc' => 'Rain', 'icon_class' => 'wi wi-rain'];
                }
                if (in_array($code, [71, 73, 75, 77, 85, 86], true)) {
                    return ['desc' => 'Snow', 'icon_class' => 'wi wi-snow'];
                }
                if (in_array($code, [95, 96, 99], true)) {
                    return ['desc' => 'Thunderstorm', 'icon_class' => 'wi wi-thunderstorm'];
                }
                return ['desc' => 'Unknown', 'icon_class' => 'wi wi-na'];
            };
            
            // Normalize
            $timezone = 'Asia/Manila';
            $currentTs = \Carbon\Carbon::parse($data['current']['time'] ?? 'now', $timezone)->timestamp;
            $currentMap = $mapCodeTo((int)($data['current']['weather_code'] ?? 0));

            $normalized = [
                'timezone' => $timezone,
                'current' => [
                    'dt' => $currentTs,
                    'temp' => $data['current']['temperature_2m'] ?? null,
                    'humidity' => $data['current']['relative_humidity_2m'] ?? null,
                    // Convert km/h to m/s to match conversion back in Blade
                    'wind_speed' => isset($data['current']['wind_speed_10m']) ? ($data['current']['wind_speed_10m'] / 3.6) : null,
                    'weather' => [[
                        'description' => $currentMap['desc'],
                        'icon' => null,
                    ]],
                    'icon_class' => $currentMap['icon_class'],
                ],
                'hourly' => [],
                'daily' => [],
            ];

            // Hourly forecast (24 hours)
            $hourTimes = $data['hourly']['time'] ?? [];
            $hourTemps = $data['hourly']['temperature_2m'] ?? [];
            $hourPops = $data['hourly']['precipitation_probability'] ?? [];
            $hourCodes = $data['hourly']['weather_code'] ?? [];
            $count = min(24, count($hourTimes));
            for ($i = 0; $i < $count; $i++) {
                $ts = \Carbon\Carbon::parse($hourTimes[$i], $timezone)->timestamp;
                $map = $mapCodeTo((int)($hourCodes[$i] ?? 0));
                $normalized['hourly'][] = [
                    'dt' => $ts,
                    'temp' => $hourTemps[$i] ?? null,
                    'pop' => isset($hourPops[$i]) ? ((int)$hourPops[$i] / 100) : 0,
                    'weather' => [[
                        'icon' => null,
                    ]],
                    'icon_class' => $map['icon_class'],
                ];
            }

            // Daily forecast (7 days)
            $dayTimes = $data['daily']['time'] ?? [];
            $dayMax = $data['daily']['temperature_2m_max'] ?? [];
            $dayMin = $data['daily']['temperature_2m_min'] ?? [];
            $dayCodes = $data['daily']['weather_code'] ?? [];
            $dcount = min(7, count($dayTimes));
            for ($i = 0; $i < $dcount; $i++) {
                $ts = \Carbon\Carbon::parse($dayTimes[$i], $timezone)->timestamp;
                $map = $mapCodeTo((int)($dayCodes[$i] ?? 0));
                $normalized['daily'][] = [
                    'dt' => $ts,
                    'temp' => [
                        'max' => $dayMax[$i] ?? null,
                        'min' => $dayMin[$i] ?? null,
                    ],
                    'weather' => [[
                        'icon' => null,
                    ]],
                    'icon_class' => $map['icon_class'],
                ];
            }

            return $normalized;
        });

        return view('weather', [
            'weather' => $weatherData,
            'lat' => $latitude,
            'lon' => $longitude,
        ]);
    }
}
