<?php

namespace App\Importer;

use App\Entity\MapImage;
use App\Entity\BongoBoo;
use App\Entity\Shelf;
use Doctrine\ORM\EntityManagerInterface;

class ShelflistImporter
{
    /** @var ShelflistFileValidator */
    private $file_validator;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(ShelflistFileValidator $file_validator, EntityManagerInterface $em)
    {
        $this->file_validator = $file_validator;
        $this->em = $em;
    }

    public function import(string $shelflist, MapImage $map, ?string $mapfile): ImportReport
    {
        $report = new ImportReport();
        $this->file_validator->validate($shelflist, $mapfile);

        $handle = fopen($shelflist, 'rb');

        while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
            $shelf = new Shelf();
            $shelf->setMapImage($map);
            $num = count($data);
            echo "<p> $num fields in line $row: <br /></p>\n";
            $row++;
            foreach ($data as $cValue) {
                echo $cValue . "<br />\n";
            }
        }
        fclose($handle);

        return $report;
    }
}