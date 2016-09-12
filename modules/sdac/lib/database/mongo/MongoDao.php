<?php

/* --------------------------------------------------------------- */
/**
 * FILE NAME : MongoDao.php
 * AUTHOR : Michelle Hong
 * SYNOPSIS :
 * DESCRIPTION : Database Access Object for Mongo database
 * SEE ALSO :
 * VERSION : 1.0 ($Revision: 107 $)
 * CREATED : Feb 5, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2016-05-24 11:12:45 +0800
 * (Tue, 24 May 2016) $
 * UPDATES :
 * NOTES :
 */
/*
 * ---------------------------------------------------------------
 * @(#) MongoDao.php 1.0 Feb 5, 2013
 * by Michelle Hong
 *
 *
 * Copyright by ASTRI, Ltd., (ECE Group)
 * All rights reserved.
 *
 * This software is the confidential and proprietary information
 * of ASTRI, Ltd. ("Confidential Information"). You shall not
 * disclose such Confidential Information and shall use it only
 * in accordance with the terms of the license agreement you
 * entered into with ASTRI.
 * ---------------------------------------------------------------
 */

/*
 * ===============================================================
 * Begin of MongoDao.php
 * ===============================================================
 */

/*
 * ---------------------------------------------------------------
 * Included Library
 * ---------------------------------------------------------------
 */
namespace Clms\Tools\PhpDao\Mongo;

use Clms\Tools\PhpDao\Util\String;
use Clms\Tools\PhpDao\Exception\DbException;

/*
 * ---------------------------------------------------------------
 * Global Variables
 * ---------------------------------------------------------------
 */

/*
 * ---------------------------------------------------------------
 * Constant definition
 * ---------------------------------------------------------------
 */

/*
 * ---------------------------------------------------------------
 * Function definition
 * ---------------------------------------------------------------
 */

/**
 *
 * Database Access Object for Mongo database
 *
 * @version 1.0
 * @since Version 1.0
 * @see
 *
 * @author Michelle Hong
 *         @testing
 *         @warnings
 *         @updates
 */
class MongoDao {
    
