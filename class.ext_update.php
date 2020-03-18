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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Cache\CacheManager;

/**
 * Flushes the page cache and deletes all processed images.
 */
class ext_update
{

    /**
     * Returns whether this script is available in the backend.
     *
     * @return bool
     *   Always TRUE.
     */
    public function access()
    {
        return true;
    }

    /**
     * Main Function.
     */
    public function main()
    {
        // Flushing the processed images folder
        /** @var ProcessedFileRepository $repository */
        $repository = GeneralUtility::makeInstance(ProcessedFileRepository::class);
        /** @var CacheManager $cacheManager */
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);

        // remove all processed files
        $repository->removeAll();

        // clear page caches
        $cacheManager->flushCachesInGroup('pages');

        return 'Flushed Page-Cache and cleared all processed images!';
    }
}
