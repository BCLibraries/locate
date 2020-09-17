<?php

namespace App\Service\MapRewriter;

class ShelfNode
{
    /** @var float */
    private $thickness;

    /** @var Coordinates */
    private $coordinates;

    /** @var float */
    private $center_point;

    /** @var \SimpleXMLElement */
    private $use_node;

    /**
     * @param \SimpleXMLElement $use_node the <use> element for this shelf
     */
    public function __construct(\SimpleXMLElement $use_node)
    {
        $this->use_node = $use_node;
        $this->use_node->registerXPathNamespace(SvgNamespaces::SVG_ALIAS, SvgNamespaces::SVG_URI);
        $this->use_node->registerXPathNamespace(SvgNamespaces::XLINK_ALIAS, SvgNamespaces::XLINK_URI);
    }

    /**
     * @return \SimpleXMLElement the shelf's parent element
     */
    public function getParent(): \SimpleXMLElement
    {
        return $this->use_node->xpath('..')[0];
    }

    /**
     * @return Coordinates the shelf's XY coords
     */
    public function getCoordinates(): Coordinates
    {
        // If we've already determined the coords, send them.
        if ($this->coordinates !== null) {
            return $this->coordinates;
        }

        // Coordinates are stored in attributes. Be explicit about 0-values.
        $attributes = $this->use_node->attributes();
        $x = (float)$attributes->x ?: 0;
        $y = (float)$attributes->y ?: 0;
        $this->coordinates = new Coordinates($x, $y);

        return $this->coordinates;
    }

    /**
     * @return float the center point of the shelf
     */
    public function getCenterPoint(): float
    {
        // If we've haven't determined the center point, load it.
        if ($this->center_point === null) {
            $this->readSymbolDataAttributes();
        }
        return $this->center_point;
    }

    /**
     * @return float the "thickness" of the shelf <symbol> (width for vertical shelves, height for horizontal ones)
     */
    public function getThickness(): float
    {
        // If we've haven't determined the thickness, load it.
        if ($this->thickness === null) {
            $this->readSymbolDataAttributes();
        }
        return $this->thickness;
    }

    private function readSymbolDataAttributes(): void
    {
        // Look up the shelf <symbol> referred to by the shelf instance's <use>.
        $symbol_id = $this->use_node->attributes(SvgNamespaces::NAMESPACES['xlink'])->href;
        $symbol_id = ltrim($symbol_id, '#');
        $symbol_xpath = "/svg:svg/svg:defs/svg:symbol[@id='$symbol_id']";
        $symbol_node = $this->use_node->xpath($symbol_xpath)[0];

        // Read the symbol attributes into instance variables.
        $attributes = $symbol_node->attributes();
        $this->center_point = (float)$attributes->{'data-centerpoint'};
        $this->thickness = $attributes->{'data-thickness'} ? (float)$attributes->{'data-thickness'} : 0;
    }
}