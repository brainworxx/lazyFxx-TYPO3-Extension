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

$EM_CONF[$_EXTKEY] = [
    'title' => 'LazyFxx',
    'description' => 'LazyFxx, image lazy loading with placeholders made by the TYPO3 image processing. You can also write your own image processors.',
    'category' => 'fe',
    'version' => '2.0.0',
    'state' => 'beta',
    'uploadfolder' => false,
    'clearCacheOnLoad' => true,
    'author' => 'BRAINWORXX GmbH',
    'author_email' => 'info@brainworxx.de',
    'createDirs' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '10.3.0-10.4.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'clearcacheonload' => true,
];

