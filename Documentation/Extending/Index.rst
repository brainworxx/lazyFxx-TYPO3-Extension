.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


Writing your own processors
===========================

lazyFxx allows you to create your own image processing classes:

  - Create an extension, if you have not done so already.
  - Create a new class somewhere in the class folder of your extension.
  - Let this new class extend the :literal:`Brainworxx\Lazyfxx\Processors\AbstractProcessor`
  - Name your class XyzProcessor, where you can use whatever name you want, followed with :literal:`Processor`
  - The filename must be the same as the class name.

This new processor class needs three different methods:

1. getMyName()
^^^^^^^^^^^^^^

lazyFxx uses this method to dynamically generate the TCA fpr the file reference.

.. code-block:: php

    public static function getMyName()
    {
        return array(
            'The name the processor will have in the TYPO3 backend.',
            static::class
        );
    }


2. instructionsGm()
^^^^^^^^^^^^^^^^^^^

The GraphicsMagick instructions, performed on your image. If you do not use GraphicsMagick, an empty method should be enough.

.. code-block:: php

    protected function instructionsGm()
    {
        $this->simpleProcessStep('convert', "-blur 0x8");
    }

3. instructionsIm()
^^^^^^^^^^^^^^^^^^^

The ImageMMagick instructions, performed on your image. If you do not use ImageMagick, an empty method should be enough.

.. code-block:: php

    protected function instructionsIm()
    {
        $this->simpleProcessStep('convert', "-blur 0x8");
    }


Registering your processor classes
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Now that you have created your own processor, you need to register it in lazyFxx. Go to the extension manager and open the imageFxx settings. Enter the namespace of your class as well as where imageFxx can find them. Then wipe all caches.

lazyFxx will **not** autoload these classes, but will list the directory to get the class names from the file names.