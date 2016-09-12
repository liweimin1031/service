<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : DriverMongo.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Database Driver for Mongo Database
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 103 $)
 * CREATED     : Feb 5, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2016-08-03 12:20:31 +0800 (週三, 03 八月 2016) $
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


namespace Clms\Tools\PhpDao\Mongo;

use Clms\Tools\PhpDao\Util\Singleton;
use Clms\Tools\PhpDao\Util\String;
use Clms\Tools\PhpDao\Exception\DbException;


use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Exception\BulkWriteException;


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
     * @var Database MongoDB Database name
     * @version 1.0
     * @since Version 1.0
     */
    protected $_database = null;


    /**
     *
     * @var \MongoDb\Driver\Manager MongoDB client
     * @version 1.0
     * @since Version 1.0
     */
    protected $_manager= null;
    
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
     * @var Save the mongoDB option variables
     * @version 1.0
     * @since Version 1.0
     */
    protected $_options= array();

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
       try {
            
            $connectOptions = array ();
            if (! empty ( $this->_options ['replicaSet'] )) {
                $connectOptions = array (
                        "replicaSet" => $this->_options ['replicaSet'],
                        "readPreference" => ReadPreference::RP_PRIMARY_PREFERRED 
                );
            }
            
            $this->_manager = new \MongoDb\Driver\Manager ( "mongodb://" . $this->_options ['dbuser'] . ":" . $this->_options ['dbpassword'] . "@" . $this->_options ['dbhost'] . '/' . $this->_options ['database'], $connectOptions );
            
            $this->_database = new Database($this->_manager, $this->_options ['database']) ;
            
            $this->_retry = 0;
           
            
        }
        catch (\Exception $e) {
            
            $this->_retry ++;
            if($this->_retry > self::RETRY){
                
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
     * Set the connection options
     *
     * Should be init in the system config, including host, user, psd,
     * database, etc.
     *
     * @version 1.0
     * @since  Version 1.0
     * @param connection options $options
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function setOptions($options){
    
        $this->_options= $options;
    }
    /**
     *
     * Get the collections based on collection name. Not supported anymore
     *
     * @version 1.4
     * @since  Version 1.0
     * @param string $collection collection name
     * @return boolean|\MongoCollection
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     * @deprecated
     */
    public function getCollection($collection)
    {
        $command = Operation::getCollection($collection);
        return $this->executeCommand($command);
    }

    /**
     *
     * List the collection name of the DB
     *
     * @version 1.4
     * @since  Version 1.0
     * @return boolean|Mongo collections array
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function listCollectionNames() {
        $command = Operation::listCollection();
        $cursor = $this->executeCommand($command);
         
        if ($cursor) {
            $result = array();
            foreach($cursor as $item){
                $result[]= $item->name;
            }
            return $result;
        }
    
        return false;
    }
    
    /**
     * 
     * Function description goes here
     *
     * @since  Version 
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function pingServer() {
        $command = Operation::pingServer();
        $cursor= $this->executeCommand($command);
        if ($cursor ) {
           return true;
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
        $collection = String::underscore($collection);
    	$command = Operation::dropCollection($collection);
    	return $this->executeCommand($command);
    	
    }
    
    /**
     * 
     * Execute a command in the database
     *
     * @version 1.4
     * @since  Version 1.4
     * @param \MongoDB\Driver\Command $command
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function executeCommand($command){
    	if ($this->isConnected()) {
    		try{
    		    $cursor = $this->_manager->executeCommand($this->_database->getDatabaseName(), $command);
    		    return $cursor;
    		} catch(\Exception $e){
    		    return false;
    		}
    	}
    	
    	return false;
    }
    
    /**
     * 
     * Execute a bulk write operation
     *
     * @version 1.4
     * @since  Version 1.4
     * @param string $collection collection name
     * @param \MongoDB\Driver\BulkWrite $bulk
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function executeBulkWrite($collection, $bulk){
    	if ($this->isConnected()) {
    	    try{
    	        $collection = String::underscore($collection);
    	        $result = $this->_manager->executeBulkWrite($this->_database->getDatabaseName(). '.'. $collection, $bulk);
    			return $result;
    		} catch(BulkWriteException $ee) {
    		    $result = $ee->getWriteResult();
    		    foreach($result->getWriteErrors() as $writeError){
    		        if ($writeError->getCode() =='11000'){
    		            throw  new DbException(DbException::DBERROR_DUPLICATE_KEY);
    		        }
    		    }
    		    return false;
    		} catch(\Exception $e){
    		    return false;
    		}
    	}
    	return false;
    }
    
    /**
     * 
     * Execute a database query
     *
     * @version 1.4
     * @since  Version 1.4 
     * @param unknown $collection
     * @param \MongoDB\Driver\Query $query
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function executeQuery($collection, $query){
        if ($this->isConnected()) {
            try{
                $collection = String::underscore($collection);
                $cursor = $this->_manager->executeQuery($this->_database->getDatabaseName(). '.'. $collection, $query);
                return $cursor;
            } catch(\Exception $e){
                throw new DbException(DbException::DBERROR_QUERY_FAIL);
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * 
     * Insert or update a document in the database collection
     * 
     * @version 1.4
     * @since  Version 1.4
     * @param string $collection collection name
     * @param object|array $document document to insert or update
     * @return boolean|\MongoDB\Driver\WriteResult
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function insert($collection, &$document){
        $bulk = new BulkWrite(['ordered'=>true]);
       
        if(is_array($document)){
            if (isset($document['_id'])){
                $_id = $document['_id'];
                $copy_of_document = array();
                foreach ($document as $k => $v) {
                    if($k !=='_id'){
                        $copy_of_document[$k] = clone $v;
                    }
                }               
                $bulk->update([
                        '_id' => $_id
                ], [
                        '$set' => $copy_of_document
                ],[ 'upsert' => true]);
            } else {
                $document['_id'] = new \MongoDB\BSON\ObjectId();
                $bulk->insert($document);
              
            }
        } else {
            if (isset($document->_id)) {
                $_id = $document->_id;
                $copy_of_document = clone $document;
                unset($copy_of_document->_id);
                $bulk->update([
                        '_id' => $_id
                ], [
                        '$set' => $copy_of_document
                ],[ 'upsert' => true]);
            } else {
                $document->_id = new \MongoDB\BSON\ObjectId();
                $bulk->insert($document);
            }
            
        }
        
        return $this->executeBulkWrite($collection, $bulk);
        
    }
    
    
    public function update($collection, $filter, $newData, $options){
        $bulk = new BulkWrite(['ordered'=>true]);
        $bulk->update($filter, $newData, $options);
        
        return $this->executeBulkWrite($collection, $bulk);
    }
    
    /**
     * 
     * Delete records based on the creteria
     *
     * @version 1.4
     * @since  Version 1.4
     * @param string $collection collection name
     * @param array $criteria delete creteria
     * @return boolean|\MongoDB\Driver\WriteResult
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function delete($collection, $criteria){
        $bulk = new BulkWrite();
        $limit = 1;
        $bulk->delete($criteria, ['limit'=>0]);
        return $this->executeBulkWrite($collection, $bulk);
    }
    
    public function getDatabase(){
        return $this->_database;
    }

    
     
    /**
     *
     * Test if the database is connected
     *
     * If there is no connection, they try to reconnect, otherwise, a exception
     * will be thrown.
     *
     * @version 1.4
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
        if (empty($this->_manager)) {
            $this->connect();
            
        } else {
            $server = $this->_manager->getServers();
            if (empty($server)){
                $this->connect();
            }
        }
        return true;
    }
}

/* ===============================================================
   End of DriverMongo.php
   =============================================================== */
