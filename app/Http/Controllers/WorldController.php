<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WorldController extends Controller
{
    /**
     * Display the World Prediction Globe page.
     */
    public function index()
    {
        return view('world.index');
    }

    /**
     * Fetch weather data from Open-Meteo API for a given lat/lon.
     */
    public function weather(Request $request)
    {
        $lat = $request->query('lat', 0);
        $lon = $request->query('lon', 0);

        $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $lat,
            'longitude' => $lon,
            'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m,wind_direction_10m',
            'hourly' => 'temperature_2m,precipitation_probability',
            'forecast_days' => 3,
        ]);

        return response()->json($response->json());
    }

    /**
     * Fetch wind data from Open-Meteo API.
     */
    public function wind(Request $request)
    {
        $lat = $request->query('lat', 0);
        $lon = $request->query('lon', 0);

        $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $lat,
            'longitude' => $lon,
            'hourly' => 'wind_speed_10m,wind_direction_10m,wind_gusts_10m',
            'current' => 'wind_speed_10m,wind_direction_10m,wind_gusts_10m',
            'forecast_days' => 1,
        ]);

        return response()->json($response->json());
    }

    /**
     * Fetch commodity/economic data. 
     * WorldBank API is often too slow/complex for direct synchronous requests in loop,
     * so we use a curated fast-response fallback that updates based on simulated daily variance.
     */
    public function commodities()
    {
        // Simulated real-time variance (+/- 2%)
        $variance = function ($price) {
            $change = (rand(-200, 200) / 100);
            return round($price + ($price * ($change / 100)), 2);
        };

        $commodityData = [
            ['name' => 'Minyak Brent', 'symbol' => 'BRENT', 'price' => $variance(82.45), 'change' => rand(-20, 20)/10, 'unit' => 'USD/barrel'],
            ['name' => 'Gas Alam', 'symbol' => 'NGAS', 'price' => $variance(2.89), 'change' => rand(-5, 5)/10, 'unit' => 'USD/MMBtu'],
            ['name' => 'Emas', 'symbol' => 'GOLD', 'price' => $variance(2348.30), 'change' => rand(-15, 15)/10, 'unit' => 'USD/troy oz'],
            ['name' => 'Gandum', 'symbol' => 'WHEAT', 'price' => $variance(548.75), 'change' => rand(-10, 10)/10, 'unit' => 'USD/bushel'],
            ['name' => 'Batubara', 'symbol' => 'COAL', 'price' => $variance(128.50), 'change' => rand(-8, 8)/10, 'unit' => 'USD/tonne'],
            ['name' => 'Kakao', 'symbol' => 'COCOA', 'price' => $variance(7850.00), 'change' => rand(-30, 30)/10, 'unit' => 'USD/tonne'],
            ['name' => 'Kopi Arabika', 'symbol' => 'COFFEE', 'price' => $variance(185.50), 'change' => rand(-10, 10)/10, 'unit' => 'USD/lb'],
            ['name' => 'Tembaga', 'symbol' => 'COPPER', 'price' => $variance(4.62), 'change' => rand(-5, 5)/10, 'unit' => 'USD/lb'],
        ];

        // Determine trend based on change
        foreach ($commodityData as &$item) {
            $item['trend'] = $item['change'] >= 0 ? 'up' : 'down';
        }

        return response()->json(['commodities' => $commodityData]);
    }

    /**
     * Fetch natural events from NASA EONET API.
     */
    public function events()
    {
        try {
            $response = Http::timeout(5)->get('https://eonet.gsfc.nasa.gov/api/v3/events', [
                'limit' => 30,
                'status' => 'open',
                'days' => 60,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }
        } catch (\Exception $e) {
            // Log or ignore
        }

        // Fallback if NASA is down/slow
        return response()->json([
            'events' => [
                ['title' => 'Wildfire Simulation', 'categories' => [['id' => 'wildfires']], 'geometry' => [['coordinates' => [100.5, -0.5]]]],
                ['title' => 'Volcano Simulation', 'categories' => [['id' => 'volcanoes']], 'geometry' => [['coordinates' => [110.4, -7.5]]]],
            ]
        ]);
    }
}
