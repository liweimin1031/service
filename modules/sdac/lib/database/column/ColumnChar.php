<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : ColumnChar.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database column definition for Char
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Jan 14, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) ColumnChar.php              1.0 Jan 14, 2013
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
   Begin of ColumnChar.php
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
 * Lms Database column definition for Char
 *
 * Usage:
 * <code>
 *     ColumnFactory::createColumn(Column::TYPE_CHAR, array(
 *          Column:: IS_REQUIRED => true,
 *          Column:: LENGTH => 8,
 *     ));
 * </code>
 * @package Php-Dao
 * @subpackage column
 * @version 1.0
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class ColumnChar extends Column
{
    /**
     * @var string Type name Column::TYPE_CHAR
     * @version 1.0
     * @since  Version 1.0
     */
    public $type= Column::TYPE_CHAR;
    /**
     * @var integer The length of the field, the default value is 255
     * @version 1.0
     * @since  Version 1.0
     */
    protected $_length= 255;
    /**
     *
     * A char type column constructor
     *
     * @since  Version 1.0
     * @param array $options support set attribute Column::LENGTH
     *
     * @see Column::__construct
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($options=array())
    {
        if (isset($options[Column::LENGTH])) {
            $this->_length= $options[Column::LENGTH];
        }
        parent::__construct($options);
    }
    /**
     *
     * Get bind type based on PDO
     * (non-PHPdoc)
     * @since Version 1.0
     * @return PDO::PARAM_STR
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
     * Get the max length of the char, by default it is 255.
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

}


/* ===============================================================
   End of ColumnChar.php
   =============================================================== */