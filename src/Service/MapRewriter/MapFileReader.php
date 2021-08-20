<?php

namespace App\Service\MapRewriter;

use App\Entity\Map;

class MapFileReader
{
    /** @var string */
    private string $maps_dir;

    public function __construct(string $maps_dir = __DIR__ . '/../../../maps/')
    {
        $this->maps_dir = $maps_dir;
    }

    public function readSvg(Map $map): \SimpleXMLElement
    {
        $library_subdir = $map->getLibrary()->getCode();
        $full_file_path = "{$this->maps_dir}/$library_subdir/{$map->getFilename()}.svg";

        $svg = simplexml_load_string(file_get_contents($full_file_path));
        $svg->registerXPathNamespace(SvgNamespaces::SVG_ALIAS, SvgNamespaces::SVG_URI);
        $svg->registerXPathNamespace(SvgNamespaces::XLINK_ALIAS, SvgNamespaces::XLINK_URI);
        return $svg;
    }
}