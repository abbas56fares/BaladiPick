<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminSettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        
        return view('admin.settings', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Bike settings
            'bike_base_fee' => 'required|numeric|min:0',
            'bike_rate_per_km' => 'required|numeric|min:0',
            'bike_min_charge' => 'required|numeric|min:0',
            'bike_max_distance' => 'required|numeric|min:1',
            
            // Car settings
            'car_base_fee' => 'required|numeric|min:0',
            'car_rate_per_km' => 'required|numeric|min:0',
            'car_min_charge' => 'required|numeric|min:0',
            'car_max_distance' => 'required|numeric|min:1',
            
            // Pickup settings
            'pickup_base_fee' => 'required|numeric|min:0',
            'pickup_rate_per_km' => 'required|numeric|min:0',
            'pickup_min_charge' => 'required|numeric|min:0',
            'pickup_max_distance' => 'required|numeric|min:1',
            
            // Fuel adjustment
            'fuel_adjustment' => 'required|numeric|min:0.1|max:5',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clear settings cache
        Cache::forget('settings');

        return back()->with('success', 'Settings updated successfully. All new orders will use the updated pricing.');
    }
}
