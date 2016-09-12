<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : ColumnTimestamp.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database column definition for Time stamp
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Jan 14, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) ColumnTimestamp.php              1.0 Jan 14, 2013
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
   Begin of ColumnTimestamp.php
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
 * Lms Database column definition for Time stamp
 *
 * Lms Timestamp is a int(10), instead of DATETIME or TIMESTAMP SQL TYPE
 *
 * The class extends ColumnInt.
 * Usage:
 * <code>
 *     ColumnFactory::createColumn(\Clms\Tools\PhpDao\Column::TYPE_TIMESTAMP);
 * </code>
 *
 * @package Php-Dao
 * @subpackage column
 * @since  Version 1.0
 * @see ColumnInt
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class ColumnTimestamp extends ColumnInt
{
    /**
     *
     * @var string Column type name Column::TYPE_TIMESTAMP
     * @version 1.0
     * @since  Version 1.0
     */
    public $type= Column::TYPE_TIMESTAMP;
    /**
     *
     * A timestamp type column  constructor
     *
     * Set the length 10 and default value 0
     *
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct()
    {
        parent::__construct(array('length'=> 10,'defaultValue'=> 0));
    }
}

/* ===============================================================
   End of ColumnTimestamp.php
   =============================================================== */