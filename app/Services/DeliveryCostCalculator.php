<?php

namespace App\Services;

use App\Models\Setting;

class DeliveryCostCalculator
{
    /**
     * Calculate delivery cost based on vehicle type and distance
     * Formula: (Base Fee + (Distance * Rate per KM)) * Fuel Adjustment
     * With minimum charge applied
     */
    public static function calculate(string $vehicleType, float $distanceKm): float
    {
        $baseFee = (float) Setting::get("{$vehicleType}_base_fee", 0);
        $ratePerKm = (float) Setting::get("{$vehicleType}_rate_per_km", 0);
        $minCharge = (float) Setting::get("{$vehicleType}_min_charge", 0);
        $fuelAdjustment = (float) Setting::get('fuel_adjustment', 1.00);

        // Calculate base cost
        $cost = ($baseFee + ($distanceKm * $ratePerKm)) * $fuelAdjustment;

        // Apply minimum charge
        $cost = max($cost, $minCharge);

        return round($cost, 2);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    public static function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Get maximum delivery distance for a vehicle type
     */
    public static function getMaxDistance(string $vehicleType): int
    {
        return (int) Setting::get("{$vehicleType}_max_distance", 90);
    }

    /**
     * Check if delivery is within range
     */
    public static function isWithinRange(string $vehicleType, float $distanceKm): bool
    {
        $maxDistance = self::getMaxDistance($vehicleType);
        return $distanceKm <= $maxDistance;
    }
}
