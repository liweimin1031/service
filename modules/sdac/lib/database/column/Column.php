<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Column.php
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
   @(#) Column.php              1.0 Jan 14, 2013
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
   Begin of Column.php
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
 * Abstract class definition for Lms Database column type
 *
 * We only support 6 type of data as defined in the Column
 * The column type does not include any unique key or index information
 * Please use Index and Unique
 *
 * Usage:
 * <code>
 *     ColumnFactory::createColumn(Column::Type_INT, array(
 *          Column:: IS_REQUIRED => true,
 *          Column:: LENGTH => 8,
 *     ));
 * </code>
 *
 * In the Lms database, we support six type of data for both MySQL and Oracle.
 *
 * <pre>
 *    ----------------------------------------------
 *    |    Lms Type  |    MySQL      |    Oracle   |
 *    ----------------------------------------------
 *    |    int       |    INT        |    NUMBER   |
 *    ----------------------------------------------
 *    |    char      |    VARCHAR    |   VARCHAR   |
 *    ----------------------------------------------
 *    |    bool      |  TINYINT(1)   |  NUMBER(1)  |
 *    ----------------------------------------------
 *    |    text      |    TEXT       |     CLOB    |
 *    ----------------------------------------------
 *    |  timestamp   |    INT(10)    |  NUMBER(10) |
 *    ----------------------------------------------
 *    |    decimal   |    DECIMAL    |   DECIMAL   |
 *    ----------------------------------------------
 * </pre>
 *
 * Please note that for text type column, default value is not set.
 *
 * @package Php-Dao
 * @subpackage column
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 *
 */
abstract class Column
{
    /*
     * Attribute Definition for Column
    */
    /**
     * Lms Database column type
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const TYPE= 'type';
    /**
     * Attribute indicate if the field is required
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const IS_REQUIRED= 'isRequired';

    /**
     * Attribute indicate if the field is auto increment
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const AUTO_INCREMENT= 'autoIncrement';
    /**
     * Attribute for field length
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const LENGTH= 'length';
    /**
     * Attribute for field default value
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const DEFAULT_VALUE= 'defaultValue';
    /**
     * Attribute for decimal field max length
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const DECIMAL_M = 'decimalM';
    /**
     * Attribute for decimal field digital length
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const DECIMAL_D = 'decimalD';


    /**
     *
     * Lms Support column support
     */
    /**
     * Lms column type suffix for vchar field
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const TYPE_CHAR = 'char';
    /**
     * Lms column type suffix for text field
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const TYPE_TEXT = 'text';
    /**
     * Lms column type suffix for timestamp field
     *
     * In Lms we use int(10) no real DATETIME or TIMESTAMP SQL type
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const TYPE_TIMESTAMP= 'timestamp';
    /**
     * Lms column type suffix for int field
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const TYPE_INT= 'int';
    /**
     * Lms column type suffix for decimal field
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const TYPE_DECIMAL= 'decimal';
    /**
     * Lms column type suffix for bool field
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    const TYPE_BOOL = 'bool';

    /**
     * @var string Type name defined in Column const
     * @version 1.0
     * @since Version 1.0
     * @see Column Type Support
     */
    public $type= Column::TYPE_INT;
    /**
     * @var bool Whether the field is required
     * @version 1.0
     * @since Version 1.0
     */
    protected $_isRequired= false;
    /**
     * @var mixed Field default value
     * @version 1.0
     * @since Version 1.0
     */
    protected $_defaultValue= null;

    /**
     *
     * A database column constructor
     *
     * only be called by the child class
     * Only isRequired and defaultValue field are used
     *
     * @since  Version 1.0
     * @param array $options column description only isRequired and defaultValue
     *                       are used
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($options)
    {
        if (isset($options[Column::IS_REQUIRED])) {
            $this->_isRequired= (bool)$options[Column::IS_REQUIRED];
        }
        if (isset($options[Column::DEFAULT_VALUE])) {
            $this->_defaultValue= $options[Column::DEFAULT_VALUE];
        }
    }


    /**
     *
     * Convert the type to the type to PDO
     *
     * Used for PDOStatement::bindParam type parameter when inserting data
     * @version 1.0
     * @since  Version 1.0
     * @return PDO::PARAM_***
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    abstract public function getBindParamType();

    /**
     *
     * Generate the NOT NULL part in table create SQL
     * @version 1.0
     * @since Version 1.0
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
    */
    public function getNullString()
    {
        return $this->_isRequired? ' NOT NULL ': '';
    }

    /**
     *
     * Get the default value of string
     * @version 1.0
     * @since Version 1.0
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getDefaultValue()
    {
        return $this->_defaultValue;
    }

    
    /**
     * 
     * Get the bind column type
     * 
     * Used for bind column when fetch data from database
     * 
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getBindColumnType()
    {
        return $this->getBindParamType();
    }
}


/* ===============================================================
   End of Column.php
   =============================================================== */