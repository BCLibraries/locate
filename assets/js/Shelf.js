import * as d3 from 'd3';
import Coordinates from "./Coordinates";

const offsets = {
    DOWN: new Coordinates(-66, -15),
    UP: new Coordinates(-66, 15),
    LEFT: new Coordinates(-15, -66),
    RIGHT: new Coordinates(-15, -66)
};

const orientations = {
    VERTICAL: 'vertical',
    HORIZONTAL: 'horizontal'
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
        if (this.orientation === orientations.VERTICAL) {
            return new Coordinates(this.x, this.y + centerPoint);
        } else {
            return new Coordinates(this.x + centerPoint, this.y);
        }
    }

    get orientation() {
        return this.#symbol.attr('id').includes('vertical') ? orientations.VERTICAL : orientations.HORIZONTAL;
    }

    get symbol() {
        return this.#symbol;
    }

    findMarkerPoint(shelfNumber) {
        const offset = this.#findOffset(shelfNumber);
        const centerPoint = this.centerPoint;
        return centerPoint.addOffset(offset);
    }

    #findOffset(shelfNumber) {
        if (this.#shelfElement.attr('data-up') == shelfNumber) {
            return offsets.UP;
        } else if (this.#shelfElement.attr('data-down') == shelfNumber) {
            return offsets.DOWN;
        } else if (this.#shelfElement.attr('data-left') == shelfNumber) {
            return offsets.LEFT;
        } else {
            return offsets.RIGHT;
        }
    }

}

export default Shelf;