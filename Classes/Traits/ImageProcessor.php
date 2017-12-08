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
 *   lazyFxx Copyright (C) 2017 Brainworxx GmbH
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

use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Holding typical functions for the image processing for viewhelpers.
 *
 * @package Brainworxx\Lazyfxx\Traits
 */
trait ImageProcessor
{

    /**
     * Generate the uri for the Placeholder
     *
     * @usage Use after:
     *   $this->tag->addAttribute('src', $imageUri);
     *
     * @param FileReference $image
     * @param array $processingInstructions
     * @param string $imageUri
     * @param \TYPO3\CMS\Extbase\Service\ImageService $imageService
     */
    protected function insertLazyFxx($image, array $processingInstructions, $imageUri, $imageService)
    {
        if (!is_a($image, FileReference::class)) {
            // Do nothing. This is not a file reference.
            return;
        }

        $useDefault = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['lazyfxx'])['useDefaultProcessor'];

        if ($useDefault === '1') {
            $processor = $this->retrieveDefaultProcessor();
        } else {
            $processor = $image->getReferenceProperties()['tx_lazyfxx_processor'];
        }

        if (class_exists($processor)) {
            // Do our own processing.
            $smallProcessingInstructions = $processingInstructions;
            $smallProcessingInstructions['tx_lazyfxx_processor'] = $processor;
            $smallProcessedImage = $imageService->applyProcessingInstructions(
                $image,
                $smallProcessingInstructions
            );
            $smallImageUri = $imageService->getImageUri($smallProcessedImage, $this->arguments['absolute']);

            $this->tag->addAttribute('src', $smallImageUri);
            $this->tag->addAttribute('data-src', $imageUri);
            $this->tag->addAttribute('class', $this->tag->getAttribute('class') . ' lazyload-placeholder');
        }
    }

    /**
     * Retrieve the standard processor class, with some static caching.
     *
     * @return string
     *   The standard processor class.
     */
    protected function retrieveDefaultProcessor()
    {
        static $processor;

        if (empty($processor)) {
            // Try to get the default processor.
            // We use the last one that we find.
            $_EXTKEY = 'lazyfxx';

            // Scanning the processor folder for a dynamic class list.
            $configPath = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY])['directory'];
            $configPath = rtrim($configPath, '/') . '/';
            $configPath = GeneralUtility::getFileAbsFileName($configPath);

            if (is_readable($configPath)) {
                // Use the provided path from the configuration.
                $configPath = $configPath . '*';
            } else {
                // Use the path from the extension as a fallback.
                $configPath = ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Processors/*';
            }

            $namespace = trim(unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY])['namespace']);

            $fileList = glob($configPath);
            foreach ($fileList as $filePath) {
                $className = trim($namespace . pathinfo($filePath)['filename'], '\\');
                if (call_user_func($className . '::isDefault', $className)) {
                    $processor = $className;
                }
            }
        }

        return $processor;
    }
}
