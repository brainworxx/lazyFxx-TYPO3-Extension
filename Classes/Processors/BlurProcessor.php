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

/**
 * Adds a massive blur to the image.
 *
 * @package Brainworxx\Lazyfxx\Processors
 */
class BlurProcessor extends AbstractProcessor
{

    /**
     * {@inheritdoc}
     */
    public static function getMyName(): array
    {
        return [
            static::TRANSLATION_FILE . ':filter.label.blur',
            static::class
        ];
    }

    /**
     * Blurring stuff, for fun and profit.
     */
    protected function instructionsGm(): void
    {
        $this->simpleProcessStep(static::PROCESSING_CONVERT, "-blur 0x8");
    }

    /**
     * Blurring stuff, for fun and profit.
     */
    protected function instructionsIm(): void
    {
        $this->simpleProcessStep(static::PROCESSING_CONVERT, "-blur 0x8");
    }
}
