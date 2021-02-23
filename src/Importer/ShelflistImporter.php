<?php

namespace App\Importer;

use App\Entity\Map;
use App\Entity\MapImage;
use App\Entity\BongoBoo;
use App\Entity\Shelf;
use App\Service\CallNoNormalizer\LCNormalizer;
use Doctrine\ORM\EntityManagerInterface;

class ShelflistImporter
{
    /** @var ShelflistFileValidator */
    private $file_validator;

    /** @var EntityManagerInterface */
    private $em;

    /** @var LCNormalizer */
    private $lc_normalizer;

    public function __construct(ShelflistFileValidator $file_validator, EntityManagerInterface $em, LCNormalizer $lc_normalizer)
    {
        $this->file_validator = $file_validator;
        $this->em = $em;
        $this->lc_normalizer = $lc_normalizer;
    }

    public function import(string $shelflist, Map $map, ?string $mapfile): ImportReport
    {
        $report = new ImportReport();
        $this->file_validator->validate($shelflist);

        $handle = fopen($shelflist, 'rb');

        while (($data = fgetcsv($handle, 1000, "\t")) !== false) {

            $code = $data[0];
            $start_call_no = $data[1];
            $end_call_no = $data[2];
            $normalized_start_callno = $this->lc_normalizer->normalize($start_call_no);
            $normalized_end_callno = $this->lc_normalizer->normalize($end_call_no);

            $shelf = new Shelf($map, $code, $start_call_no, $normalized_start_callno, $end_call_no, $normalized_end_callno);
            $this->em->persist($shelf);
        }
        fclose($handle);

        $this->em->flush();

        return $report;
    }
}