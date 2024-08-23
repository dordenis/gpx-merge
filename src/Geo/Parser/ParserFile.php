<?php

namespace Geo\Parser;

class ParserFile
{

    static function factory(\SplFileInfo $file): InterfaceParseFile
    {
        $name = $file->getPathname();
        $ext = $file->getExtension();
        switch ($ext) {
            case "gpx":
                return (new ParserGpx())->trackFile($name);

            case "fit":
                return (new ParserFit())->trackFile($name);
        }

    }
}