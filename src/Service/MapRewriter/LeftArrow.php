<?php


namespace App\Service\MapRewriter;


class LeftArrow extends Arrow
{
    public function getDataAttribute(): string
    {
        return 'data-right';
    }

    public function getXOffset(float $width, float $shelf_center_point, float $shelf_thickness): float
    {
        return $shelf_thickness - $width/2;
    }

    public function getYOffset(float $height, float $shelf_center_point, float $shelf_thickness): float
    {
        return -$shelf_center_point;
    }
}