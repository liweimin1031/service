<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : QueryOracle.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database Query Constructor for Oracle
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Dec 7, 2012
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
 @(#) QueryOracle.php              1.0 Dec 7, 2012
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
  Begin of QueryOracle.php
  =============================================================== */

namespace Clms\Tools\PhpDao\Query;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
//require_once(dirname(__FILE__). LMSDS.'Query.php');

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
 * Lms Database Query Builder for Oracle
 * @package Php-Dao
 * @subpackage query
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
*/
class QueryOracle extends Query
{


    /**
     * Process the limit parameters for ORACLE Database
     *
     * Implements Query::processLimit()
     * The oracle database use the following statement:
     * <code>
     *     "SELECT * FROM ($query) where rownum <= " . ($offset + $limit) .
           " MINUS SELECT * FROM ($query) where rownum <= $offset";
     * </code>
     * @since Version 1.0
     * @param   string   $sql   The query in string format
     * @param   integer  $limit   The limit for the result set
     * @param   integer  $offset  The offset for the result set
     * @return  string a sql statement string with limit and offset
     * (non-PHPdoc)
     * @see LmsDatabseQueyLimitable::processLimit()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function processLimit($sql, $limit, $offset = 0)
    {
        // Check if we need to mangle the query.

        if ($offset > 0 && $limit > 0) {
            $maxRow = $offset + $limit;
            return 'SELECT * FROM ('
                . PHP_EOL. 'SELECT a.*, ROWNUM rnum from ('
                . $sql
                . PHP_EOL . ') a '
                . PHP_EOL . 'WHERE ROWNUM <=' . $maxRow . ')'
                . PHP_EOL . 'WHERE rnum >' . $offset;
        } elseif ($limit > 0 ) {
            return 'SELECT * FROM (' . $sql
                . PHP_EOL . ') WHERE ROWNUM <= ' . $limit;
        } else {
            return $sql;
        }

    }

}

/* ===============================================================
   End of QueryOracle.php
   =============================================================== */