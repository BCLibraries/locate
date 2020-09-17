<?php

namespace App\Service\MapRewriter;

abstract class Arrow implements ArrowInterface
{
    /**
     * @return string id of the arrow <symbol> element in the map SVG
     */
    abstract public function getSymbolId(): string;

    /**
     * @return string data attribute that holds the shelf number that this arrow corresponds to
     */
    abstract public function getDataAttribute(): string;

    /**
     * @return int number of degrees the arrow <symbol> should be rotated from
     */
    abstract public function getRotation(): int;


}