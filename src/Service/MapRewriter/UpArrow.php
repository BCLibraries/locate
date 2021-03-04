<?php

namespace App\Service\MapRewriter;

class UpArrow extends Arrow
{
    public function getDataAttribute(): string
    {
        return 'data-down';
    }

    public function getXOffset(float $width, float $shelf_center_point, float $shelf_thickness): float
    {
        return $shelf_center_point - $width/2;
    }

    public function getYOffset(float $height, float $shelf_center_point, float $shelf_thickness): float
    {
        return -$height + 2 * $shelf_thickness;
    }
}