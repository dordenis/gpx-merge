<?php

namespace Geo\Parser;

use adriangibbons\phpFITFileAnalysis;
use Geo\Track;
use Location\Coordinate;
use Location\Polyline;
use Location\Processor\Polyline\SimplifyInterface;

class ParserFit implements InterfaceParseFile
{
    private Track $track;

    public function trackFile(string $file): InterfaceParseFile
    {
        //$options = ['data_every_second' => true];
        $fit = new phpFITFileAnalysis($file);
        $track = new Track();

        $polyline = new Polyline();
        foreach ($fit->data_mesgs['record']['timestamp'] as $timestamp) {
            $lat = $fit->data_mesgs['record']['position_lat'][$timestamp];
            $lon = $fit->data_mesgs['record']['position_long'][$timestamp];

            $polyline->addPoint(new Coordinate($lat, $lon));
        }

        $track->addPolyline($polyline);
        $this->track = $track;
        return $this;
    }

    public function simplify(SimplifyInterface $simplify): InterfaceParseFile
    {
        $track = new Track();
        foreach ($this->track->getPolylines() as $polyline) {
            $polyline = $simplify->simplify($polyline);
            $track->addPolyline($polyline);
        }

        $this->track = $track;
        return $this;
    }

    public function getTrack(): Track
    {
        return $this->track;
    }

}