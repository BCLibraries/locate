import '../css/app.scss';
import spinnerPath from '../images/loader-img.svg';
import Router from "./Router";
import {placeIndicator, findAndHighlightShelf} from "./placeIndicator";

require('bootstrap');

/**
 * Route function for highlighting a single shelf
 *
 * This route is used by editors when they are editing a shelf list. The shelf lists are
 * keyed by shelf ID, and the editors use these links to see the shelf on the map.
 *
 * @param {Router} router
 */
function highlightShelf(router) {
    findAndHighlightShelf(router.shelf.toString())
}

window.addEventListener("DOMContentLoaded", function () {
    const body = document.querySelector('body');
    const emailRoute = body.getAttribute('data-email-route').replaceAll('\/', '');
    const shelfRoute = body.getAttribute('data-shelf-route').replaceAll('\/', '');

    // Die early if no map has been loaded.
    const mapExists = document.querySelector(".map > svg");
    if (!mapExists) {
        throw new Error("No map found");
    }

    // Figure out what route has been called and run it.
    const router = new Router({
        'map': locateItem,
        'shelf': highlightShelf
    });
    router.execute();


    /**
     * Route function for locating item on shelf
     *
     * This is the main function that is called when a user is looking for the
     * location of a book on the shelf.
     *
     * @param {Router} router
     */
    function locateItem(router) {
        if (!router.library && router.callNumber) {
            throw new Error("Could not find library ID or call number");
        }

        loadShelfData(router.library, router.callNumber).then((data) => {
            placeIndicator(data.shelf.id.toString());
        });
    }

    /**
     * AJAX request for shelf data
     *
     * @param {string} library
     * @param {string} callNumber
     * @return {Promise<any>}
     */
    async function loadShelfData(library, callNumber) {
        console.log(shelfRoute);
        const response = await fetch(`${shelfRoute}?lib=${library}&callno=${callNumber}`);
        return await response.json();
    }

    function sendData() {
        const XHR = new XMLHttpRequest();

        // Bind the FormData object and the form element
        const FD = new FormData(form);

        // Define what happens on successful data submission
        XHR.addEventListener("load", function (event) {
            setModalMessage("Your request has been sent.");
        });

        // Define what happens in case of error
        XHR.addEventListener("error", function (event) {
            setModalMessage("There was an error processing your request.");
        });

        // Set up our request
        XHR.open("POST", emailRoute);

        // The data sent is what the user provided in the form
        XHR.send(FD);

        showLoadingSpinner();
    }

    // Access the form element...
    const form = document.querySelector(".sms-form");

    // ...and take over its submit event.
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        sendData();
    });
});

function setModalMessage(message) {
    const modalBody = document.querySelector('.modal-body');
    modalBody.innerHTML = `<div>${message}</div>`;
}

function showLoadingSpinner() {
    setModalMessage(`<div class="sms-form__loading-container"><img class="sms-form__loading-spinner" src="${spinnerPath}" alt="Loading"></div>`);
}