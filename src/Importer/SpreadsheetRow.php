<?php

namespace App\Importer;

class SpreadsheetRow
{
    private int $id;
    private string $map;
    private string $code;
    private string $start_call_number;
    private string $end_call_number;

    public function __construct(array $csv_row)
    {
        $this->id = (int)$csv_row[0];
        $this->map = $csv_row[1];
        $this->code = $csv_row[2];
        $this->start_call_number = $csv_row[3];
        $this->end_call_number = $csv_row[4];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMap(): string
    {
        return $this->map;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getStartCallNumber(): string
    {
        return $this->start_call_number;
    }

    public function getEndCallNumber(): string
    {
        return $this->end_call_number;
    }


}
