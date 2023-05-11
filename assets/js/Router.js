class Router {
    #shelf;
    #callNumber;
    #library;
    #routeName;
    /**
     * @type {Object.<string, function>}
     */
    #routes;

    constructor(routes) {
        this.#routes = routes;
        const urlParts = window.location.pathname.split('/').slice(-3);
        this.#routeName = urlParts[0].toLowerCase();
        this.#library = urlParts[1].toLowerCase();
        if (this.#routeName === 'map') {
            this.#callNumber = urlParts[2];
        } else if (this.#routeName === 'shelf') {
            this.#shelf = urlParts[2];
        }

    }

    get library() {
        return this.#library;
    }

    /**
     * @returns {string}
     */
    get callNumber() {
        return this.#callNumber;
    }

    get shelf() {
        return this.#shelf;
    }

    execute() {
        this.#routes[this.#routeName](this);
    }

}

export default Router;
