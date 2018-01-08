<?php
/**
 * Created by PhpStorm.
 * User: guelzow
 * Date: 08.12.2017
 * Time: 16:11
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
     * Retrieve a cass list of image processors.
     *
     * @return array
     */
    public static function retrieveProcessorList()
    {
        $namespace = rtrim(trim(unserialize(
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['lazyfxx']
        )['namespace']), '\\') . '\\';

        $fileList = static::retrieveFileList();
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

        return $processorList;
    }

    /**
     * Retrieve the standard processor class, with some static caching.
     *
     * @return string
     *   The standard processor class.
     */
    public static function retrieveDefaultProcessor()
    {
        static $processor;

        if (empty($processor)) {
            $namespace = rtrim(trim(unserialize(
                $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['lazyfxx']
            )['namespace']), '\\') . '\\';
            $fileList = static::retrieveFileList();

            foreach ($fileList as $filePath) {
                $className = trim($namespace . pathinfo($filePath)['filename'], '\\');
                if (is_callable($className . '::isDefault') && call_user_func($className . '::isDefault')) {
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
    protected static function retrieveFileList()
    {
         $_EXTKEY = 'lazyfxx';

        // Scanning the processor folder for a dynamic class list.
        $configPath = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY])['directory'];
        $configPath = rtrim($configPath, '/') . '/';
        $configPath = GeneralUtility::getFileAbsFileName($configPath);

        if (is_readable($configPath)) {
            // Use the provided path from the configuration.
            $configPath = $configPath . '*';
        } else {
            // Use the path from the extension as a fallback.
            $configPath = ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Processors/*';
        }

        return glob($configPath);
    }
}
