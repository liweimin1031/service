<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : ClassAutoloader.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Auto loader class based on namespace
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 60 $)
 * CREATED     : Jan 14, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2013-03-20 11:14:49 +0800 (Wed, 20 Mar 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) ClassAutoloader.php              1.0 Jan 14, 2013
   by Michelle Hong


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */


/* ===============================================================
   Begin of ClassAutoloader.php
   =============================================================== */


namespace Astri\Lib\Util;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */



/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */



/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */
/**
 *
 * Lms Class Autoloader to dynamically load the class.
 *
 * Please note, we Captilize the namespace name even it is not case-sensitive.
 * However, the corresponding folder name should be all in lower-case.
 *
 * For example,
 * <code>
 *    namespace Cls\Core;
 * </code>
 *
 * The path should be astri\core;
 *
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class ClassAutoloader
{
    /**
     * namespace default separator
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const NS_SEPARATOR     = '\\';


    /**
     * @var array Namespace/directory pairs to search;
     *            ASTRI library added by default
     */
    protected $_namespaces = array();

    /**
     *
     * ClassAutoloader Constructor
     *
     * In Lms, we have two namespaces 'Cls' for Lms and 'Lemo' for LEarning
     * MOdule.
     * @todo Add the Cls namespace for reference the Cls Class later
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct()
    {
        //$this->registerNamespace('Cls', (dirname(dirname(__DIR__))));
        $this->registerNamespace('Astri\Lib', dirname(__DIR__));
    }



    /**
     *
     * Register a namespace
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $namespace namespace
     * @param string $directory path to the namespace directory
     * @return \Cls\Core\Loader\ClassAutoloader
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function registerNamespace($namespace, $directory)
    {
        $namespace = rtrim($namespace, self::NS_SEPARATOR). self::NS_SEPARATOR;
        $this->_namespaces[$namespace] = $this->normalizeDirectory($directory);
        return $this;
    }

    /**
     * Register many namespace/directory pairs at once
     *
     * @param  array $namespaces
     * @return StandardAutoloader
     */

    /**
     *
     * Register multiple namespaces
     *
     * @version 1.0
     * @since  Version 1.0
     * @param array $namespaces an array of the key value pair for namespace
     *                          and its path
     * @return boolean|\Cls\Core\Loader\ClassAutoloader
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function registerNamespaces($namespaces)
    {
        if (!is_array($namespaces) ) {
            return false;
        }

        foreach ($namespaces as $namespace => $directory) {
            $this->registerNamespace($namespace, $directory);
        }
        return $this;
    }

    /**
     *
     * Auto load a class
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $class Fully quantified class name
     * @return mixed the class or false if not found
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function autoload($class)
    {
        if (false !== strpos($class, self::NS_SEPARATOR)) {
            if ($this->loadClass($class)) {
                return $class;
            }
            return false;
        }

        return false;
    }

    /**
     *
     * Register the ClassAutoloader to php spl_autoload
     *
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }


    /**
     *
     * Transform the class name to a filename
     *
     * During the transformation, we lower case the directory path
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $class
     * @param string $directory
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function transformClassNameToFilename($class, $directory)
    {
        // $class may contain a namespace portion, in  which case we need
        // to preserve any underscores in that portion.
        $matches = array();
        preg_match('/(?P<namespace>.+\\\)?(?P<class>[^\\\]+$)/', $class, $matches);

        $class     = (isset($matches['class'])) ? $matches['class'] : '';
        $namespace = (isset($matches['namespace'])) ? $matches['namespace'] : '';

        return $directory
        . strtolower(str_replace(self::NS_SEPARATOR, DIRECTORY_SEPARATOR, $namespace))
        . $class. '.php';
    }


    /**
     *
     * Load a class based on type
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $class Class name
     * @return boolean
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function loadClass($class)
    {

        foreach ($this->_namespaces as $leader => $path) {

            if (0 === strpos($class, $leader)) {

                $trimmedClass = substr($class, strlen($leader));
                // create filename
                $filename = $this->transformClassNameToFilename($trimmedClass, $path);

                if (file_exists($filename)) {
                    return include $filename;
                }
                return false;
            }
        }
        return false;
    }

    /**
     *
     * Normalize the directory to include a trailing directory separator
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $directory
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function normalizeDirectory($directory)
    {
        $last = $directory[strlen($directory) - 1];
        if (in_array($last, array('/', '\\'))) {
            $directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;
            return $directory;
        }
        $directory .= DIRECTORY_SEPARATOR;
        return $directory;
    }
}

/* ===============================================================
   End of ClassAutoloader.php
   =============================================================== */