    /**
     *
     * Save a object to database
     *
     * We use the native mongodb <b>_id</b> as a primary key. If "_id" exists,
     * then update the record, otherwise insert.
     *
     * All the private or protected field are note saved.
     *
     * @version 1.4
     * @since Version 1.0
     * @param string $collection Collection name
     * @param object $object Object to save (If an object is used, it may not
     *        have protected or private properties)
     * @return mixed
     * @see
     *
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function save($collection, &$object) {
        // $result= self::getClassPublicProperty($object);
        if (! is_object ( $object ) && ! is_array ( $object )) {
            throw new DbException ( DbException::DBERROR_INVALID_PARAMETER );
        }
        
        $driver = DriverMongo::getInstance ();
        $result = $driver->insert ( $collection, $object );
        
        if ($result && ($result->getInsertedCount () == 1 || $result->getMatchedCount () == 1)) {
            $error = $result->getWriteErrors ();
            if (empty ( $error ))
                return $object;
        }
        throw new DbException ( DbException::DBERROR_CREATE );
        return false;
    }
    
    /**
     *
     * List all collection names of the DB
     *
     * @version 1.0
     * @since Version 1.0
     * @see
     *
     * @author Ming Hua
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function listCollectionNames() {
        $driver = DriverMongo::getInstance ();
        
        $collections = $driver->listCollectionNames ();
        
        return $collections;
    }
    
    /**
     *
     * Drop collection by name
     *
     * @version 1.0
     * @since Version 1.0
     * @param string $collection Collection name
     * @see
     *
     * @author Ming Hua
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function dropCollection($collection) {
        $driver = DriverMongo::getInstance ();
        
        $cursor = $driver->dropCollection ( $collection );
        return Operation::is_operation_success ( $cursor );
    }
    
    /**
     *
     * Remove an object based on MongoID
     *
     * @version 1.0
     * @since Version 1.0
     * @param string $collection Collection name
     * @param string $id MongoDB id
     * @see
     *
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function deleteById($collection, $id) {
        $criteria = array (
                '_id' => new \MongoDB\BSON\ObjectId ( $id ) 
        );
        return MongoDao::deleteList ( $collection, $criteria );
    }
    /**
     *
     * Delete record from mongodb
     *
     * @version 1.0
     * @since Version 1.0
     * @param string $collection Collection name
     * @param array $criteria Deleted criteria
     * @see
     *
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function deleteList($collection, $criteria) {
        $driver = DriverMongo::getInstance ();
        
        $cursor = $driver->delete ( $collection, $criteria );
        return Operation::is_operation_success ( $cursor );
    }
    
    /**
     *
     * Add index of collection
     *
     * @version 1.4
     * @since Version 1.0
     * @param string $collection
     * @param string|array $key key name or an array of keys
     * @param array $options options
     * @see \MongoCollection::ensureIndex
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function ensureIndex($collection, $keys, $options = array()) {
        if (empty ( $keys )) {
            throw new DbException ( DbException::DBERROR_INVALID_PARAMETER );
        }
        $driver = DriverMongo::getInstance ();
        $result = MongoDao::listCollectionNames ();
        
        $indexes = array ();
        $index = array ();
        
        if (is_array ( $keys )) {
            $index ['name'] = implode ( '_', $keys );
            $index ['key'] = array ();
            foreach ( $keys as $key => $value ) {
                $index ['key'] [$key] = $value;
            }
        } else {
            $index ['name'] = $keys;
            $index ['key'] [$keys] = 1;
        }
        foreach ( $options as $key => $value ) {
            $index->$key = $value;
        }
        
        $driver = DriverMongo::getInstance ();
        $index ['ns'] = $driver->getDatabase ()->getDatabaseName () . '.' . $collection;
        $indexes [] = $index;
        $command = Operation::createIndexes ( $collection, $indexes );
        $cursor = $driver->executeCommand ( $command );
        if (Operation::is_operation_success ( $cursor )) {
            return true;
        } else {
            throw new DbException ( DbException::DBERROR_CANNOT_CREATE_INDEX );
        }
    }
    
    /**
     *
     * Add index of collection
     *
     * @version 1.4
     * @since Version 1.0
     * @param string $collection
     * @param string|array $key key name or an array of keys
     * @param array $options options
     * @see \MongoCollection::ensureIndex
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function ensureTextIndex($collection, $keys, $options = array()) {
        if (empty ( $keys )) {
            throw new DbException ( DbException::DBERROR_INVALID_PARAMETER );
        }
        $driver = DriverMongo::getInstance ();
        $result = MongoDao::listCollectionNames ();
    
        $indexes = array ();
        $index = array ();
    
        if (is_array ( $keys )) {
            $index ['name'] = implode ( '_', $keys );
            $index ['key'] = array ();
            foreach ( $keys as $key ) {
                $index ['key'] [$key] = 'text';
            }
        } else {
            $index ['name'] = $keys;
            $index ['key'] [$keys] = 'text';
        }
        foreach ( $options as $key => $value ) {
            $index->$key = $value;
        }
    
        $driver = DriverMongo::getInstance ();
        $index ['ns'] = $driver->getDatabase ()->getDatabaseName () . '.' . $collection;
        $indexes [] = $index;
        $command = Operation::createIndexes ( $collection, $indexes );
        $cursor = $driver->executeCommand ( $command );
        if (Operation::is_operation_success ( $cursor )) {
            return true;
        } else {
            throw new DbException ( DbException::DBERROR_CANNOT_CREATE_INDEX );
        }
    }
    
    /**
     *
     * Remove all the indexes of a collection
     *
     * @version 1.0
     * @since Version 1.0
     * @param string $collection collection name
     * @see
     *
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function dropIndexes($collection) {
        $driver = DriverMongo::getInstance ();
        $command = Operation::dropIndexes ( $collection, '*' );
        $cursor = $driver->executeCommand ( $command );
        
        return Operation::is_operation_success ( $cursor );
    }
    
    /**
     *
     * Search one record from database
     *
     * @version 1.0
     * @since Version 1.0
     * @param string $collection Collection name
     * @param array $query Search criteria
     * @param array $fields Fields to return
     * @param array $sort sort
     * @see
     *
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates add a field sort
     */
    public static function searchOne($collection, $query, $fields = array(), $sort = array()) {
        $driver = DriverMongo::getInstance ();
        $options = array (
                'limit' => 1 
        );
        if (! empty ( $sort )) {
            $options ['sort'] = $sort;
        }
        $query = Operation::find ( $query, $options, $fields );
        
        $cursor = $driver->executeQuery ( $collection, $query );
        
        if ($cursor) {
            foreach ( $cursor as $document ) {
                return $document;
            }
        }
        return false;
    }
    
