# Adding js and css files. We use css transitions for the blending of the placeholder image and the original.
page.includeJSFooterlibs {
    lazyfxx = EXT:lazyfxx/Resources/Public/JavaScript/lazyfxx.js
}
page.includeCSS {
    lazyfxx = EXT:lazyfxx/Resources/Public/Css/styles.css
}
plugin.tx_lazyfxx.settings {
    # Defining the default processor.
    default = Brainworxx\Lazyfxx\Processors\GrayscaleBlurProcessor
}