<?php

use App\Service\CallNoNormalizer\LCNormalizer;

require_once __DIR__ . '/vendor/autoload.php';

$input_file = __DIR__ . '/data/floor-3.csv';
$out_file = __DIR__ . '/data/floor-3-final.csv';

$out_fh = fopen($out_file, 'w');

$row = 0;
if (($in_fh = fopen($input_file, 'r')) !== FALSE) {
    while (($data = fgetcsv($in_fh, 1000, ',')) !== FALSE) {
        // Skip first row.
        $row++;
        if ($row === 1) {
            continue;
        }

        $normalizer = new LCNormalizer();
        $start_call_number = $normalizer->normalize("{$data[1]} {$data[2]}");
        $end_call_number = $normalizer->normalize("$data[3] $data[4]");

        $location_parts = explode(' ', $data[0]);
        $shelf = array_pop($location_parts);

        fputcsv($out_fh, [$shelf, "{$data[1]} {$data[2]}", "$data[3] $data[4]"]);

    }
    fclose($in_fh);
}

fclose($out_fh);
