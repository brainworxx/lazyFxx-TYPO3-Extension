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


$useDefault = \Brainworxx\Lazyfxx\Tool\Box::getSettings()['useDefaultProcessor'];

if ($useDefault !== '1') {
    // Add the filter dropdown to the FAL image display in the backend.
    $tempColumns = [
        'tx_lazyfxx_processor' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:lazyfxx/Resources/Private/Language/locallang.xlf:filter.label',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => \Brainworxx\Lazyfxx\Tool\Box::retrieveProcessorList(),
                'default' => '',
            ]
        ],
    ];
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
