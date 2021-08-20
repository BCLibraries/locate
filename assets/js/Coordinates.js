class Coordinates {
    #x;
    #y;

    constructor(x, y) {
        this.#x = parseInt(x);
        this.#y = parseInt(y);
    }

    get x() {
        return this.#x;
    }

    get y() {
        return this.#y
    }

    /**
     * Add two coordinates
     *
     * @param {Coordinates} offset
     */
    addOffset(offset) {
        return new Coordinates(this.x + offset.x, this.y + offset.y);
    }
}

export default Coordinates;