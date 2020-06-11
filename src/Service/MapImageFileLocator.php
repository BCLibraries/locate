<?php

namespace App\Service;

use App\Entity\MapImage;

class MapImageFileLocator
{
    /** @var string */
    private $project_dir;

    /** @var string */
    private $map_image_dir;

    public function __construct(string $project_dir, string $map_image_dir)
    {
        $this->project_dir = $project_dir;
        $this->map_image_dir = $map_image_dir;
    }

    public function getFilePath(MapImage $image)
    {
        return "{$this->project_dir}/{$this->map_image_dir}/{$image->getFilename()}";
    }
}