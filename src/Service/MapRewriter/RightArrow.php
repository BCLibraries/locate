<?php

namespace App\Service\MapRewriter;

class RightArrow extends Arrow
{
    public function getDataAttribute(): string
    {
        return 'data-left';
    }

    public function getXOffset(float $width, float $shelf_center_point, float $shelf_thickness): float
    {
        return -$width/2 - $shelf_thickness;
    }

    public function getYOffset(float $height, float $shelf_center_point, float $shelf_thickness): float
    {
        return -$shelf_center_point;
    }
}