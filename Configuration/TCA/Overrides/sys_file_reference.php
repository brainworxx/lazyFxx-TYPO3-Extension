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

$useDetault = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['lazyfxx'])['useDefaultProcessor'];

if ($useDetault !== '1') {
    // We let the user choose.
    $_EXTKEY = 'lazyfxx';

    // Scanning the processor folder for a dynamic class list.
    $configPath = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY])['directory'];
    $configPath = rtrim($configPath, '/') . '/';
    $configPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($configPath);
    $namespace = trim(unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY])['namespace']);

    if (is_readable($configPath)) {
        // Use the provided path from the configuration.
        $configPath = $configPath . '*';
    } else {
        // Use the path from the extension as a fallback.
        $configPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Processors/*';
    }

    $fileList = glob($configPath . '*');
    $processorList = array();

    $processorList[] = array(' --- ', 'do_nothing');

    if (!empty($fileList)) {
        foreach ($fileList as $filePath) {
            $className = $namespace . pathinfo($filePath)['filename'];
            $callBack = $className . '::getMyName';
            if (is_callable($callBack)) {
                if (call_user_func($className . '::isDefault', $className)) {
                    // Default class goes first.
                    array_unshift($processorList, call_user_func($callBack, $className));
                } else {
                    // Add it at the end.
                    $processorList[] = call_user_func($callBack, $className);
                }
            }
        }
    }

    // Add the filter dropdown to the FAL image display in the backend.
    $tempColumns = array(
        'tx_lazyfxx_processor' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:lazyfxx/Resources/Private/Language/locallang.xlf:filter.label',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => $processorList,
                'default' => '',
            )
        ),
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'sys_file_reference',
        $tempColumns
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToAllPalettesOfField(
        'sys_file_reference',
        'description',
        'tx_lazyfxx_processor'
    );
    $GLOBALS['TCA']['sys_file_reference']['palettes']['imageoverlayPalette']['showitem'] .= ', tx_lazyfxx_processor';
}