    /**
     *
     * Find a record and update its value
     *
     * @version 1.4
     * @since Version 1.0
     * @param string $collection collection name
     * @param array $query findAndModify
     * @param array $update The update criteria.
     * @param array $fields Optionally only return these fields.
     * @param array $options An array of options to apply, such as remove the
     *        match
     *        document from the DB and return it.
     * @return the original document or modified document when new is et in
     *         options
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function findAndModify($collection, $query, $update, $fields = array(), $options = array()) {
        $driver = DriverMongo::getInstance ();
        $sort = array ();
        if (isset ( $options ['sort'] )) {
            $sort = $options ['sort'];
        }
        // get the old document
        $document = self::searchOne ( $collection, $query, $sort );
        
        if (empty ( $document )) {
            if (isset ( $options ['upsert'] ) && $options ['upsert'] && ! empty ( $update )) {
                $newData = new \stdClass ();
                foreach ( $query as $key => $value ) {
                    $newData->$key = $value;
                }
                if (isset ( $update ['$set'] )) {
                    foreach ( $update ['$set'] as $key => $value ) {
                        $newData->$key = $value;
                    }
                }
                if (isset ( $update ['$inc'] )) {
                    foreach ( $update ['$inc'] as $key => $value ) {
                        $newData->$key = $value;
                    }
                }
                $newObj = self::save ( $collection, $newData );
                
                $objectId = is_array ( $newObj ) ? $newObj ['_id'] : $newObj->_id;
                $query = array (
                        '_id' => $objectId 
                );
                $document = self::searchOne ( $collection, $query, $fields );
            } else {
                return null;
            }
        } else {
            // modify the document
            $objectId = is_array ( $document ) ? $document ['_id'] : $document->_id;
            self::update ( $collection, array (
                    '_id' => $objectId 
            ), $update );
        }
        
        if (isset ( $options ['new'] ) && $options ['new']) {
            // get the updated document
            $objectId = is_array ( $document ) ? $document ['_id'] : $document->_id;
            $query = array (
                    '_id' => $objectId 
            );
            $document = self::searchOne ( $collection, $query, $fields );
        }
        return $document;
    }
    
    /**
     * Update a document
     *
     * @version 1.4
     * @since Version 1.4
     * @param string $collection collection name
     * @param array $query
     * @param array|object $newObj updated field of the document.
     *        This may contain the update operator.
     * @param array $options update options. eg.g multiple or single.
     * @see
     *
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function update($collection, $query, $newObj, $options = array()) {
        $driver = DriverMongo::getInstance ();
        return $driver->update ( $collection, $query, $newObj, $options );
    }
    
    /**
     *
     * Search on item from Mongodb based on mongoID
     *
     * @version 1.4
     * @since Version 1.0
     * @param string $collection collection name
     * @param string $id MongoDB id {$id}
     * @param array $fields fields to return
     * @return array|null Returns record matching the search in array format of
     *         null
     * @see
     *
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function searchOneById($collection, $id, $fields = array()) {
        $query = array (
                '_id' => new \MongoDB\BSON\ObjectId ( $id ) 
        );
        return self::searchOne ( $collection, $query, $fields );
    }
    /**
     *
     * Find all the record based on the search criteria
     *
     * @version 1.4
     * @since Version 1.0
     * @param string $collection Collection name
     * @param array $query Search criteria
     * @param integer $offset Search offset
     * @param integer $limit Search limit
     * @param array $sortBy Sorting information
     * @param array $fields Returned collection collumn
     * @see
     *
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function search($collection, $query, $offset = 0, $limit = 0, $sortBy = null, $fields = array()) {
        $options = array ();
        if (! empty ( $offset )) {
            $options ['skip'] = $offset;
        }
        if (! empty ( $limit )) {
            $options ['limit'] = $limit;
        }
        if (! empty ( $sortBy )) {
            $options ['sort'] = $sortBy;
        }
        
        $driver = DriverMongo::getInstance ();
        $query = Operation::find ( $query, $options, $fields );
        return $driver->executeQuery ( $collection, $query );
    }
    
    /**
     *
     * Find all the record by filter and text search
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection Collection name
     * @param string $search Text search keyword
     * @param array $query query options
     * @param integer $offset Search offset
     * @param integer $limit Search limit
     * @param array $sortBy Sorting information
     * @see
     * @author      Sandy Wong
     * @testing
     * @warnings
     * @updates
     */
    public static function keywordSearch($collection, $search, $query, $offset = 0, $limit = 0, $sortBy= array()) {
    
        $query['$text'] = array('$search' => $search);
    
    
            $result = self::search($collection, $query, $offset, $limit, $sortBy);
    
    
            return $result;
    }
    
