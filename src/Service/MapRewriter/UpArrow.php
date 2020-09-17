<?php

namespace App\Service\MapRewriter;

class UpArrow implements ArrowInterface
{
    public function getSymbolId(): string
    {
        return 'shelf-map__arrow-up';
    }

    public function getDataAttribute(): string
    {
        return 'data-down';
    }

    public function getXOffset(float $width, float $shelf_center_point, float $shelf_thickness): float
    {
        return $shelf_center_point - $width / 2;
    }

    public function getYOffset(float $height, float $shelf_center_point, float $shelf_thickness): float
    {
        return  $shelf_thickness + ($shelf_thickness / 2);
    }
}