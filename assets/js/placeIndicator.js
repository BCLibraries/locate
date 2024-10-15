import Shelf from "./Shelf";
import * as d3 from "d3";

let horizontalViewBox = '';
console.log('PLACING');

/**
 * Determine where the indicator should go and place it on the map
 *
 * @param data the data from the shelf data response
 */
function placeIndicator(shelfId) {
    // Check the shelf number against the data-up, data-down, data-left
    // and data-right attributes of the shelves. If the number matches,
    // paint the shelf.
    console.log(`placing at ${shelfId}`);
    ['up', 'down', 'left', 'right'].some(type => {
        const match = d3.select(`use[data-${type}="${shelfId}"]`);
        if (!match.empty()) {
            paintIndicator(match, shelfId);
            horizontalViewBox = d3.select(".map > svg").attr('viewBox');
            resizeMap();
            return true; // If we've matched, return true to break out of some() loop.
        }
    });
}

function findAndHighlightShelf(shelfId) {
    // Check the shelf number against the data-up, data-down, data-left
    // and data-right attributes of the shelves. If the number matches,
    // paint the shelf.
    ['up', 'down', 'left', 'right'].some(type => {
        const match = d3.select(`use[data-${type}="${shelfId}"]`);
        if (!match.empty()) {
            const shelf = new Shelf(match);
            shelf.highlight(shelfId);
            horizontalViewBox = d3.select(".map > svg").attr('viewBox');
            resizeMap();
            return true; // If we've matched, return true to break out of some() loop.
        }
    });
}

/**
 * Actually paint the indicator
 *
 * @param matchingNode
 * @param {string} shelfId
 */
function paintIndicator(matchingNode, shelfId) {
    // Get the point to center the indicator at.
    const shelf = new Shelf(matchingNode);
    const indicatorCoords = shelf.findMarkerPoint(shelfId);

    // Draw the indicator.
    d3.select("#map svg").append('use')
        .attr('xlink:href', '#shelf-map__map-pin')
        .attr('x', indicatorCoords.x)
        .attr('y', indicatorCoords.y);
}

// When the window resizes we need to focus the SVG to either the left or right depending on
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

export {placeIndicator, findAndHighlightShelf};
