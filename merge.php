<?php

include_once "vendor/autoload.php";

use \Location\Processor\Polyline\SimplifyDouglasPeucker;
use \Geo\Create\CreateGpx;
use \Geo\Parser\ParserFile;

$file = new File();

$gpx = new CreateGpx();

// папка где лежат gpx файлы
$files = $file->getFiles("folder/gpx/");

$total = count($files);
$n = 0;

foreach ($files as $file) {
    $track = ParserFile::factory($file)->simplify(new SimplifyDouglasPeucker(7))->getTrack();
    $track->name = $file->getFilename();

    echo ++$n."/{$total}\t{$file->getPathname()}\t{$track->getLength()}\n";

    $gpx->addTrack($track);
}

// название итогового файла
$gpx->xml("merge.gpx");