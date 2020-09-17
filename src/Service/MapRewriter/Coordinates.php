<?php

namespace App\Service\MapRewriter;

class Coordinates
{
    /** @var float */
    private $x;

    /** @var float */
    private $y;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return float x coordinate
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * @return float y coordinate
     */
    public function getY(): float
    {
        return $this->y;
    }
}