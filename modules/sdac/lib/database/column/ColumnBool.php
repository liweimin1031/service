<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : ColumnBool.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database column definition for BOOL
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Jan 14, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) ColumnBool.php              1.0 Jan 14, 2013
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
   Begin of ColumnBool.php
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
 * Lms Database column definition for Boolean
 *
 * Usage:
 * <code>
 *     ColumnFactory::createColumn(Column::TYPE_BOOL, array(
 *          Column:: DEFAULT_VALUE => true
 *     ));
 * </code>
 *
 * @package Php-Dao
 * @subpackage column
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class ColumnBool extends Column
{
    /**
     * @var string Type name Column::TYPE_BOOL
     * @version 1.0
     * @since Version 1.0
     */
    public $type= Column::TYPE_BOOL;
    /**
     *
     * A bool type column constructor
     *
     * @since  Version 1.0
     * @param array $options boolean type options, such as default value, etc
     * @see Column::__construct
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($options=array())
    {
        if (!isset($options[Column::DEFAULT_VALUE])) {
            $options[Column::DEFAULT_VALUE]=false;
        }
        parent::__construct($options);
    }
    /**
     *
     * Get bind type based on PDO
     * (non-PHPdoc)
     * @since Version 1.0
     * @return PDO::PARAM_BOOL
     * @see Column::getBindParamType()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getBindParamType()
    {
        return \PDO::PARAM_BOOL;
    }

}


/* ===============================================================
   End of ColumnBool.php
   =============================================================== */