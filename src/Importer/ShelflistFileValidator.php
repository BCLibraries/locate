<?php

namespace App\Importer;

use App\Exception\BadCommandArgumentException;

class ShelflistFileValidator extends FileValidator
{
    private const VALID_MIME_TYPES = ['text/plain'];

    public function validate(string $file_under_test): void
    {
        if (!$this->isValidShelflist($file_under_test)) {
            throw new BadCommandArgumentException("$shelflist is not a valid shelf list file");
        }
    }


    public function z(string $shelflist, ?string $mapfile = null): void
    {

    }

    /**
     * @param string $shelflist
     * @return bool
     */
    private function isValidShelflist(string $shelflist): bool
    {
        return file_exists($shelflist) && $this->fileHasFormat($shelflist, self::VALID_MIME_TYPES);
    }


}