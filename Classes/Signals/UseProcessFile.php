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
 *   lazyFxx Copyright (C) 2017-2018 Brainworxx GmbH
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

namespace Brainworxx\Lazyfxx\Signals;

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\Service\FileProcessingService;
use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Trigger a post processing of images.
 *
 * @see
 *   $signalSlotDispatcher->connect(
 *       \TYPO3\CMS\Core\Resource\ResourceStorage::class,
 *       \TYPO3\CMS\Core\Resource\Service\FileProcessingService::SIGNAL_PostFileProcess,
 *       \Brainworxx\Lazyfxx\Signals\UseProcessFile::class,
 *       'postProcess',
 *       true
 *   );
 *
 * @package Brainworxx\Lazyfxx\Signals
 */
class UseProcessFile
{
    /**
     * The object manager, what else?
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Inject the object manager.
     *
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Post processing the files, using the registered processor class.
     *
     * @param FileProcessingService $processor
     * @param DriverInterface $driver
     * @param ProcessedFile $processedFile
     * @param FileInterface $file
     * @param string $context
     * @param array $configuration
     */
    public function postProcess(
        FileProcessingService $processor,
        DriverInterface $driver,
        ProcessedFile $processedFile,
        FileInterface $file,
        $context,
        array $configuration
    ) {
        // Test if we have to do anything.
        if (isset($configuration['tx_lazyfxx_processor']) && class_exists($configuration['tx_lazyfxx_processor'])) {
            // Jep, we need to do stuff.
            /** @var \Brainworxx\Lazyfxx\Processors\AbstractProcessor $processor */
            $fxxProcessor = $this->objectManager->get($configuration['tx_lazyfxx_processor']);
            $fxxProcessor->process($processor, $driver, $processedFile, $file, $context, $configuration);
        }
    }
}
