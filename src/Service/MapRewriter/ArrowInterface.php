<?php

namespace App\Service\MapRewriter;

interface ArrowInterface
{
    /**
     * @return string id of the arrow <symbol> element in the map SVG
     */
    public function getSymbolId(): string;

    /**
     * @return string data attribute that holds the shelf number that this arrow corresponds to
     */
    public function getDataAttribute(): string;

    /**
     * @param float $width the width of arrow <symbol>
     * @param float $shelf_center_point the center point of the shelf
     * @param float $shelf_thickness how "thick" the shelf is
     * @return float distance to offset arrow horizontally
     */
    public function getXOffset(float $width, float $shelf_center_point, float $shelf_thickness): float;

    /**
     * @param float $height the height of the arrow <symbol>
     * @param float $shelf_center_point the center point of the shelf
     * @param float $shelf_thickness how "thick" the shelf is
     * @return float distance to offset arrow vertically
     */
    public function getYOffset(float $height, float $shelf_center_point, float $shelf_thickness): float;
}