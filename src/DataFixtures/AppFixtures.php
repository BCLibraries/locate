<?php

namespace App\DataFixtures;

use App\Entity\Library;
use App\Entity\Map;
use App\Entity\MapImage;
use App\Entity\Shelf;
use App\Service\CallNoNormalizer\LCNormalizer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Load fixture data for testing
 *
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    /** @var LCNormalizer */
    private $lc_normalizer;

    public function __construct(LCNormalizer $lc_normalizer)
    {
        $this->lc_normalizer = $lc_normalizer;
    }

    /**
     * Load test data
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $oneill = new Library('onl', "O'Neill Library");
        $manager->persist($oneill);

        $onl_level_5 = $this->buildMap($manager, $oneill, 'onl-floor-5', 'floor-5', 'Level 5');
        $onl_lvl_4_south = $this->buildMap($manager, $oneill, 'onl-floor-4-south', 'floor-4-south', 'Level 4 South');
        $onl_lvl_4_north = $this->buildMap($manager, $oneill, 'onl-floor-4-north', 'floor-4-north', 'Level 4 North');
        $old_lvl_3 = $this->buildMap($manager, $oneill, 'onl-floor-3', 'floor-3', 'Level 3');

        $law = new Library('law', 'Law School Library');
        $manager->persist($law);

        $law_level_3 = $this->buildMap($manager, $law, 'law-floor-3', 'floor-3', 'Level 3');
        $law_level_4 = $this->buildMap($manager, $law, 'law-floor-4', 'floor-4', 'Level 4');

        $manager->persist($law_level_3);
        $manager->persist($law_level_4);

        $this->buildShelves($manager, $onl_level_5, $this->loadOneillShelfData());
        $this->buildShelves($manager, $law_level_4, $this->loadLawShelfData());

        $manager->flush();
    }


    private function buildShelves(ObjectManager $manager, Map $map, array $shelves_array): void
    {
        foreach ($shelves_array as $shelf) {
            $this->buildShelf($manager, $map, $shelf[0], $shelf[1], $shelf[2]);
        }
    }

    private function buildShelf(ObjectManager $manager, Map $map, $code, string $start_callno, string $end_callno)
    {
        $normalized_start_callno = $this->lc_normalizer->normalize($start_callno);
        $normalized_end_callno = $this->lc_normalizer->normalize($end_callno);
        $shelf = new Shelf($map, $code, $start_callno, $normalized_start_callno, $end_callno, $normalized_end_callno);
        $manager->persist($shelf);
    }

    private function buildMap(ObjectManager $manager, $library, string $filename, string $code, string $label): Map
    {
        $map = new Map($library, $code, $label, $filename);
        $manager->persist($map);
        return $map;
    }

    private function loadOneillShelfData(): array
    {
        $regular_shelves = [
            ['52', 'BJ45.K46 2001', 'BJ1188.5.R57'],
            ['53', 'BJ1188.5.S65A', 'BJ1461.O27'],
            ['54', 'BJ1461.O35', 'BL1.T39'],
            ['55', 'BL1.T39', 'BL51.W444'],
            ['56', 'BL51.W46 2010', 'BL240.2.P5754']
        ];

        $vertical_shelves = [
            ['61', 'BM535.H32 1990', 'BM660.E928 2011']
        ];

        $strange_shape_shelves = [
            ['67', 'BR5.S65', 'BR60.C6'],
            ['128', 'D1.P37', 'D13.A6'],
            ['129', 'D13.A64 1983', 'D51.H5'],
            ['140', 'D790.A97 1997', 'D804.3.S599'],
            ['141', 'D804.3.S63 1992', 'D810.J4 H6433 1981']
        ];

        return array_merge($regular_shelves, $vertical_shelves, $strange_shape_shelves);
    }

    private function loadLawShelfData(): array
    {
        return [
            ['11A', 'KF 135 .0 .P2 P312', 'KF 135 .0 .P21 P322'],
            ['11B', 'KF 135 .0 .P21 P323', 'KF 135 .0 .S61 S622'],
            ['12A', 'KF 135 .0 .S61 S622', 'KF 135 .0 .S7 S612'],
            ['12B', 'KF 135 .0 .S7 S612', 'KF 135 .0 .S8 S612'],
            ['13A', 'KF 135 .0 .S8 S612', 'KF 141 .0 .A363'],
            ['13B', 'KF 141 .0 .A364 ', 'KF 141 .0 .W474'],
            ['14A', 'KF 148 .0 .R363 ', 'KF 154 .0 .R84'],
            ['14B', 'KF 154 .0 .R84 ', 'KF 170 .0 .N5'],
            ['21A', 'KF 1325 .0 .C58 S36', 'KF 1428 .0 .A75 A73'],
            ['21B', 'KF 1428 .0 .A75 T48', 'KF 1444 .0 .A55'],
            ['22A', 'KF 1444 .0 .A55 ', 'KF 1515 .0 .A2 W47'],
            ['22B', 'KF 1515 .0 .A2 W47', 'KF 1611 .0 .A2 F6'],
            ['23A', 'KF 1611 .0 .A2 F6', 'KF 2085 .0 .A2 F8'],
            ['23B', 'KF 2085 .0 .A2 F8', 'KF 2172 .0 .A2 I58'],
            ['24A', 'KF 2172 .0 .A2 I58', 'KF 2763 .3 .A2 F44'],
            ['24B', 'KF 2763 .5 .L48 ', 'KF 3114 .0 .C47'],
            ['25A', 'KF 3114 .0 .C47 ', 'KF 3144 .0 .C47'],
            ['25B', 'KF 3319 .0 .L325 ', 'KF 3372 .0 .A55'],
        ];
    }
}
