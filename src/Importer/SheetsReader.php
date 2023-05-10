<?php

namespace App\Importer;

class SheetsReader
{
    private \Google_Client $client;

    private const CONFIGURATION_JSON = __DIR__ . '/../../data/graceful-album-386115-3ed1dcf47826.json';

    private const BACKUP_DIR = __DIR__ . '/../../backups';

    private const SPREADSHEET_ID = '17WIVdhlPJFws7uRbJBPuBVdNBJFxl_DNpiNONaXefks';

    public function __construct()
    {
        $this->client = new \Google_Client();
    }

    /**
     * @throws \Google\Exception
     * @throws \Exception
     */
    public function read(): Spreadheet
    {
        $service = $this->connect();
        $rows = $service->spreadsheets_values->get(self::SPREADSHEET_ID, 'shelves')->getValues();
        $sheet = new Spreadheet($rows);
        if ($this->sheetHasNotBeenLoaded($sheet)) {
            $timestamp = date('Ymd-His');
            $backup_filename = "{$timestamp}-{$sheet->fingerprint()}.csv";
            file_put_contents(self::BACKUP_DIR . '/' . $backup_filename, $sheet->asCSV());
        }
        return $sheet;
    }

    /**
     * Connect to the Sheets API
     *
     * @return \Google_Service_Sheets
     * @throws \Google\Exception
     */
    private function connect(): \Google_Service_Sheets
    {
        $this->client->setApplicationName('Google Sheets API');
        $this->client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $this->client->setAccessType('offline');
        $this->client->setAuthConfig(self::CONFIGURATION_JSON);
        return new \Google_Service_Sheets($this->client);
    }


    /**
     * @throws \Exception
     */
    public function sheetHasNotBeenLoaded(Spreadheet $sheet): bool
    {
        $fingerprint = $sheet->fingerprint();
        foreach (scandir(self::BACKUP_DIR) as $file) {
            if (str_contains($file, $fingerprint)) {
                return false;
            }
        }
        return true;
    }
}
