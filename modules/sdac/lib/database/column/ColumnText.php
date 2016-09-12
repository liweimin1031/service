<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : ColumnText.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database column definition for Text
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Jan 14, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) ColumnText.php              1.0 Jan 14, 2013
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
   Begin of ColumnText.php
   =============================================================== */

namespace Clms\Tools\PhpDao\Column;

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
 * Lms Database column definition for Text
 *
 * Usage:
 * <code>
 *     ColumnFactory::createColumn(\Clms\Tools\PhpDao\Column::TYPE_TEXT);
 * </code>
 * @package Php-Dao
 * @subpackage column
 * @since  Version 1.0
 * @version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class ColumnText extends Column
{
    /**
     * @var string  field type name Column::TYPE_TEXT
     * @version 1.0
     * @since Version 1.0
     */
    public $type= Column::TYPE_TEXT;


    /**
     *
     * Get bind type based on PDO
     * (non-PHPdoc)
     * @since Version 1.0
     * @return PDO::PARAM_STR
     *
     * @see Column::getBindParamType()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */

    public function getBindParamType()
    {
        return \PDO::PARAM_STR;
    }

    /**
     * Get the bind column type
     * 
     * @version 1.0
     * @since  Version 1.0
     * @return string  \PDO::PARAM_LOB
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getBindColumnType()
    {
        return \PDO::PARAM_LOB;
    }

}


/* ===============================================================
   End of ColumnText.php
   =============================================================== */