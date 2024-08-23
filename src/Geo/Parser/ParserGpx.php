<?php

namespace Geo\Parser;

//https://github.com/mjaschen/phpgeo/tree/main/src

use Geo\Track;
use Location\Coordinate;
use Location\Polyline;
use Location\Processor\Polyline\SimplifyInterface;

class ParserGpx implements InterfaceParseFile
{
    private Track $track;

    public function trackFile(string $file): InterfaceParseFile
    {
        $xml = simplexml_load_file($file);
        return $this->trackXml($xml);
    }

    public function trackXml(\SimpleXMLElement $xml): InterfaceParseFile
    {
        $track = new Track();

        foreach ($xml->trk as $trk) {
            foreach ($trk->trkseg as $seg) {
                $polyline = new Polyline();
                foreach ($seg->trkpt as $trkpt) {
                    $lat = (float)$trkpt["lat"];
                    $lon = (float)$trkpt["lon"];

                    if ($this->isNull($lat, $lon)) {
                        continue;
                    }

                    $polyline->addPoint(new Coordinate($lat, $lon));
                }

                $track->addPolyline($polyline);
            }
        }

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

    private function isNull($lat, $lon): bool
    {
        return empty($lat) || empty($lon);
    }

    public function getTrack(): Track
    {
        return $this->track;
    }
}