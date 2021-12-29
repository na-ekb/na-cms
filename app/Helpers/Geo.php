<?php

namespace App\Helpers;

class Geo {
    public $latitude, $longitude;

    public function __construct($latitude, $longitude) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function distanceTo(Geo $point) {
        $distanceX = $this->latitude - $point->latitude;
        $distanceY = $this->longitude - $point->longitude;
        return sqrt($distanceX * $distanceX + $distanceY * $distanceY);
    }

    public function __toString() {
        return 'x: ' . $this->latitude . ', y: ' . $this->longitude;
    }

    public static function getClosest(array $curPoint, array $points) {
        if (empty($points) || empty($curPoint)) {
            return [];
        }
        $curPoint = new self($curPoint[0], $curPoint[1]);
        $curNearestPoint = new self($points[0][0], $points[0][1]);
        $curNearestDistance = $curPoint->distanceTo($curNearestPoint);
        foreach($points as $point) {
            $point = new self($point[0], $point[1]);
            $distance = $curPoint->distanceTo($point);
            if ($distance < $curNearestDistance) {
                $curNearestDistance = $distance;
                $curNearestPoint = $point;
            }
        }

        return $curNearestPoint;
    }
}