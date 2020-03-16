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
 *   lazyFxx Copyright (C) 2017-2019 Brainworxx GmbH
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

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$boot = function () {
    /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
    );

    // Store the settings. We use them all over the place.
    \Brainworxx\Lazyfxx\Tool\Box::setSettings(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    )->get('lazyfxx'));

    // Check for '1'. For some reason we get a 'true' string, in case we have no data.
    if (\Brainworxx\Lazyfxx\Tool\Box::getSettings()['useXclasses'] === '1') {
        // X-Classing the image viewhelper.
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Fluid\ViewHelpers\ImageViewHelper::class] = [
            'className' => Brainworxx\Lazyfxx\Xclass\ImageViewHelper::class
        ];

        // X-Classing the image viewhelper.
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Fluid\ViewHelpers\MediaViewHelper::class] = [
            'className' => Brainworxx\Lazyfxx\Xclass\MediaViewHelper::class
        ];
    }
};

$boot();
unset($boot);
