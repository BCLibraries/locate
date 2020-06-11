<?php

namespace App\Importer;

class ImportReport
{
    private $success = false;
    private $imported_records = [];

    public function succeeded(): void
    {
        $this->success = true;
    }

    public function addRecord($record): void
    {
        $this->imported_records[] = $record;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function countRecords(): int
    {
        return count($this->imported_records);
    }

    public function getImportedRecords(): array
    {
        return $this->imported_records;
    }
}