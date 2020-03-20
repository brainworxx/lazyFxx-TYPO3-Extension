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

namespace Brainworxx\Lazyfxx\Traits;

use Brainworxx\Lazyfxx\Processors\AbstractProcessor;
use Brainworxx\Lazyfxx\Tool\Box;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\ResourceInterface;
use TYPO3\CMS\Extbase\Service\ImageService;

/**
 * Holding typical functions for the image processing for viewhelpers.
 *
 * @package Brainworxx\Lazyfxx\Traits
 */
trait ImageProcessor
{
    /**
     * Add an additional argument, to overwrite the configured processor.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'overwriteProcessor',
            'string',
            'Overwrite the configured Processor. Please use the full qualified class name.',
            false,
            ''
        );
    }

    /**
     * Generate the uri for the Placeholder
     *
     * usage Use after:
     *   $this->tag->addAttribute('src', $imageUri);
     *
     * @param FileReference $image
     * @param array $processingInstructions
     * @param string $imageUri
     * @param \TYPO3\CMS\Extbase\Service\ImageService $imageService
     */
    protected function insertLazyFxx(
        $image,
        array $processingInstructions,
        string $imageUri,
        ImageService $imageService
    ): void {

        // Retrieve a possible processor class name.
        $processor = static::retrieveProcessorName(
            $this->arguments,
            $image
        );

        if (!empty($processor) && class_exists($processor)) {
            // Do our own processing.
            $processingInstructions[AbstractProcessor::TX_LAZYFXX_PROCESSOR] = $processor;
            $smallProcessedImage = $imageService->applyProcessingInstructions(
                $image,
                $processingInstructions
            );

            $smallImageUri = $imageService->getImageUri($smallProcessedImage, $this->arguments['absolute']);

            $this->tag->addAttribute('src', $smallImageUri);
            $this->tag->addAttribute('data-src', $imageUri);
            $this->tag->addAttribute('class', $this->tag->getAttribute('class') . ' lazyload-placeholder');
        }
    }

    /**
     * Return the uri of the lazy image.
     *
     * @param \TYPO3\CMS\Core\Resource\FileInterface $image
     *   The file reference image.
     * @param array $processingInstructions
     *   The processing information.
     * @param string $imageUri
     *   Fallback to the old uri.
     * @param \TYPO3\CMS\Extbase\Service\ImageService $imageService
     *   The image service.
     * @param bool $absolute
     *   Are we using the absolute path.
     * @param array $arguments
     *   The arguments from the template.
     *
     * @return string
     *   The processed uri, or the fallback uri.
     */
    protected static function returnLazyFxxUrl(
        $image,
        array $processingInstructions,
        string $imageUri,
        ImageService $imageService,
        bool $absolute,
        array $arguments
    ): string {
        $processor = static::retrieveProcessorName($arguments, $image);

        if (class_exists($processor)) {
            // Do our own processing.
            $processingInstructions[AbstractProcessor::TX_LAZYFXX_PROCESSOR] = $processor;

            $smallProcessedImage = $imageService->applyProcessingInstructions(
                $image,
                $processingInstructions
            );
            return $imageService->getImageUri($smallProcessedImage, $absolute);
        }

        return $imageUri;
    }

    /**
     * Retrieve the configured processor name.
     *
     * @param array $arguments
     * @param \TYPO3\CMS\Core\Resource\ResourceInterface $image
     * @return string
     */
    protected static function retrieveProcessorName(array $arguments, ResourceInterface $image): string
    {
        $processor = '';

        // Retrieve a possible processor class name.
        if (!empty($arguments['overwriteProcessor'])) {
            $processor = $arguments['overwriteProcessor'];
        } elseif (Box::getSettings()['useDefaultProcessor'] === '1') {
            $processor = Box::retrieveDefaultProcessor();
        } elseif (is_a($image, FileReference::class)) {
            $processor = $image->getReferenceProperties()[AbstractProcessor::TX_LAZYFXX_PROCESSOR];
        }

        return $processor;
    }
}
