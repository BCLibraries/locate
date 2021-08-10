<?php

namespace App\Service\MapRewriter;

use App\Entity\Map;

class MapRewriter
{
    /** @var \SimpleXMLElement */
    private $svg;

    /** @var Map */
    private $map;

    /** @var string */
    private $stacks_xpath;

    /**
     * @param Map $file the map we're looking for
     * @param MapFileReader $svg_reader
     * @param string $stacks_xpath the XPath prefix for the stacks
     */
    public function __construct(Map $file, MapFileReader $svg_reader, string $stacks_xpath = '/')
    {
        $this->map = $file;
        $this->svg = $svg_reader->readSvg($this->map);
        $this->stacks_xpath = $stacks_xpath;
    }

    /**
     * Add an arrow to the SVG pointing to a shelf
     *
     * @param string $shelf the identifier for the shelf
     * @return string the body of the SVG with an arrow attached
     * @throws BadShelfQueryException
     */
    public function addArrow(string $shelf): string
    {
        /** @var Arrow[] $arrows */
        $arrows = [new DownArrow(), new UpArrow(), new LeftArrow(), new RightArrow()];

        // Scroll through each possible arrow and looking for matching shelf nodes.
        foreach ($arrows as $arrow) {
            $shelf_node = $this->getShelfNode($shelf, $arrow);

            // If a shelf node matches, add an arrow to it and return.
            if (isset($shelf_node)) {
                $this->paintArrow($shelf_node, $arrow);
                return $this->svg->asXML();
            }
        }

        // If we did not find a matching shelf, something went terribly wrong.
        throw new BadShelfQueryException("Could not find shelf $shelf in {$this->map->getFilename()}");
    }

    /**
     * Add a label node to the SVG
     *
     * @param string $text
     * @return string
     */
    public function addLabel(string $text): string
    {
        $group = $this->svg->addChild('g');
        $box = $group->addChild('rect');
        $box->addAttribute('width','300');
        $box->addAttribute('height', '100');
        $box->addAttribute('fill', '#000000');
        $text = $group->addChild('text',$text);
        $text->addAttribute('x',50);
        $text->addAttribute('y', 50);
        $text->addAttribute('font-family', 'Georgia');
        $text->addAttribute('fill', '#FFFFFF');
        $text->addAttribute('font-size', '55');
        $text->addAttribute('class', 'label');


        //$box = new \SimpleXMLElement('<rect width="300" height="100" style="fill:rgb(0,0,255);stroke-width:3;stroke:rgb(0,0,0)" />');
        return $this->svg->asXML();
    }

    /**
     * @param ShelfNode $shelf_node the shelf to point to
     * @param Arrow $arrow the kind of arrow we want
     * @throws BadShelfQueryException
     */
    private function paintArrow(ShelfNode $shelf_node, Arrow $arrow): void
    {
        // Get the shelf geometry.
        $coords = $shelf_node->getCoordinates();
        $center_point = $shelf_node->getCenterPoint();

        // Get the arrow node's dimensions.
        $symbol_id = $arrow->getSymbolId();
        $symbol_xpath = "/svg:svg/svg:defs/svg:symbol[@id='$symbol_id']";
        $arrow_node = $this->svg->xpath($symbol_xpath)[0];

        if (!$arrow_node) {
            throw new BadShelfQueryException("Can't find arrow <symbol> $symbol_id");
        }

        $arrow_attributes = $arrow_node->attributes();
        $arrow_width = (float)$arrow_attributes->{'data-width'};
        $arrow_height = (float)$arrow_attributes->{'data-height'};

        $x = $coords->getX() + $arrow->getXOffset($arrow_width, $center_point, $shelf_node->getThickness());
        $y = $coords->getY() + $arrow->getYOffset($arrow_height, $center_point, $shelf_node->getThickness());

        // Build the arrow XML element and append it to the map. Arrows have to go at the
        // end of the visible map or they might appear under later elements.
        $node = $this->getFullMapNode()->addChild('use');
        $node->addAttribute('x', $x);
        $node->addAttribute('y', $y);
        $node->addAttribute(SvgNamespaces::XLINK_ALIAS . ':href', "#$symbol_id", SvgNamespaces::XLINK_URI);
    }

    /**
     * Find the corresponding shelf to an ID
     *
     * Shelves are identified in the SVG by directional data- attributes (data-up, data-down,
     * etc). A single <use> element can have multiple shelves attached to it, e.g.:
     *
     *     <use id="#shelf-map__long-shelf" x="20" y="30" data-left="30" data-right="31" />
     *
     * would refer to a vertically-oriented rectangle whose left side refers to shelf 30 and
     * right side refers to shelf 31.
     *
     * @param string $shelf the shelf ID
     * @param Arrow $arrow the kind of arrow we are looking for
     * @return ShelfNode|null the matching shelf
     */
    private function getShelfNode(string $shelf, Arrow $arrow): ?ShelfNode
    {
        // Look for shelves with a data- attribute corresponding to the type of
        // arrow we're using.
        $xpath = "{$this->stacks_xpath}/svg:use[@{$arrow->getDataAttribute()}='$shelf']";
        $results = $this->svg->xpath($xpath);

        // If we find one, return it.
        return (count($results) > 0) ? new ShelfNode($results[0]) : null;
    }

    /**
     * @return \SimpleXMLElement the node containing the entire visible map
     */
    private function getFullMapNode(): \SimpleXMLElement
    {
        $xpath = "/svg:svg/svg:g[@id='shelf-map__visible-map']";
        $results = $this->svg->xpath($xpath);
        return $results[0];
    }
}