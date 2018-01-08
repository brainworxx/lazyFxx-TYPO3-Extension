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

$EM_CONF[$_EXTKEY] = array(
    'title' => 'LazyFxx',
    'description' => 'LazyFxx, image lazy loading with placeholders made by the TYPO3 image processing. You can also write your own image processors.',
    'category' => 'fe',
    'version' => '1.0.0',
    'state' => 'beta',
    'uploadfolder' => 0,
    'clearCacheOnLoad' => 1,
    'author' => 'BRAINWORXX GmbH',
    'author_email' => 'tobias.guelzow@brainworxx.de',
    'author_company' => 'BRAINWORXX GmbH',
    'constraints' => array(
        'depends' => array(
            'typo3' => '8.7.0-8.7.99',
            'fluid_styled_content' => ''
        ),
        'conflicts' => array(),
    ),
);
