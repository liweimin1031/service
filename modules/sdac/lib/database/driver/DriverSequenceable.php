<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : DriverSequenceable.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Dec 18, 2012
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) DriverSequenceable.php              1.0 Dec 18, 2012
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
   Begin of DriverSequenceable.php
   =============================================================== */

namespace Clms\Tools\PhpDao\Driver;

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
 * Interface for Lms Database to get the next sequence value from database
 * This interface should be implemented by the database driver which does
 * not support auto-increment function natively
 * @package Php-Dao
 * @subpackage driver
 * @version 1.0
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */

interface DriverSequenceable
{
    /**
     *
     * Function to get the next value
     * Used for auto-increment
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getSequencer($table);

    /**
     *
     * Create a table to hold the sequencer
     * @version 1.0
     * @since Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function createSequencerTable();

}
/* ===============================================================
   End of DriverSequenceable.php
   =============================================================== */