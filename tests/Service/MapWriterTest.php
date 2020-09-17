<?php

namespace App\Tests;

use App\Entity\MapImage;
use App\Service\MapRewriter\MapRewriter;
use PHPUnit\Framework\TestCase;

class MapWriterTest extends TestCase
{
    const SAVE_FILE = '/Users/benjaminflorin/PhpstormProjects/locate/data/floor-5-with-auto-arrow.svg';

    public function testSomething()
    {
        $image = new MapImage();
        $image->setFilename('/Users/benjaminflorin/PhpstormProjects/locate/data/Floor5.svg');

        $rewriter = new MapRewriter($image);

        $svg = $rewriter->addArrow('83'); file_put_contents(self::SAVE_FILE, $svg);

        $this->assertTrue(true);
    }
}
