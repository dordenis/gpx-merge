<?php

namespace Geo\Create;

use Geo\Parser\InterfaceParseFile;
use Geo\Track;

class CreateGpx
{
    /**
     * @var array<Track>
     */
    private array $tracks = [];
    private \SimpleXMLElement $xml;
    private array $name = [];

    public function __construct(string|null $file=null)
    {
        if (is_null($file)) {
            $xmlstr = '<?xml version="1.0" standalone="yes"?><gpx></gpx>';
            $this->xml = new \SimpleXMLElement($xmlstr);
        } else {
            $this->xml = simplexml_load_file($file);
            $this->name = array_map(function ($n) {
                return (string) $n;
            }, $this->xml->xpath('//trk/name'));
        }
    }

    public function addTrack(Track $track): void
    {
        $this->tracks[] = $track;
    }

    public function xml(string $filename = null): bool|string
    {
        foreach ($this->tracks as $track) {
            foreach ($track->getPolylines() as $polyline) {
                $trk = $this->xml->addChild("trk");
                $trk->name = $track->name;
                $trkseg = $trk->addChild('trkseg');

                foreach ($polyline->getPoints() as $point) {
                    $trkpt = $trkseg->addChild('trkpt');
                    $trkpt->addAttribute("lat", $point->getLat());
                    $trkpt->addAttribute("lon", $point->getLng());
                }
            }
        }

        return $this->xml->asXML($filename);
    }

    public function isName(string $name): bool
    {
        return in_array($name, $this->name);
    }
}