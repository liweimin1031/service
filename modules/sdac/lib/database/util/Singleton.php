<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Singleton.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : A singleton design class
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Mar 1, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) Singleton.php              1.0 Mar 1, 2013
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
   Begin of Singleton.php
   =============================================================== */
namespace Clms\Tools\PhpDao\Util;

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
   Class definition
   --------------------------------------------------------------- */
/**
 *
 * Singleton design pattern using PHP5 latest binding
 * @package Php-Dao
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class Singleton
{
    /**
     *
     * @var array All the class with singleton
     * @version 1.0
     * @since Version 1.0
     */
    protected static $_instances = array();

    /**
     *
     * Class constructor
     *
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function __construct()
    {
    }

    /**
     *
     * Get a singleton object
     *
     * @version 1.0
     * @since  Version 1.0
     * @return multitype:
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function getInstance()
    {

        $class = \get_called_class();
        if (!isset(static::$_instances[$class]))
            static::$_instances[$class] = new static;

        return static::$_instances[$class];

    }
}

/* ---------------------------------------------------------------
   Interface definition
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */



/* ===============================================================
   End of Singleton.php
   =============================================================== */