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

namespace Brainworxx\Lazyfxx\Tool;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Toolbox class for LazyFxx.
 *
 * @package Brainworxx\Lazyfxx\Tool\Box
 */
class Box
{
    /**
     * Array keys, used at various places.
     */
    protected const CONFIG_NAMESPACE = 'namespace';
    protected const CONFIG_DIRECTORY = 'directory';
    protected const PATHINFO_FILENAME = 'filename';
    protected const CALLBACK_GET_MY_NAME = '::getMyName';
    protected const CALLBACK_IS_DEFAULT = '::isDefault';


    /**
     * The backend settings.
     *
     * @var array
     */
    protected static $settings = [];

    /**
     * Setter for the configuration options.
     *
     * We use them all over the place. We cache them here.
     *
     * @param array $settings
     *   The settings.
     */
    public static function setSettings($settings)
    {
        static::$settings = $settings;
    }

    /**
     * Getter for the configuration options.
     *
     * We use them all over the place. We cache them here.
     *
     * @return array $settings
     *   The settings.
     */
    public static function getSettings(): array
    {
        return static::$settings;
    }

    /**
     * Retrieve a cass list of image processors.
     *
     * @return array
     */
    public static function retrieveProcessorList(): array
    {
        $namespace = rtrim(trim(static::$settings[static::CONFIG_NAMESPACE]), '\\') . '\\';

        $fileList = static::retrieveFileList();
        $processorList = [];
        $processorList[] = [' --- ', 'do_nothing'];

        if (!empty($fileList)) {
            foreach ($fileList as $filePath) {
                $className = $namespace . pathinfo($filePath)[static::PATHINFO_FILENAME];
                $callBack = $className . static::CALLBACK_GET_MY_NAME;
                if (is_callable($callBack)) {
                    if (call_user_func($className . static::CALLBACK_IS_DEFAULT, $className)) {
                        // Default class goes first.
                        array_unshift($processorList, call_user_func($callBack, $className));
                    } else {
                        // Add it at the end.
                        $processorList[] = call_user_func($callBack, $className);
                    }
                }
            }
        }

        return $processorList;
    }

    /**
     * Retrieve the standard processor class, with some static caching.
     *
     * @return string
     *   The standard processor class.
     */
    public static function retrieveDefaultProcessor(): string
    {
        static $processor = '';

        if (empty($processor)) {
            $namespace = rtrim(trim(static::$settings[static::CONFIG_NAMESPACE]), '\\') . '\\';
            $fileList = static::retrieveFileList();

            foreach ($fileList as $filePath) {
                $className = trim($namespace . pathinfo($filePath)[static::PATHINFO_FILENAME], '\\');

                if (
                    is_callable($className . static::CALLBACK_IS_DEFAULT) &&
                    call_user_func($className . static::CALLBACK_IS_DEFAULT)
                ) {
                    $processor = $className;
                    break;
                }
            }
        }
        return $processor;
    }

    /**
     * Retrieve the file list of the configured processors.
     *
     * @return array
     */
    protected static function retrieveFileList(): array
    {
        // Scanning the processor folder for a dynamic class list.
        $configPath = static::$settings[static::CONFIG_DIRECTORY];
        $configPath = rtrim($configPath, '/') . '/';
        $configPath = GeneralUtility::getFileAbsFileName($configPath);

        if (is_readable($configPath)) {
            // Use the provided path from the configuration.
            $configPath = $configPath . '*';
        } else {
            // Use the path from the extension as a fallback.
            $configPath = ExtensionManagementUtility::extPath('lazyfxx') . 'Classes/Processors/*';
        }

        return glob($configPath);
    }
}
