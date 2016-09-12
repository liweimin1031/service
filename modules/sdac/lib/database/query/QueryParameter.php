<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : QueryParameter.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database Query Parameter used by Query Builder
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Dec 7, 2012
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) QueryParameter.php              1.0 Dec 7, 2012
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
   Begin of QueryParameter.php
   =============================================================== */

namespace Clms\Tools\PhpDao\Query;

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
 * Lms Database Query Parameter used by Query Builder
 * @package Php-Dao
 * @subpackage query
 * @version 1.0
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class QueryParameter
{
    /**
     * @var    string  The name of the query element.
     * @version 1.0
     * @since  1.0
     */
    protected $_name = null;

    /**
     * @var    array An array of elements.
     * @version 1.0
     * @since  1.0
     */
    protected $_elements = null;

    /**
     * @var    string The string connector between two element
     * @version 1.0
     * @since  1.0
     */
    protected $_glue = null;

    /**
     *
     * Query parameter constructor
     *
     * @version 1.0
     * @since  Version 1.0
     * @param   string  $name      The name of the element.
     * @param   mixed   $elements  String or array.
     * @param   string  $glue      The glue for elements.
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($name, $elements, $glue = ',')
    {
        $this->_elements = array();
        $this->_name = $name;
        $this->_glue = $glue;

        $this->append($elements);
    }

    /**
     *
     * Convert a query parameters to string.
     *
     * This function is called from Query
     *
     * @version 1.0
     * @since  Version 1.0
     * @return string a query parameter string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __toString()
    {
        //for columns parameters
        if (substr($this->_name, -2) == '()') {
            return PHP_EOL . substr($this->_name, 0, -2)
                    . '(' . implode($this->_glue, $this->_elements) . ')';
        } else {
            return PHP_EOL . $this->_name . ' '
                    . implode($this->_glue, $this->_elements);
        }
    }


    /**
     *
     * Append new elements into existing query parameter
     *
     * @version 1.0
     * @since  Version 1.0
     * @param mixed $elements new elements (string or array)
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function append($elements)
    {
        if (is_array($elements)) {
            $this->_elements = array_merge($this->_elements, $elements);
        } else {
            $this->_elements = array_merge($this->_elements, array($elements));
        }
    }


    /**
     *
     * Gets the elements of this element.
     *
     * @version 1.0
     * @since  Version 1.0
     * @return mixed the elements inside the parameters
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getElements()
    {
        return $this->_elements;
    }

    /**
     *
     * Clone the query parameters
     *
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __clone()
    {
        foreach ($this as $k => $v) {
            if (is_object($v) || is_array($v)) {
                $this->{$k} = unserialize(serialize($v));
            }
        }
    }
}

/* ===============================================================
   End of QueryParameter.php
   =============================================================== */