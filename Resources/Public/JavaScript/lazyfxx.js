/**
 * lazyFxx: Lazy Loading Effects
 *
 * Using the image processing framework of TYPO3 to create placeholder images.
 *
 * @author
 *   brainworXX GmbH <info@brainworxx.de>
 *
 * @license
 *   http://opensource.org/licenses/LGPL-2.1
 *
 *   GNU Lesser General Public License Version 2.1
 *
 *   lazyFxx Copyright (C) 2017-2020 Brainworxx GmbH
 *
 *   This library is free software; you can redistribute it and/or modify it
 *   under the terms of the GNU Lesser General Public License as published by
 *   the Free Software Foundation; either version 2.1 of the License, or (at
 *   your option) any later version.
 *   This library is distributed in the hope that it will be useful, but WITHOUT
 *   ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 *   FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 *   for more details.
 *   You should have received a copy of the GNU Lesser General Public License
 *   along with this library; if not, write to the Free Software Foundation,
 *   Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */
(function () {
    "use strict";

    /**
     * Lazy loading of images, just because we can.
     *
     * @namespace lazyFxx
     *   Collection of js functions.
     */
    function lazyFxx() {}

    /**
     * Caching the images that we want to lazy load.
     *
     * @type {NodeList}
     */
    lazyFxx.images = {};

    /**
     * Caching of the source sets.
     *
     * @type {NodeList}
     */
    lazyFxx.sourceSets = {};

    /**
     * Registering everything as soon as the dom is ready.
     *
     * @event DOMContentLoaded
     */
    lazyFxx.onDocumentReady = function () {
        // 1. Get a list of all images
        lazyFxx.updateList();
        // 2. Test the list of elements
        window.setInterval(lazyFxx.checkList, 1000);
    };

    /**
     * Checking the visibility of the images every second.
     *
     * @event interval
     */
    lazyFxx.checkList = function () {
        var i;
        // Check on scroll, if any image is in viewport and trigger a lazy load.
        for (i = 0; i < lazyFxx.images.length; i++) {
            if (lazyFxx.isInViewport(lazyFxx.images[i])) {
                lazyFxx.lazyLoad(lazyFxx.images[i]);
            }
        }

        // And while we are at it, also check the source elements.
        for (i = 0; i < lazyFxx.sourceSets.length; i++) {
            if (lazyFxx.isInViewport(lazyFxx.sourceSets[i])) {
                lazyFxx.lazyLoadSourceSet(lazyFxx.sourceSets[i]);
            }
        }
    };

    /**
     * Update the list that we are watching.
     */
    lazyFxx.updateList = function () {
        lazyFxx.images = document.querySelectorAll('img.lazyload-placeholder');
        lazyFxx.sourceSets = document.querySelectorAll('picture source.lazyload-placeholder');
    };

    /**
     * Find out, if Element is in Viewport
     *
     * @param {Node} el
     */
    lazyFxx.isInViewport = function (el) {
        var rect = el.getBoundingClientRect();

        var atAllTop    = rect.top >= 0 && rect.left >= 0;
        var atAllBottom = rect.bottom >= 0 && rect.left >= 0;
        var bottom      = rect.bottom <= (window.innerHeight || document.documentElement.clientHeight);
        var right       = rect.right <= (window.innerWidth || document.documentElement.clientWidth);
        var top         = rect.top <= (window.innerHeight || document.documentElement.clientHeight);

        return (atAllTop && bottom && right) || (atAllBottom && top && right);
    };

    /**
     * Create a new element from the el, replace the src and show it.
     *
     * @param {Node} placeholder
     */
    lazyFxx.lazyLoad = function (placeholder) {
        // Create a new container around everything.
        var container = document.createElement('div');
        container.style['height'] = placeholder.offsetHeight + 'px';
        container.style['width'] = placeholder.offsetWidth + 'px';
        container.className += ' lazy-container';
        placeholder.parentNode.insertBefore(container, placeholder);

        // Copy the placeholder into the container
        container.appendChild(placeholder);

        // Create the original image.
        var original = placeholder.cloneNode(true);
        original.className += ' lazyload-original';

        // Add it to the dom.
        container.insertBefore(original, placeholder);
        // Replace the src.
        original.setAttribute('src', lazyFxx.getDataset(placeholder, 'src'));
        // Remove the lazyload class, because we ara already lazy loading this one.
        original.classList.remove('lazyload-placeholder');

        // Switch the image as soon as it's loaded.
        if (original.complete) {
            // It's already loaded. Maybe from the cache?
            lazyFxx.switchPlaceholder(original);
        } else {
            original.addEventListener('load', lazyFxx.switchPlaceholder)
        }
    };

    /**
     * We replace the src, simple as that.
     *
     * @param {Node} sourceSet
     */
    lazyFxx.lazyLoadSourceSet = function (sourceSet) {

        // Replace the scr set.
        var src = lazyFxx.getDataset(sourceSet, 'src');
        if (src === '') {
            return;
        }

        sourceSet.setAttribute('srcset', lazyFxx.getDataset(sourceSet, 'src'));
        sourceSet.removeAttribute('data-src');
        sourceSet.classList.remove('lazyload-placeholder');

        // Play the animation.
        var sibling = sourceSet.nextElementSibling;
        while (sibling) {
            if (sibling.matches('img:not(.lazy-done)')) {
                sibling.classList.add('lazy-done');
                sibling.addEventListener('load', lazyFxx.animateSourcePicture);
                return;
            }
            sibling = sibling.nextElementSibling
        }
    };

    /**
     * We replace the src, simple as that.
     *
     * @param {event} event
     */
    lazyFxx.animateSourcePicture = function (event) {
        // We do this only once.
        event.target.removeEventListener('load', lazyFxx.animateSourcePicture);

        // Play the animation as soon as it's loaded.
        event.target.classList.add('lazy-picture');
        setTimeout(function () {
            event.target.classList.add('lazy-loaded');
        }, 200);
    };

    /**
     * Gets the dataset from an element.
     *
     * @param {Element} el
     * @param {string} what
     *
     * @returns {string}
     */
    lazyFxx.getDataset = function (el, what) {

        /** @type {string|null} */
        var result = el.getAttribute('data-' + what);
        if (result === null) {
            return '';
        } else {
            return result;
        }
    };

    /**
     * Switch the placeholder with te real image
     *
     * @param {Event|Node} original
     */
    lazyFxx.switchPlaceholder = function (original) {
        // Check, if we have an event, and get the real image.
        if (typeof original.target !== 'undefined') {
            original = original.target;
        }

        // Remove the event listener, just in case.
        original.removeEventListener('load', lazyFxx.switchPlaceholder);

        // Get the placeholder.
        var placeholder = original.nextSibling;
        var container = original.parentNode;
        var parent = container.parentNode;

        setTimeout(function () {
            placeholder.className += ' lazyload-hide';

            // Display the image
            original.className += ' lazyload-show';

            // Remove the placeholder from DOM and cache.
            placeholder.classList.remove('lazyload-placeholder');
            lazyFxx.updateList();

            // Re-witch the placeholder and the original, so we can keep the
            // registered events, which are probably on the placeholder.
            setTimeout(function () {
                placeholder.style['transition'] = 'none';
                placeholder.setAttribute('src', original.getAttribute('src'));
                placeholder.className = original.className;
                container.removeChild(original);

                // Remove all lazyloading debris.
                placeholder.style['transition'] = '';
                placeholder.classList.remove('lazyload-show');
                placeholder.classList.remove('lazyload-original');
                placeholder.removeAttribute('data-src');
                parent.insertBefore(placeholder, container);
                parent.removeChild(container);

            }, 500);

            container.style = [];
        }, 100);
    };

    // Start the drama as soon as the DOM is ready.
    document.addEventListener("DOMContentLoaded", lazyFxx.onDocumentReady);

    // Register it in the DOM
    window.lazyFxx = lazyFxx;

    // Return the lazyFxx object.
    return lazyFxx;

})();