<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : ColumnInt.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Jan 14, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) ColumnInt.php              1.0 Jan 14, 2013
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
   Begin of ColumnInt.php
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
 * Lms Database column definition for Integer
 *
 * Usage:
 * <code>
 *     ColumnFactory::createColumn(Column::Type_INT, array(
 *          Column:: IS_REQUIRED => true,
 *          Column:: LENGTH => 8,
 *     ));
 * </code>
 * @package Php-Dao
 * @subpackage column
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class ColumnInt extends Column
{
    /**
     * @var string Type name Column::TYPE_INT
     * @version 1.0
     * @since  Version 1.0
     */
    public $type= Column::TYPE_INT;
    /**
     * @var integer The max length of the integer and the default length is 10
     * @version 1.0
     * @since  Version 1.0
     */
    protected $_length= 10;
    /**
     * @var boolean Whether the field is auto increment
     * @version 1.0
     * @since  Version 1.0
     */
    protected $_autoIncrement= false;
    /**
     *
     * A Int type column constructor
     * @version 1.0
     * @since  Version 1.0
     * @param array $options support set attribute Column::LENGTH
     *                       and Column::AUTO_INCREMENT
     * @see Column::__construct
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($options= array())
    {

        if (isset($options[Column::LENGTH])) {
            $this->_length= $options[Column::LENGTH];
        }
        if (isset($options[Column::AUTO_INCREMENT])) {
            $this->_autoIncrement= $options[Column::AUTO_INCREMENT];
        }
        parent::__construct($options);
    }
    /**
     *
     * Get bind type based on PDO
     * (non-PHPdoc)
     * @version 1.0
     * @since Version 1.0
     * @return PDO::PARAM_INT
     *
     * @see Column::getBindParamType()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getBindParamType()
    {
        return \PDO::PARAM_INT;
    }

    /**
     *
     * Get the max length of the int field
     *
     * @version 1.0
     * @since  Version 1.0
     * @return int max length
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getLength()
    {
        return $this->_length;
    }
    /**
     *
     * Check if the field is auto incremental
     *
     * @version 1.0
     * @return bool whether the field in auto incremental
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function isAutoIncremental()
    {
        return $this->_autoIncrement;
    }

}


/* ===============================================================
   End of ColumnInt.php
   =============================================================== */