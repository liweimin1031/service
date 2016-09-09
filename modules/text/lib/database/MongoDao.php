<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : MongoDao.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Database Access Object for Mongo database
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 2376 $)
 * CREATED     : Feb 5, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2014-11-18 17:05:20 +0800 (Tue, 18 Nov 2014) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) MongoDao.php              1.0 Feb 5, 2013
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
   Begin of MongoDao.php
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
 * Database Access Object for Mongo database
 *
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class MongoDao
{

    /**
     *
     * Save a object to database
     *
     * We use the native mongodb <b>_id</b> as a primary key. If "_id" exists,
     * then update the record, otherwise insert.
     *
     * All the private or protected field are note saved.
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection Collection name
     * @param object $object Object to save (If an object is used, it may not
     *                       have protected or private properties)
     * @return mixed
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function save($collection, $object)
    {
        //$result= self::getClassPublicProperty($object);

        if (is_object($object) && !isset($object->_id)) {
            unset($object->_id);
        }

        if (is_array($object) && !isset($object['_id'])) {
            unset($object['_id']);
        }

        $driver = DriverMongo::getInstance();

        $collections = $driver->getCollection($collection);

        $collections->save($object);
        
        /*if (is_object($object)) {
            $object->_id= $result['_id'];
        }

        if (is_array($object)) {
            $object['_id']= $result['_id'];
        }*/

        /*$collectionName = String::underscore($collection);
        $db= new DriverMongo();
        $collections= $db->$collectionName;

        $id =null;
        if (is_object($object) && isset($object->_id)) {
            $id=$object->_id;
        }

        if (is_array($object) && isset($object['_id'])) {
            $id=$object['_id'];
        }

        if (empty($id)) {
            $result= $collections->insert($object);

            return $result['_id'];
        } else {
            $criteria= array('_id'=> $id);
            $options=  array("upsert" => true);
            return $collections->update($criteria, $object, $options);
        }*/

    }

    /**
     *
     * List all collection names of the DB
     *
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Ming Hua
     * @testing
     * @warnings
     * @updates
     */
    public static function listCollectionNames()
    {
        $driver = DriverMongo::getInstance();

        $collections = $driver->listCollectionNames();

        return $collections;
    }

    /**
     *
     * Drop collection by name
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection Collection name
     * @see
     * @author      Ming Hua
     * @testing
     * @warnings
     * @updates
     */
    public static function dropCollection($collection)
    {
        $driver = DriverMongo::getInstance();

        return $driver->dropCollection($collection);
    }


    /**
     *
     * Remove an object based on MongoID
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection Collection name
     * @param string $id MongoDB id
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function deleteById($collection, $id)
    {
        $creteria = array('_id' => new \MongoId($id));
        return MongoDao::deleteList($collection, $creteria);
    }
    /**
     *
     * Delete record from mongodb
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection Collection name
     * @param array $criteria Deleted criteria
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function deleteList($collection, $criteria)
    {
        $collections= DriverMongo::getInstance()->getCollection($collection);

        return $collections->remove($criteria);
    }


    /**
     *
     * Add index of collection
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection
     * @param string|array $key key name or an array of keys
     * @param array $options options
     * @see \MongoCollection::ensureIndex
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function ensureIndex($collection, $key, $options=array())
    {
        $collections= DriverMongo::getInstance()->getCollection($collection);

        return $collections->ensureIndex($key, $options);
    }


    /**
     *
     * Remove all the indexes of a collection
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection collection name
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function dropIndexs($collection)
    {
        $collections= DriverMongo::getInstance()->getCollection($collection);

        return $collections->deleteIndexes();
    }

    /**
     *
     * Search one record from database
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection Collection name
     * @param array $query Search criteria
     * @param array $fields Fields to return
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function searchOne($collection, $query, $fields= array())
    {
       $collections= DriverMongo::getInstance()->getCollection($collection);
       return $collections->findOne($query, $fields);

    }


    /**
     *
     * Find a record and update its value
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection collection name
     * @param array $query findAndModify
     * @param array $update The update criteria.
     * @param array $fields Optionally only return these fields.
     * @param array $options An array of options to apply, such as remove the match 
     *                         document from the DB and return it.
     * @see \MongoCollection::findAndModify
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function findAndModify ($collection, $query,  $update, $fields=array(), $options=array())
    {
        $collections= DriverMongo::getInstance()->getCollection($collection);

        return $collections->findAndModify($query,  $update, $fields, $options);
    }
    
    public static function update($collection, $query, $newObj, $options = array()){
        $collections= DriverMongo::getInstance()->getCollection($collection);
        return  $collections -> update($query, $newObj, $options);
    }

    /**
     *
     * Search on item from Mongodb based on mongoID
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string  $collection collection name
     * @param string $id MongoDB id {$id}
     * @param array $fields fields to return
     * @return array|null Returns record matching the search in array format of null
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function searchOneById($collection, $id , $fields= array())
    {
         $query = array('_id' => new \MongoId($id));
         return self::searchOne($collection, $query, $fields);
    }
    /**
     *
     * Find all the record based on the search criteria
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection Collection name
     * @param array $query Search criteria
     * @param integer $offset Search offset
     * @param integer $limit Search limit
     * @param array $sortBy Sorting information
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function search($collection, $query, $offset = 0, $limit = 0,
        $sortBy = null)
    {
        $collections = DriverMongo::getInstance()->getCollection($collection);

        $cursor = $collections->find($query);

        if (!empty($sortBy)) {
            $cursor->sort($sortBy);
        }

        if ($offset !== 0) {
            $cursor->skip($offset);
        }

        if ($limit !== 0) {
            $cursor->limit($limit);
        }
        return $cursor;


    }


    /**
     * 
     * Counter the number of result based on query criteria
     * @version 1.0
     * @since  Version 1.0
     * @param string $collection collection name
     * @param array $query search criteria
     * @return number
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function count($collection, $query)
    {
        $collections = DriverMongo::getInstance()->getCollection($collection);

        return $collections->count($query);

    }

    /**
     *
     * Get the public property of the object
     *
     * @version 1.0
     * @since  Version 1.0
     * @param object $object an object
     * @return multitype:mixed multitype:NULL mixed
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private static function getClassPublicProperty($object)
    {

        $ref = new \ReflectionObject($object);
        $pros = $ref->getProperties(\ReflectionProperty::IS_PUBLIC);
        $result = array();
        foreach ($pros as $pro) {
            false && $pro = new \ReflectionProperty();

            if ($pro->getName() === '_id' && $pro->getValue($object)===null) {
                continue;
            }

            if (is_object($pro->getValue($object))) {
                $result[$pro->getName()] = self::getClassPublicProperty(
                    $pro->getValue($object)
                );
            } else {
                $result[$pro->getName()] = $pro->getValue($object);
            }

        }

        return $result;

    }
}

/* ===============================================================
   End of MongoDao.php
   =============================================================== */
