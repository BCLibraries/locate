<?php

namespace App\Service\MapRewriter;

class RightArrow implements ArrowInterface
{
    public function getSymbolId(): string
    {
        return 'shelf-map__arrow-right';
    }

    public function getDataAttribute(): string
    {
        return 'data-left';
    }

    public function getXOffset(float $width, float $shelf_center_point, float $shelf_thickness): float
    {
        return -$width - ($shelf_thickness / 5);
    }

    public function getYOffset(float $height, float $shelf_center_point, float $shelf_thickness): float
    {
        return $shelf_center_point - $height / 2;
    }
}