<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : ColumnDecimal.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database column definition for Decimal
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Jan 14, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) ColumnDecimal.php              1.0 Jan 14, 2013
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
   Begin of ColumnDecimal.php
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
 * Lms Database column definition for Decimal
 *
 * By default is decimal(10,2).
 * Usage:
 * <code>
 *     ColumnFactory::createColumn(Column::TYPE_DECIMAL);
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
class ColumnDecimal extends Column
{
    /**
     * @var string Type name Column::TYPE_DECIMAL
     * @version 1.0
     * @since  Version 1.0
     */
    public $type= Column::TYPE_DECIMAL;
    /**
     *
     * @var integer  The maximum number of digits (the precision)
     * @version 1.0
     * @since Version 1.0
     */
    protected $_decimalM = 10;
    /**
     *
     * @var integer The number of digits to the right of the decimal point
     * @version 1.0
     * @since Version 1.0
     */
    protected $_decimalD = 2;
    /**
     *
     * A decimal type column constructor
     *
     * @since  Version 1.0
     * @param array $options options to set the value of the columns
     *                       only Column::DECIMAL_M and Column::DECIMAL_M
     *                       are valid.
     * @see Column::__construct
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($options=array())
    {
        if (isset($options[Column::DECIMAL_M])) {
            $this->_decimalM= $options[Column::DECIMAL_M];
        }
        if (isset($options[Column::DECIMAL_D])) {
            $this->_decimalD= $options[Column::DECIMAL_D];
        }
        parent::__construct($options);
    }
    /**
     *
     * Get bind type based on PDO
     * (non-PHPdoc)
     * @since Version 1.0
     * @return PARAM_STR
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
     *
     * Get the maximum number of digits
     *
     * @version 1.0
     * @since  Version 1.0
     * @return integer
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getDecimalM()
    {
        return $this->_decimalM;
    }

    /**
     *
     * Get the number of digits to the right of the decimal point
     *
     * @version 1.0
     * @since  Version 1.0
     * @return integer
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getDecimalD()
    {
        return $this->_decimalD;
    }
}

/* ===============================================================
   End of ColumnDecimal.php
   =============================================================== */