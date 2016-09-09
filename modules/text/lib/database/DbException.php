<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : DbException.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database Exception
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 3 $)
 * CREATED     : Jan 8, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2013-03-15 10:54:21 +0800 (Fri, 15 Mar 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) DbException.php              1.0 Jan 8, 2013
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
   Begin of DbException.php
   =============================================================== */

namespace Astri\Lib\Database;
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
 * Database exception definition
 *
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class DbException extends \Exception
{
    /**
     * Error code base
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_BASE = 100000;

    /**
     * Error code for database connection error
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_CONNECTION = 100001;

    /**
     * Error code for cannot find suitable driver class
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_CANNOT_GET_DRIVER = 100002;

    /**
     * Error code for cannot find query class for driver
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_CANNOT_GET_QUERY_CLASS = 100003;

    /**
     * Error code for unsupported PDO library
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_PDO_UNSUPPOT = 100004;

    /**
     * Error code for database test connection multiple times
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_TEST_CONNECTION_MULTIPLE = 100005;

    /**
     * Error code for database insert
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_CREATE = 100006;

    /**
     * Error code for database update
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_UPDATE = 100007;

    /**
     * Error code for database delete
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_DELETE = 100008;

    /**
     * Error code for database read
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_READ = 100009;

    /**
     * Error code for database query fail
     *
     * Used by different query other than CRUD operation
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_QUERY_FAIL= 100010;

    /**
     * Error code for wrong search parameter cause empty record
     *
     * Used by different query other than CRUD operation
     * @var integer
     * @version 1.0
     * @since Version 1.0
     */
    const DBERROR_NO_RECORD= 100011;



    /**
     *
     * DbExcpetion constructor
     *
     * @version 1.0
     * @since  Version 1.0
     * @param number $code Error code for application
     * @param string $previous Previous Exception
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($code = 0, $previous = null)
    {
        $message = $this->getErrorMessage($code);
        parent::__construct($message, $code, $previous);
    }

    /**
     *
     * Convert the Exception to string
     *
     * @version 1.0
     * @since Version 1.0
     * (non-PHPdoc)
     * @see Exception::__toString()
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __toString()
    {
        return __CLASS__
            . ": [{$this->code}]:{$this->message}: [{$this->getDevCode()}]: [{$this
                ->getDevMessage()}]\n";
    }

    /**
     *
     * Get the error message based on error code
     *
     * @version 1.0
     * @since  Version 1.0
     * @param integer $code Error code
     * @return string Error message
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private function getErrorMessage($code)
    {
        switch ($code) {
            case self::DBERROR_BASE:
                return 'Database unknown error';
            case self::DBERROR_CONNECTION:
                return 'Database cannot connect';
            case self::DBERROR_CANNOT_GET_DRIVER: // 4 //
                return 'Database cannot get the suitable driver';
            case self::DBERROR_CANNOT_GET_QUERY_CLASS: // 8 //
                return 'Database cannot get the query class';
            case self::DBERROR_PDO_UNSUPPOT: // 16 //
                return 'Database is not supported by your server';
            case self::DBERROR_TEST_CONNECTION_MULTIPLE:
                return 'Database test the connection multiple times';
            case self::DBERROR_CREATE:
                return 'Database insert error';
            case self::DBERROR_UPDATE:
                return 'Database update error';
            case self::DBERROR_DELETE:
                return 'Database delete error';
            case self::DBERROR_READ:
                return 'Database search error';
            case self::DBERROR_QUERY_FAIL:
                return 'Database query error';
            case self::DBERROR_NO_RECORD:
                return 'Cannot find record';
        }
        return "Database unknown error";
    }

    /**
     *
     * Get previous error
     *
     * Used by tracking the old previous error trigger the exception if any
     * @version 1.0
     * @since  Version 1.0
     * @return boolean
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function hasPrevious()
    {
        $previous = $this->getPrevious();
        if (!empty($previous)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * Get the previous error code
     *
     * @version 1.0
     * @since  Version 1.0
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getDevCode()
    {
        if ($this->hasPrevious()) {
            return $this->getPrevious()->getCode();
        }
        return '';
    }

    /**
     *
     * Get previous error message
     *
     * @version 1.0
     * @since  Version 1.0
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getDevMessage()
    {
        if ($this->hasPrevious()) {
            return $this->getPrevious()->getMessage();
        }
        return '';
    }
}

/* ===============================================================
   End of DbException.php
   =============================================================== */