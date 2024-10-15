import * as d3 from 'd3';
import Coordinates from "./Coordinates";

const offsets = {
    down: new Coordinates(-33, -66),
    up: new Coordinates(-33, -79),
    left: new Coordinates(-25, -33),
    right: new Coordinates(-21, -73)
};

const orientations = {
    vertical: 'vertical',
    horizontal: 'horizontal'
};

class Shelf {

    // The <use> element for the shelf.
    #shelfElement;

    // The <symbol> element that the shelf refers to.
    #symbol;

    /**
     *
     * @param shelfElement the result of the d3 select statement that found the shelf
     */
    constructor(shelfElement) {
        this.#shelfElement = shelfElement;

        // <use> elements are connected to <symbol>s by xlink:href attributes that refer
        // to the <symbol>'s id attribute.
        const href = this.#shelfElement.attr('xlink:href');
        this.#symbol = d3.select(href);
    }

    get x() {
        return parseInt(this.#shelfElement.attr('x'));
    }

    get y() {
        return parseInt(this.#shelfElement.attr('y'));
    }

    get centerPoint() {
        const centerPoint = parseInt(this.#symbol.attr('data-centerpoint'));
        if (this.orientation === orientations.vertical) {
            return new Coordinates(this.x, this.y + centerPoint);
        } else {
            return new Coordinates(this.x + centerPoint, this.y);
        }
    }

    get orientation() {
        return this.#symbol.attr('id').includes('vertical') ? orientations.vertical : orientations.horizontal;
    }

    get symbol() {
        return this.#symbol;
    }

    /**
     * Draw a yellow box over the selected shelf
     *
     * @param {string} shelfID
     */
    highlight(shelfID) {
        // Get the shelf dimensions from the bounding box of the <use>
        // element.
        const box = this.#shelfElement.node().getBBox();
        const dimensions = {
            x: box.x,
            y: box.y,
            width: box.width,
            height: box.height
        }

        // Change the dimensions to match the side of the shelving unit
        // indicated by the ID.
        if (this.#shelfElement.attr('data-up') === shelfID) {
            dimensions.height = dimensions.height/2;
        } else if (this.#shelfElement.attr('data-down') === shelfID) {
            dimensions.height = dimensions.height/2;
            dimensions.y = dimensions.y + dimensions.height;
        } else if (this.#shelfElement.attr('data-left') === shelfID) {
            dimensions.width = dimensions.width/2;
        } else if (this.#shelfElement.attr('data-right') === shelfID) {
            dimensions.width = dimensions.width/2;
            dimensions.x = dimensions.x + dimensions.width;
        }

        // Draw the highlighted box.
        d3.select("#shelf-map__visible-map").append('rect')
            .attr('x', dimensions.x)
            .attr('y', dimensions.y)
            .attr('height', dimensions.height)
            .attr('width', dimensions.width)
            .attr('stroke', 'black')
            .attr('fill', '#FFFF00')
            .attr('stroke-width', '2');
    }

    /**
     * Get the coordinates where a marker for this shelf should be added
     *
     * @param {string} shelfID
     * @returns {Coordinates}
     */
    findMarkerPoint(shelfID) {
        const offset = this.#findOffset(shelfID);
        const centerPoint = this.centerPoint;
        return centerPoint.addOffset(offset);
    }

    /**
     * Find the offset from the shelf <use> element for the marker
     *
     * The marker should not be placed directly on the shelf, but in the aisle facing
     * the shelf. To do that, we need the orientation of the shelf.
     *
     * @param {string} shelfID
     * @returns {Coordinates}
     */
    #findOffset(shelfID) {
        shelfID = shelfID.replace(/[A-Z]/,'');
        if (this.#shelfElement.attr('data-up') === shelfID) {
            return offsets.up;
        } else if (this.#shelfElement.attr('data-down') === shelfID) {
            return offsets.down;
        } else if (this.#shelfElement.attr('data-left') === shelfID) {
            return offsets.left;
        } else {
            return offsets.right;
        }
    }
}

export default Shelf;
