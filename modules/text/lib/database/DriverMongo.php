<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : DriverMongo.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Database Driver for Mongo Database
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 2376 $)
 * CREATED     : Feb 5, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2014-11-18 17:05:20 +0800 (Tue, 18 Nov 2014) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) DriverMongo.php              1.0 Feb 5, 2013
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
   Begin of DriverMongo.php
   =============================================================== */

namespace Astri\Lib\Database;

use Astri\Lib\Util\String;

use Astri\Lib\Util\Singleton;

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
 * Database Driver for Mongo Database
 *
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class DriverMongo extends Singleton
{
    const RETRY = 3;
    /**
     *
     * @var \MongoDB MongoDB instance
     * @version 1.0
     * @since Version 1.0
     */
    protected $_database = null;


    /**
     *
     * @var \MongoClient MongoDB client
     * @version 1.0
     * @since Version 1.0
     */
    protected $_client= null;
    
    /**
     * 
     * @var Set the retry for database
     * @since  Version 1.4.1
     * @author      Michelle Hong
     * @updates
     */
    protected $_retry =0;


    /**
     *
     * Mongo driver constructor
     *
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */

    protected function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * Try to connect to database
     *
     * @version 1.0
     * @since  Version 1.0
     * @throws DbException
     * @return boolean
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function connect()
    {
        global $mongoOptions;
        
        //\MongoLog::setLevel(\MongoLog::ALL);
        //\MongoLog::setModule(\MongoLog::ALL);
        try {
            
            $connectOptions= array();
            if (!empty($mongoOptions['replicaSet'])) {
                $connectOptions =
                        array(
                                "replicaSet" => $mongoOptions['replicaSet'],
                                "readPreference" => \MongoClient::RP_PRIMARY_PREFERRED
                        );

                //\MongoCursor::$slaveOkay = true;
                
            }
            
            
            $this->_client = new \MongoClient(
                "mongodb://" . $mongoOptions['dbuser'] . ":"
                    . $mongoOptions['dbpassword'] . "@"
                    . $mongoOptions['dbhost'] . '/' . $mongoOptions['database'],
                $connectOptions
            );
            

            $this->_database = $this->_client->selectDB($mongoOptions['database']);
            
            $this->_retry = 0;
           
            
        }
        catch (\Exception $e) {
            
            $this->_retry ++;
            if($this->_retry > self::RETRY){
                error_log(print_r($e,2));
                
                $this->_retry = 0;
                throw new DbException(DbException::DBERROR_CONNECTION, $e);
            } else {
                return self::connect(); 
            }
            
        }
        return true;
    }

    /**
     *
     * Get the collections based on collection name
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection collection name
     * @return boolean|\MongoCollection
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getCollection($collection)
    {
        $collectionName = String::underscore($collection);
        //try again if the connection fail
        if ($this->isConnected()) {
            return $this->_database->$collectionName;
        }
    }

    /**
     *
     * List the collection name of the DB
     *
     * @version 1.0
     * @since  Version 1.0
     * @return boolean|Mongo collections array
     * @see
     * @author      Ming Hua
     * @testing
     * @warnings
     * @updates
     */
    public function listCollectionNames() {
        if ($this->isConnected()) {
            return $this->_database->getCollectionNames();
        }

        return false;
    }

    /**
     *
     * Drop collection
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection collection name
     * @return boolean|Mongo response
     * @see
     * @author      Ming Hua
     * @testing
     * @warnings
     * @updates
     */
    public function dropCollection($collection) {
        if ($this->isConnected()) {
            return $this->_database->$collection->drop();
        }

        return false;
    }

    /**
     *
     * Test if the database is connected
     *
     * If there is no connection, they try to reconnect, otherwise, a exception
     * will be thrown.
     *
     * @version 1.0
     * @since  Version 1.0
     * @return boolean
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function isConnected()
    {
        if (empty($this->_client) || !$this->_client->connected ) {
            $this->connect();
        }
        return true;
    }
}

/* ===============================================================
   End of DriverMongo.php
   =============================================================== */
