<?php

namespace App\Importer;

use App\Entity\Map;
use App\Entity\MapImage;
use App\Exception\BadFileException;
use App\Service\MapFileReader;

/**
 * Imports map image files
 *
 * Map image files are validated, given new standardized file names, and copied into a main map
 * image directory. Files will have names like:
 *
 *     ONL_LVL3_WEST-87ixb1zfpdwkcog0wsk44ogs0.png
 *
 * Validation is handled in the validator. Exceptions are thrown on bad file types or bad input.
 *
 * @package App\Importer
 */
class MapImageImporter
{
    /** @var MapImageFileValidator */
    private $validator;

    /** @var MapFileReader */
    private $locator;

    /**
     * MapImageImporter constructor.
     *
     * @param MapImageFileValidator $validator
     * @param MapFileReader $locator
     */
    public function __construct(
        MapImageFileValidator $validator,
        MapFileReader $locator
    ) {
        $this->validator = $validator;
        $this->locator = $locator;
    }

    /**
     * Import a map image file
     *
     * @param string $filepath full path to the file
     * @param string $map_code the parent Map code
     * @throws \Exception
     * @throws BadFileException
     * @throws \RuntimeException
     */
    public function import(string $filepath, string $map_code): MapImage
    {
        $map_image = new MapImage();

        // Make sure it's a valid map image file.
        $this->validator->validate($filepath);

        // Set file metadata.
        $new_file_name = $this->buildNewFileName($map_code);
        $map_image->setFilename($new_file_name);

        return $map_image;
    }

    /**
     * Build a standardized image file names
     *
     * Map images files have a standardized filename based on the map code and
     * a random string for hash busting.
     *
     * @param string $map_code
     * @return string the new file name
     * @throws \Exception
     */
    private function buildNewFileName(string $map_code): string
    {
        $date_string = date('YmdHi');

        $random_hex = bin2hex(random_bytes(4));
        $random_string = base_convert($random_hex, 16, 36);

        return "$map_code-$date_string-$random_string.svg";
    }
}