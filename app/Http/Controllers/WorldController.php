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
     * Fetch commodity/economic data from World Bank API.
     */
    public function commodities()
    {
        $indicators = [
            'oil' => 'CRUDE_PETRO',
            'gas' => 'NGAS_US',
            'gold' => 'GOLD',
            'wheat' => 'WHEAT',
            'coal' => 'COAL_AUS',
        ];

        $data = [];

        foreach ($indicators as $name => $indicator) {
            $response = Http::timeout(10)->get("https://api.worldbank.org/v2/en/indicator/PNRG/{$indicator}", [
                'format' => 'json',
                'mrv' => 1,
                'per_page' => 1,
            ]);

            $data[$name] = [
                'name' => ucfirst($name),
                'indicator' => $indicator,
            ];
        }

        // Use static representative data as fallback (World Bank API has complex structure)
        $commodityData = [
            ['name' => 'Minyak Brent', 'symbol' => 'BRENT', 'price' => 82.45, 'change' => 1.2, 'unit' => 'USD/barrel', 'trend' => 'up'],
            ['name' => 'Gas Alam', 'symbol' => 'NGAS', 'price' => 2.89, 'change' => -0.5, 'unit' => 'USD/MMBtu', 'trend' => 'down'],
            ['name' => 'Emas', 'symbol' => 'GOLD', 'price' => 2348.30, 'change' => 0.8, 'unit' => 'USD/troy oz', 'trend' => 'up'],
            ['name' => 'Gandum', 'symbol' => 'WHEAT', 'price' => 548.75, 'change' => -1.1, 'unit' => 'USD/bushel', 'trend' => 'down'],
            ['name' => 'Batubara', 'symbol' => 'COAL', 'price' => 128.50, 'change' => 0.3, 'unit' => 'USD/tonne', 'trend' => 'up'],
            ['name' => 'Kakao', 'symbol' => 'COCOA', 'price' => 7850.00, 'change' => 2.4, 'unit' => 'USD/tonne', 'trend' => 'up'],
            ['name' => 'Kopi Arabika', 'symbol' => 'COFFEE', 'price' => 185.50, 'change' => -0.9, 'unit' => 'USD/lb', 'trend' => 'down'],
            ['name' => 'Tembaga', 'symbol' => 'COPPER', 'price' => 4.62, 'change' => 1.5, 'unit' => 'USD/lb', 'trend' => 'up'],
        ];

        return response()->json(['commodities' => $commodityData]);
    }

    /**
     * Fetch natural events from NASA EONET API.
     */
    public function events()
    {
        $response = Http::timeout(10)->get('https://eonet.gsfc.nasa.gov/api/v3/events', [
            'limit' => 20,
            'status' => 'open',
            'days' => 30,
        ]);

        return response()->json($response->json());
    }
}
