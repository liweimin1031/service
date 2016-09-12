<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Unique.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database unique constraint definition
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 85 $)
 * CREATED     : Jan 14, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-02-02 15:23:16 +0800 (週一, 02 二月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) Unique.php              1.0 Jan 14, 2013
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
   Begin of Unique.php
   =============================================================== */

namespace Clms\Tools\PhpDao;

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
 * Lms Database unique constraint definition
 * @package Php-Dao
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class Unique
{
    /**
     * @var string Unique index name
     * @version 1.0
     * @since Version 1.0
     */
    protected $_name;
    /**
     * @var mixed String or array of string of columns which hold
     *            the compound indexes
     * @version 1.0
     * @since Version 1.0
     */
    protected $_columns=null;
    /**
     *
     * Unique index constructor
     * @version 1.0
     * @since Version 1.0
     * @param string $name unique index name
     * @param mixed $columns string or array of string which hold the
     *                      compound indexes
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($name, $columns)
    {
        $this->_name= $name;
        $this->_columns = $columns;
    }
    /**
     *
     * Get the columns list related to the unique key
     * @version 1.0
     * @since Version 1.0
     * @return mixed
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getColumns()
    {
        return $this->_columns;
    }
    /**
     *
     * Get the key name
     * @version 1.0
     * @since Version 1.0
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getKeyName()
    {
        return $this->_name;
    }

}

/* ===============================================================
   End of Unique.php
   =============================================================== */