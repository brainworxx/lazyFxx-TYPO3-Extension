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

namespace Brainworxx\Lazyfxx\ViewHelpers\Uri;

use Brainworxx\Lazyfxx\Traits\ImageProcessor;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

class ImageViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Uri\ImageViewHelper
{
    use ImageProcessor;

    /**
     * Resizes the image (if required) and returns its path. If the image was not resized, the path will be equal to $src
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     * @throws Exception
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $src = (string)$arguments['src'];
        $image = $arguments['image'];
        $treatIdAsReference = (bool)$arguments['treatIdAsReference'];
        $cropString = $arguments['crop'];
        $absolute = $arguments['absolute'];

        if (($src === '' && $image === null) || ($src !== '' && $image !== null)) {
            throw new Exception('You must either specify a string src or a File object.', 1460976233);
        }

        try {
            $imageService = self::getImageService();
            $image = $imageService->getImage($src, $image, $treatIdAsReference);

            if ($cropString === null && $image->hasProperty('crop') && $image->getProperty('crop')) {
                $cropString = $image->getProperty('crop');
            }

            $cropVariantCollection = CropVariantCollection::create((string)$cropString);
            $cropVariant = $arguments['cropVariant'] ?: 'default';
            $cropArea = $cropVariantCollection->getCropArea($cropVariant);
            $processingInstructions = [
                'width' => $arguments['width'],
                'height' => $arguments['height'],
                'minWidth' => $arguments['minWidth'],
                'minHeight' => $arguments['minHeight'],
                'maxWidth' => $arguments['maxWidth'],
                'maxHeight' => $arguments['maxHeight'],
                'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
            ];
            if (!empty($arguments['fileExtension'])) {
                $processingInstructions['fileExtension'] = $arguments['fileExtension'];
            }

            $processedImage = $imageService->applyProcessingInstructions($image, $processingInstructions);
            // edit bwx
            return static::returnLazyFxxUrl(
                $image,
                $processingInstructions,
                $imageService->getImageUri($processedImage, $absolute),
                $imageService,
                $absolute,
                $arguments
            );
            // edit end.
        } catch (ResourceDoesNotExistException $e) {
            // thrown if file does not exist
            throw new Exception($e->getMessage(), 1509741907, $e);
        } catch (\UnexpectedValueException $e) {
            // thrown if a file has been replaced with a folder
            throw new Exception($e->getMessage(), 1509741908, $e);
        } catch (\RuntimeException $e) {
            // RuntimeException thrown if a file is outside of a storage
            throw new Exception($e->getMessage(), 1509741909, $e);
        } catch (\InvalidArgumentException $e) {
            // thrown if file storage does not exist
            throw new Exception($e->getMessage(), 1509741910, $e);
        }
    }
}
