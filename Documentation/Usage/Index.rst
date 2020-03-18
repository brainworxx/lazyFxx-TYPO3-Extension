.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Usage in Fluid
==============

Viewhelpers
^^^^^^^^^^^

LazyFxx is used inside the fluid templates. We hve provides three viewhelpers for this:

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
        You need to provide the processing class, either by usinf the
        option 'Use default processor' in the extension configuration,
        or by using the overwriteProcessor argument.

        If you are using the overwriteProcessor argument, please provide
        a fully qualified class name.
     -->
    <lfxx:image src="path/to/my/image.png" alt="some description" />
    <lfxx:media file="{fileobject}" width="400" height="375" overwriteProcessor="Brainworxx\Lazyfxx\Processors\BlurProcessor"/>

    <!--
        Get the uri of the lazy image
    -->
    <lfxx:uri.image image="{imageReference}" overwriteProcessor="Brainworxx\Lazyfxx\Processors\BlurProcessor" />

    </html>

There is however an additional argument: :literal:`overwriteProcessor`. Here you can overwrite the configured processor class.

Picture and source tags
^^^^^^^^^^^^^^^^^^^^^^^

This one is a little bit tricky, because we do not provide a ViewHelper for it. The reason for this is simple: There are
so many ways to optimize it, that a ViewHelper would only make things more complicated.
|
You need to do three things here:

1. Add the class :literal:`lazyload-placeholder` to all source tags.
2. Add the placeholder images to the :literal:`srcset` attribute to all source tags.
3. Add the original images to the :literal:`data-src` attribute to all source tags.
4. Add another placeholder to the :literal:`img` tag inside the :literal:`picture` tag

Simple example with some hardcoded values.

.. code-block:: html

    <html xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
          xmlns:lfxx="http://typo3.org/ns/Brainworxx/Lazyfxx/ViewHelpers"
          data-namespace-typo3-fluid="true">

    <picture>
        <source class="lazyload-placeholder"
                srcset="{lfxx:uri.image(image: file, maxWidth: 780)}"
                data-src="{f:uri.image(image: file, maxWidth: 780)}"
                media="(min-width:1280px), (min-resolution: 136dpi) and (min-width:780px)">
        <source class="lazyload-placeholder"
                srcset="{lfxx:uri.image(image: file, maxWidth: 400)}"
                data-src="{f:uri.image(image: file, maxWidth: 400)}"
                media="(min-width:640px), (min-resolution: 136dpi) and (min-width:400px)">
        <source class="lazyload-placeholder"
                srcset="{lfxx:uri.image(image: file, maxWidth: 1024)}"
                data-src="{f:uri.image(image: file, maxWidth: 1024)}">

        <!-- Fallback with lazy loading for the IE is also possible, if you absolutely have to.
        <lfxx:image image="{file}" title="{file.properties.title}" alt="{file.properties.alternative}" />
        -->
        <f:image image="{file}" title="{file.properties.title}" alt="{file.properties.alternative}" maxWidth="400" />
    </picture>



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

    <!-- example -->
    <f:image image="{imagePlaceholderObject}"
             class="lazyload-placeholder"
             data="{src: pathToOriginal}" />