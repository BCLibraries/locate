import '../css/app.scss';
import spinnerPath from '../images/loader-img.svg';
import Router from "./Router";
import {placeIndicator, findAndHighlightShelf} from "./placeIndicator";

require('bootstrap');

window.addEventListener('load', function () {

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
});

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

/**
 * AJAX request for shelf data
 *
 * @param {string} library
 * @param {string} callNumber
 * @return {Promise<any>}
 */
async function loadShelfData(library, callNumber) {
    const response = await fetch(`${shelfRoute}?lib=${library}&callno=${callNumber}`);
    return await response.json();
}


window.addEventListener("load", function () {
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
        XHR.open("POST", smsRoute);

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

/**  
 * Function and event listeners for SMS input area auto-formatting
 * 
 * @param {e}
 */


window.addEventListener("load", function () {
    const telFormatter = (e) => { 
        // If the pressed key was a number or backspacer...
        if (parseInt(e.key) >= 0 || e.key === "Backspace" || e.key === "Delete") {

            // Take the current input value and remove stuff that isn't numbers...
            let input = document.getElementById('phone');   
            let formatted = input.value.replace(/\D/g,'');

            // Reformat these into (123) 456-7890 depending on length...
            if (formatted.length > 0) {
                let newForm = '(' + formatted.substring(0,3);
                if (formatted.length >= 4) {
                    newForm = newForm + ') ' + formatted.substring(3,6);
                }
                if (formatted.length >= 7) {
                    newForm = newForm + '-' + formatted.substring(6,10); 
                }

                // ...And put this new formatted string into the input value.
                input.value = newForm;
            }
        }
    }

    document.getElementById('phone').addEventListener('keyup',telFormatter);
    document.getElementById('phone').addEventListener('keydown',telFormatter);
});