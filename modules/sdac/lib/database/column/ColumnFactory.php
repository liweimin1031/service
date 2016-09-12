<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : ColumnFactory.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Column Factory
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Mar 8, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) ColumnFactory.php              1.0 Mar 8, 2013
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
   Begin of ColumnFactory.php
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
   Class definition
   --------------------------------------------------------------- */
/**
 *
 * An abstract factory for create column
 *
 * Used by Dao to create column spec.
 *
 * @package Php-Dao
 * @subpackage column
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
abstract class ColumnFactory
{

    /**
     *
     * Get the instance of Database column definition
     *
     * Usage:
     * <code>
     *     ColumnFactory::createColumn(Column::Type_INT, array(
     *          Column:: IS_REQUIRED => true,
     *          Column:: LENGTH => 8,
     *     ));
     * </code>
     * @since Version 1.0
     * @param string $type a Column::TYPE_*** string
     * @param array $options configuration options to pass to constructor.
     *                       see Column attribute constant
     * @return Column an instance of Column
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function createColumn($type, $options= array())
    {
        $typeClass= __NAMESPACE__ . '\\'. 'Column'. ucfirst($type);
        return new $typeClass($options);
    }
}

/* ---------------------------------------------------------------
   Interface definition
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */



/* ===============================================================
   End of ColumnFactory.php
   =============================================================== */