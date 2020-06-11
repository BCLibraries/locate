<?php

namespace App\Importer;

use App\Exception\BadFileException;

class MapImageFileValidator extends FileValidator
{
    private const VALID_MIME_TYPES = ['image/jpeg', 'image/png'];

    /**
     * @param string $file_under_test
     * @throws BadFileException
     */
    public function validate(string $file_under_test): void
    {
        if (!$this->isValidMap($file_under_test)) {
            throw new BadFileException("$file_under_test is not a valid map image file");
        }
    }

    /**
     * @param string $map_file
     * @return bool
     */
    private function isValidMap(string $map_file): bool
    {
        return file_exists($map_file) && $this->fileHasFormat($map_file, self::VALID_MIME_TYPES);
    }
}