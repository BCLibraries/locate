<?php

namespace App\Importer;

abstract class FileValidator
{
    abstract public function validate(string $file_under_test): void;

    /**
     * @param string $shelflist
     * @param string[] $mime_types
     * @return bool
     */
    protected function fileHasFormat(string $shelflist, array $mime_types): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mtype = finfo_file($finfo, $shelflist);
        finfo_close($finfo);
        return in_array($mtype, $mime_types, true);
    }
}