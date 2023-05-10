<?php

namespace App\Importer;

class SheetsReader
{
    private \Google_Client $client;

    private const CONFIGURATION_JSON = __DIR__ . '/../../data/graceful-album-386115-3ed1dcf47826.json';

    private const SPREADSHEET_ID = '17WIVdhlPJFws7uRbJBPuBVdNBJFxl_DNpiNONaXefks';

    public function __construct()
    {
        $this->client = new \Google_Client();
    }

    /**
     * @throws \Google\Exception
     * @throws \Exception
     */
    public function read(): Spreadsheet
    {
        $service = $this->connect();
        $rows = $service->spreadsheets_values->get(self::SPREADSHEET_ID, 'shelves')->getValues();
        return new Spreadsheet($rows);
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
}
