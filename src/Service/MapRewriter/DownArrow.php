<?php

namespace App\Service\MapRewriter;

class DownArrow extends Arrow
{
    public function getDataAttribute(): string
    {
        return 'data-up';
    }

    public function getXOffset(float $width, float $shelf_center_point, float $shelf_thickness): float
    {
        return $shelf_center_point - $width/2;
    }

    public function getYOffset(float $height, float $shelf_center_point, float $shelf_thickness): float
    {
        return -$height - ($shelf_thickness / 5);
    }
}