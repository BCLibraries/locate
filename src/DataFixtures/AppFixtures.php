<?php

namespace App\DataFixtures;

use App\Entity\Library;
use App\Entity\Map;
use App\Entity\MapImage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $oneill = $this->buildLibrary('onl', "O'Neill Library");
        $manager->persist($oneill);

        $onl_level_5 = $this->buildFloorMap($oneill, 'onl-floor-5', 'floor-5', 'Level 5');
        $onl_lvl_4_south = $this->buildFloorMap($oneill, 'onl-floor-4-south','floor-4-south', 'Level 4 South');
        $onl_lvl_4_north = $this->buildFloorMap($oneill, 'onl-floor-4-north','floor-4-north', 'Level 4 North');
        $old_lvl_3 = $this->buildFloorMap($oneill, 'onl-floor-3','floor-3', 'Level 3');

        $manager->persist($onl_level_5);
        $manager->persist($onl_lvl_4_south);
        $manager->persist($onl_lvl_4_north);
        $manager->persist($old_lvl_3);

        $law = $this->buildLibrary('law', 'Law School Library');
        $manager->persist($law);

        $law_level_3 = $this->buildFloorMap($law, 'law-floor-3', 'floor-3', 'Level 3');
        $law_level_4 = $this->buildFloorMap($law, 'law-floor-4', 'floor-4', 'Level 4');

        $manager->persist($law_level_3);
        $manager->persist($law_level_4);

        $manager->flush();
    }

    private function buildFloorMap(Library $library, string $filename, string $code, string $label)
    {
        $level_5_map_img = new MapImage();
        $level_5_map_img->setFilename($filename);
        $level_5_map_img->setOriginalFilename($filename);

        $level_5_map = new Map($library, $code, $label);
        $level_5_map->setImage($level_5_map_img);
        return $level_5_map;
    }

    /**
     * @return Library
     */
    private function buildLibrary(string $code, string $label): Library
    {
        $oneill = new Library();
        $oneill->setCode($code);
        $oneill->setLabel($label);
        return $oneill;
    }
}
