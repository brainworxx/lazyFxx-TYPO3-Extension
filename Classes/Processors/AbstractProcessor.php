<?php

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

namespace Brainworxx\Lazyfxx\Processors;

use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Imaging\GraphicalFunctions;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Provides all necessary methods for image manipulation and backend
 * registration of the processing classes.
 *
 * @package Brainworxx\Lazyfxx\Processors
 */
abstract class AbstractProcessor
{
    /**
     * Array key, where we store our configuration, at various places.
     */
    public const TX_LAZYFXX_PROCESSOR = 'tx_lazyfxx_processor';

    /**
     * The translation file.
     */
    protected const TRANSLATION_FILE = 'LLL:EXT:lazyfxx/Resources/Private/Language/locallang.xlf';

    /**
     * The convert commend for the processing.
     */
    protected const PROCESSING_CONVERT = 'convert';

    /**
     * Path to the local copy, used for the image manipulation.
     *
     * @var string
     */
    protected $localCopy;

    /**
     * The default processor class, according to the typo script.
     *
     * @var string
     */
    protected static $default;

    /**
     * Determine, if this is the default processor.
     *
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     *
     * @return bool
     *   The info, duh!
     */
    public static function isDefault(): bool
    {
        // Try to determine the default processor.
        if (empty(static::$default)) {
            $settings = GeneralUtility::makeInstance(ObjectManager::class)
                ->get(ConfigurationManagerInterface::class)
                ->getConfiguration(
                    ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
                );
            // For unknown reasons, I was unable to get the ts settings via
            // ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS.
            // Meh, this will do. It will only get called, when wiping the red
            // cache and only once.
            if (isset($settings['plugin.']['tx_lazyfxx.']['settings.']['default'])) {
                static::$default = $settings['plugin.']['tx_lazyfxx.']['settings.']['default'];
            } else {
                // Fall back to do nothing.
                static::$default = 'do_nothing';
            }
        }
        // Nice, huh? static::class is the class name of the extending class.
        return (static::class === static::$default);
    }

    /**
     * Simple Step for image processing. Use3d for batch processing in the
     * process() function.
     *
     * @see CommandUtility::imageMagickCommand()
     * @see CommandUtility::exec()
     *
     * @param string $command
     *   The command we want to execute. Depending on what we have here, we will
     *   generate the actual im command in another way.
     * @param string $parameter
     *   The parameters for the command.
     *
     * @return string
     *   Returns the last line from the shell.
     */
    protected function simpleProcessStep($command, $parameter): string
    {
        $parameters = $this->localCopy . ' ' . $parameter;

        // We don't need a second image in case we want to identify stuff in the image.
        if ($command !== 'identify') {
            $parameters .= ' ' . $this->localCopy;
        }

        $shellCommand = CommandUtility::imageMagickCommand($command, $parameters);

        $out = [];
        $returnValue = 1;
        return CommandUtility::exec($shellCommand, $out, $returnValue);
    }

    /**
     * Update all the stuff inside the processed file.
     *
     * @param \TYPO3\CMS\Core\Resource\ProcessedFile $processedFile
     *   The processed file object we want to manipulate.
     */
    protected function updateProperties(ProcessedFile $processedFile): void
    {
        // Get the new dimension and the checksum of the new file
        $imageDimensions = $this->getGraphicalFunctionsObject()->getImageDimensions($this->localCopy);
        $properties = [
            'width' => $imageDimensions[0],
            'height' => $imageDimensions[1],
            'size' => filesize($this->localCopy),
            'checksum' => $processedFile->getTask()->getConfigurationChecksum()
        ];
        $processedFile->updateProperties($properties);

        // Update the processed file with the content
        if ($processedFile->usesOriginalFile()) {
            // This one was not preprocessed, so we need to to this now.
            // We do not have a file inside the preprocessed folder, we will
            // create it now
            $processedFile->setName($processedFile->getTask()->getTargetFileName());
            $processedFile->updateWithLocalFile($this->localCopy);
            $processedFile->getTask()->setExecuted(true);
            // Register the processed file with the system, so it can be cleaned
            // up later, if necessary.
            $processedFileRepository = GeneralUtility::makeInstance(ProcessedFileRepository::class);
            $processedFileRepository->add($processedFile);
        } else {
            // Everything was prepared by the framework so far, we only need
            // to update the file contents.
            $processedFile->updateWithLocalFile($this->localCopy);
        }
    }

    /**
     * Getter for the GraphicalFunctions.
     *
     * @return \TYPO3\CMS\Core\Imaging\GraphicalFunctions
     */
    protected function getGraphicalFunctionsObject(): GraphicalFunctions
    {
        static $graphicalFunctionsObject = null;

        if ($graphicalFunctionsObject === null) {
            /** @var GraphicalFunctions $graphicalFunctionsObject */
            $graphicalFunctionsObject = GeneralUtility::makeInstance(GraphicalFunctions::class);
        }

        return $graphicalFunctionsObject;
    }

    /**
     * Do the processing of the image.
     *
     * @ event TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent
     *   Meh, I can not use the real tag, because Doctrine will try to interpret it.
     *
     * @param \TYPO3\CMS\Core\Resource\ProcessedFile $processedFile
     *   The processed image FAL object.
     */
    public function process(ProcessedFile $processedFile): void
    {
        if ($processedFile->getTask()->isExecuted()) {
            // Assign the file to the localCopy, so the simpleProcessStep can
            // access it.
            $this->localCopy = $processedFile->getForLocalProcessing();

            // Execute all instructions, depending on the image processor library used.
            if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'] === 'GraphicsMagick') {
                $this->instructionsGm();
            } elseif ($GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'] === 'ImageMagick') {
                $this->instructionsIm();
            }

            // Add the properties to the fal, we do have now a new image after all.
            $this->updateProperties($processedFile);
        }
    }

    /**
     * Return the name of the processor for the backend.
     *
     * @return array
     */
    abstract public static function getMyName(): array;

    /**
     * The image manipulation instructions for ImageMagick.
     *
     * usage
     *   $imageDims = $this->simpleProcessStep('identify','-format "%wx%h"');
     *   Get the dimensions of the image
     *
     *   $this->simpleProcessStep('convert', '-background white -magnify 2');
     *   Doubling the size does not really make any sense . . .
     *
     */
    abstract protected function instructionsIm(): void;

    /**
     * The image manipulation instructions for GraphicsMagick
     *
     * usage
     *   $imageDims = $this->simpleProcessStep('identify','-format "%wx%h"');
     *   Get the dimensions of the image
     *
     *   $this->simpleProcessStep('convert', '-background white -magnify 2');
     *   Doubling the size does not really make any sense . . .
     *
     */
    abstract protected function instructionsGm(): void;
}