    /**
     *
     * Counter the number of result based on query criteria
     *
     * @version 1.0
     * @since Version 1.0
     * @param string $collection collection name
     * @param array $query search criteria
     * @return number
     * @see
     *
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    public static function count($collection, $query = array()) {
        $driver = DriverMongo::getInstance ();
        $command = Operation::count ( $collection, $query );
        $cursor = $driver->executeCommand ( $command );
        if ($cursor) {
            foreach ( $cursor as $document ) {
                if ($document->ok) {
                    return $document->n;
                } else {
                    return false;
                }
            }
        }
        return false;
    }
    /**
     * 
     * Count by keyword for text search 
     *
     * @since  Version 1.4
     * @param string $collection Collection name
     * @param string $search Text search keyword
     * @param array $query query options
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function keywordCount($collection, $search, $query){
        $query['$text'] = array('$search' => $search);
    
        return self::count($collection, $query);
    
    }
    public static function pingServer() {
        $driver = DriverMongo::getInstance ();
        
        return $driver->pingServer ();
    }
    
    /**
     *
     * Get the public property of the object
     *
     * @version 1.0
     * @since Version 1.0
     * @param object $object an object
     * @return multitype:mixed multitype:NULL mixed
     * @see
     *
     * @author Michelle Hong
     *         @testing
     *         @warnings
     *         @updates
     */
    private static function getClassPublicProperty($object) {
        $ref = new \ReflectionObject ( $object );
        $pros = $ref->getProperties ( \ReflectionProperty::IS_PUBLIC );
        $result = array ();
        foreach ( $pros as $pro ) {
            false && $pro = new \ReflectionProperty ();
            
            if ($pro->getName () === '_id' && $pro->getValue ( $object ) === null) {
                continue;
            }
            
            if (is_object ( $pro->getValue ( $object ) )) {
                $result [$pro->getName ()] = self::getClassPublicProperty ( $pro->getValue ( $object ) );
            } else {
                $result [$pro->getName ()] = $pro->getValue ( $object );
            }
        }
        
        return $result;
    }
}

/* ===============================================================
   End of MongoDao.php
   =============================================================== */
