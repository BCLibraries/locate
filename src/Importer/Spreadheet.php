<?php

namespace App\Importer;

class Spreadheet
{
    /**
     * @var SpreadsheetRow[]
     */
    private array $rows;

    private string $csv_string;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /**
     * @throws \Exception
     */
    public function fingerprint(): string
    {
        return md5($this->asCSV());
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function asCSV(): string
    {
        // If we've already built the CSV string, use that.
        if (isset($this->csv_string)) {
            return $this->csv_string;
        }

        // Otherwise, write the CSV to an in-memory "file" and save as a string.
        $fp = fopen('php://temp', 'r+');
        foreach ($this->rows as $row) {
            fputcsv($fp, $row);
        }
        rewind($fp);
        $csv_string = fread($fp, 1048576);
        if ($csv_string === false) {
            throw new \Exception('Error generating CSV from Sheets data');
        }
        fclose($fp);
        $this->csv_string = $csv_string;
        return $csv_string;
    }

    /**
     * @return SpreadsheetRow[];
     */
    public function getRows(): array
    {
        return $this->rows;
    }
}
