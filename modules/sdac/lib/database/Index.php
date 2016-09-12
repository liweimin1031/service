<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Index.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database index definition class
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 18 $)
 * CREATED     : Jan 14, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2013-04-26 10:29:13 +0800 (週五, 26 四月 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) Index.php              1.0 Jan 14, 2013
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
   Begin of Index.php
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
 * Lms Database index definition class
 * @package Php-Dao
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class Index
{
    /**
     * @var string Index name
     * @version 1.0
     * @since Version 1.0
     */
    protected $_name= '';

    /**
     * @var mixed String or array of string related to a index
     * @version 1.0
     * @since Version 1.0
     */
    protected $_columns='';

    /**
     * @var bool Unique index
     * @version 1.0
     * @since Version 1.0
     */
    protected $_unique= false;

    /**
     *
     * Index constraint constructor
     *
     * @since Version 1.0
     * @param string $name Index name
     * @param mixed $columns Indexed column names
     * @param bool $unique Whether the index should be unique
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($name, $columns, $unique = false)
    {

        $this->_name = $name;
        $this->_columns = $columns;
        $this->_unique = $unique;
    }
    /**
     *
     * Check the index is unique
     * @version 1.0
     * @since Version 1.0
     * @return boolean <code>true</code> if if is unique
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function isUnique()
    {
        return $this->_unique;
    }
    /**
     *
     * Return the columns information
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
     * Get the index name
     * @version 1.0
     * @since Version 1.0
     * @return string index name
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getIndexName()
    {
        return $this->_name;
    }
}


/* ===============================================================
   End of Index.php
   =============================================================== */