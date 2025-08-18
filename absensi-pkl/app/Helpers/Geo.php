<?php
namespace App\Helpers;
class Geo {
    // Haversine distance in meters
    public static function distance(float $lat1, float $lon1, float $lat2, float $lon2): float {
        $R = 6371000;
        $phi1 = deg2rad($lat1); $phi2 = deg2rad($lat2);
        $dphi = deg2rad($lat2 - $lat1); $dlambda = deg2rad($lon2 - $lon1);
        $a = sin($dphi/2)**2 + cos($phi1)*cos($phi2)*sin($dlambda/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
}