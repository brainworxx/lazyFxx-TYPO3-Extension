.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Usage in Fluid
==============

Viewhelpers
^^^^^^^^^^^

LazyFxx is used inside the fluid templates. We hve provides tree viewhelpers for this:

  - Image viewhelper
  - Media viewhelper
  - Uri viewhelper

Those are essential the same as their original core counterparts.

.. code-block:: html

    <html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
          xmlns:lfxx="http://typo3.org/ns/Brainworxx/Lazyfxx/ViewHelpers"
          data-namespace-typo3-fluid="true">

    <!--
       Render the ready-to-use lazy images for our js library.
     -->
    <lfxx:image src="path/to/my/image.png" alt="some description" />
    <lfxx:media file="path/to/my/file.jpg" width="400" height="375" />

    <!-- Get the uri of the lazy image -->
    <lfxx:uri.image image="{imageobject}" alt="some description" />

    </html>

Use only the processor, and  not the JavaScript
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

There are a lot of awesome lazyloading js libs out there. You do not have to use the provided library. Simply do the following:

  - Use the uri viewhelper to get the uri of the lazy image
  - Remove the js and css from the typoscript

.. code-block:: typoscript

    page.includeJSFooterlibs.lazyfxx >
    page.includeCSS.lazyfxx >

Use only the JavaScript and not the processor
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

If you do not want to use the image processing of TYPO3 and only want to use the java script part, you only need to do the following:

  - The src needs to point to the placeholder image.
  - Add the class :literal:`lazyload-placeholder` to your image.
  - Add the attribute :literal:`data-src` with the path to the image you want to load.
  - Make sure that the files :literal:`lazyfxx.js` and :literal:`styles.css` are included on the frontend.

.. code-block:: html

    <f:image image="{imagePlaceholderObject}"
             class="lazyload-placeholder"
             data="{src: pathToOriginal}" />