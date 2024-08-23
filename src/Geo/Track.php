<?php

namespace Geo;

use Location\Distance\Vincenty;
use Location\Polyline;

class Track
{
    public string $name;

    /**
     * @var array<Polyline>
     */
    private array $polylines = [];

    public function addPolyline(Polyline $polyline): void
    {
        $this->polylines[] = $polyline;
    }

    /**
     * @return  array<Polyline>
     */
    public function getPolylines(): array
    {
        return $this->polylines;
    }

    public function isVolgograd(): bool
    {
        foreach ($this->getPolylines() as $polyline) {
            foreach ($polyline->getPoints() as $point) {
                $lat = (int) $point->getLat();
                return ($lat == 48);
            }
        }

        return false;
    }

    public function getLength(): float
    {
        $dist = 0;
        foreach ($this->getPolylines() as $polyline) {
            $dist += $polyline->getLength(new Vincenty());
        }
        return $dist;
    }

}