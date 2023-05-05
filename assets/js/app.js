/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import '../css/app.scss';

// Load spinner icon.
import spinnerPath from '../images/loader-img.svg';

import * as d3 from 'd3';
import Shelf from "./Shelf";
import shelf from "./Shelf";

require('bootstrap');

let horizontalViewBox = '';

window.addEventListener('load', function () {
    // Load the data for the requested item and place the indicator on the map.
    const [libraryCode, callNumber] = window.location.pathname.split('/').slice(-2);
    const mapExists = document.querySelector(".map > svg");

    // If we have all the parts we need, fetch the shelf and add it to the map.
    if (libraryCode && callNumber && mapExists) {
        loadShelfData(libraryCode, callNumber).then(placeIndicator);
        horizontalViewBox = d3.select(".map > svg").attr('viewBox');
        resizeMap();
    }
});

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

/**
 * Determine where the indicator should go and place it on the map
 *
 * @param data the data from the shelf data response
 */
function placeIndicator(data) {
    const shelfID = data.shelf.id.toString();
    // Check the shelf number against the data-up, data-down, data-left
    // and data-right attributes of the shelves. If the number matches,
    // paint the shelf.
    ['up', 'down', 'left', 'right'].some(type => {
        console.log(`Looking for ${shelfID}`);
        console.log(`use[data-${type}="${shelfID}"]`);
        const match = d3.select(`use[data-${type}="${shelfID}"]`);
        console.log(match);
        if (!match.empty()) {
            console.log('will paint indicator');
            paintIndicator(match, shelfID);
            return true; // If we've matched, return true to break out of some() loop.
        } else {
            console.log('match is empty');
        }
    });
}

/**
 * Actually paint the indicator
 *
 * @param matchingNode
 */
function paintIndicator(matchingNode, shelfId) {

    console.log(`painting indicator for ${shelfId}`);

    // Get the point to center the indicator at.
    const shelf = new Shelf(matchingNode);
    const indicatorCoords = shelf.findMarkerPoint(shelfId);

    // Draw the indicator.
    d3.select("#map svg").append('use')
        .attr('xlink:href', '#shelf-map__map-pin')
        .attr('x', indicatorCoords.x)
        .attr('y', indicatorCoords.y);
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

// When the window resizes we need to focus the SVG to either the left or right depenind on
// where the relevant shelf is. We do the refocusing by replacing the regular viewbox
// attribute of the SVG with its data-viewbox.
function resizeMap() {
    if (window.innerWidth < 992) {
        const vertViewBox = document.querySelector(".map > svg").getAttribute('data-viewbox');
        d3.select(".map > svg").attr('viewBox', vertViewBox);
    } else {
        d3.select(".map > svg").attr('viewBox', horizontalViewBox);
    }
}

window.addEventListener("resize", resizeMap);
