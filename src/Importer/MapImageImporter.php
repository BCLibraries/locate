<?php

namespace App\Importer;

use App\Entity\Map;
use App\Entity\MapImage;
use App\Exception\BadFileException;
use App\Service\MapImageFileLocator;

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

    /** @var string */
    private $project_base;

    /** @var string */
    private $map_image_dir;

    /** @var MapImageFileLocator */
    private $locator;

    /**
     * MapImageImporter constructor.
     *
     * @param MapImageFileValidator $validator
     * @param MapImageFileLocator $locator
     */
    public function __construct(
        MapImageFileValidator $validator,
        MapImageFileLocator $locator
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

        // Get info about file.
        $original_filename = basename($filepath);
        [$width, $height, $file_type_code] = getimagesize($filepath);

        // Set file metadata.
        $new_file_name = $this->buildNewFileName($map_code, $file_type_code);
        $map_image->setFilename($new_file_name);
        $map_image->setOriginalFilename($original_filename);
        $map_image->setHeight($height);
        $map_image->setWidth($width);

        // Try to build the new file.
        $new_filepath = $this->locator->getFilePath($map_image);
        $copy_succeeded = copy($filepath, $new_filepath);
        if (!$copy_succeeded) {
            throw new \RuntimeException("Failed to copy $filepath to $new_filepath");
        }

        return $map_image;
    }

    /**
     * Build a standardized image file names
     *
     * Map images files have a standardized filename based on the map code and
     * a random string for hash busting.
     *
     * @param string $map_code
     * @param int $file_type_code PHP image file type constant from getimagesize()
     * @return string the new file name
     * @throws \Exception
     */
    private function buildNewFileName(string $map_code, int $file_type_code): string
    {
        $date_string = date('YmdHi');

        $random_hex = bin2hex(random_bytes(4));
        $random_string = base_convert($random_hex, 16, 36);

        $extension = $this->getFileExtension($file_type_code);

        return "$map_code-$date_string-$random_string.$extension";
    }

    /**
     * Map PHP file type codes to preferred file extension
     *
     * @param int $type_code PHP image file type constant from getimagesize()
     * @return string
     */
    private function getFileExtension(int $type_code): string
    {
        $code_to_extension = [
            1 => 'gif',
            2 => 'jpg',
            3 => 'png',
            4 => 'swf',
            5 => 'psd',
            6 => 'bmp',
            7 => 'tiff',
            8 => 'tiff',
            9 => 'jpc',
            10 => 'jp2',
            11 => 'jpx',
            12 => 'jb2',
            13 => 'swc',
            14 => 'iff',
            15 => 'wbmp',
            16 => 'xbm'
        ];

        if (!isset($code_to_extension[$type_code])) {
            throw new BadFileException('Could not find file type information in map image file');
        }

        return $code_to_extension[$type_code];
    }
}