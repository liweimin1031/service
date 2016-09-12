<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : QueryMysql.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Dec 7, 2012
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) QueryMysql.php              1.0 Dec 7, 2012
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
   Begin of QueryMysql.php
   =============================================================== */

namespace Clms\Tools\PhpDao\Query;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
//require_once(dirname(__FILE__). '/Query.php');

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */



/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */

/* ---------------------------------------------------------------
   Class definition
   --------------------------------------------------------------- */
/**
 *
 * Class definition for Lms database For MySQL
 * @package Php-Dao
 * @subpackage query
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class QueryMysql extends Query
{


    /**
     *
     * Process the limit parameters for MySQL Database
     *
     * Implements Query::processLimit(). The function appends
     * the string ' LIMIT $offset, $limit'
     *
     * @version 1.0
     * @since Version 1.0
     * (non-PHPdoc)
     * @see Query::processLimit()
     * @param string $sql The query in string format
     * @param int $limit The limit for the result set
     * @param int $offset The offset for the result set
     * @return string a sql statement with limit and offset
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function processLimit($sql, $limit, $offset = 0)
    {
        if ($limit > 0 || $offset > 0) {
            $sql .= ' LIMIT ' . $offset . ', ' . $limit;
        }

        return $sql;
    }

}

/* ===============================================================
   End of QueryMysql.php
   =============================================================== */