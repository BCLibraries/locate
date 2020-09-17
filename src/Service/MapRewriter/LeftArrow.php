<?php


namespace App\Service\MapRewriter;


class LeftArrow implements ArrowInterface
{
    public function getSymbolId(): string
    {
        return 'shelf-map__arrow-left';
    }

    public function getDataAttribute(): string
    {
        return 'data-right';
    }

    public function getXOffset(float $width, float $shelf_center_point, float $shelf_thickness): float
    {
        return $shelf_thickness + ($shelf_thickness / 2);
    }

    public function getYOffset(float $height, float $shelf_center_point, float $shelf_thickness): float
    {
        return $shelf_center_point - $height / 2;
    }
}