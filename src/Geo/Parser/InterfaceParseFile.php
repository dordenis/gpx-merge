<?php

namespace Geo\Parser;

use Geo\Track;
use Location\Processor\Polyline\SimplifyInterface;

interface InterfaceParseFile
{
    public function trackFile(string $file): InterfaceParseFile;
    public function simplify(SimplifyInterface $simplify): InterfaceParseFile;
    public function getTrack(): Track;
}