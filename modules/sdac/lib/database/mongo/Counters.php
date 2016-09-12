<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Counters.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 1728 $)
 * CREATED     : May 20, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2014-02-25 14:38:57 +0800 (Tue, 25 Feb 2014) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) Counters.php              1.0 May 20, 2013
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
   Begin of Counters.php
   =============================================================== */

namespace Clms\Tools\PhpDao\Mongo;
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
   Class definition
   --------------------------------------------------------------- */

/**
 * 
 * Counter class for MongoDB
 *
 * This class is used to generated a counter in MongoDB based on collection name.
 * Each collection could have one counter. The counter could be used as the unique index
 * similar to mysql auto-incremental counter.
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class Counters
{
    /**
     * Collection name
     * @var string
     * @since  Version 1.0
     * @author      Michelle Hong
     * @updates
     */
    const COLLECTION= 'counters';

    /**
     * 
     * @var string mongodb index
     * @since  Version 1.0
     * @author      Michelle Hong
     * @updates
     */
    public $_id;

    /**
     * 
     * @var integer The real sequence number
     * @since  Version 1.0
     * @author      Michelle Hong
     * @updates
     */
    public $seq;

    /**
     * 
     * Counter constructor
     *
     * @since  Version 1.0
     * @param string $name collection name
     * @param integer $seq sequence value
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    function __construct($name, $seq)
    {
        $this->_id= $name;
        $this->seq= $seq;
    }
    
    /**
     * 
     * Init the counter based on collection name
     *
     * @since  Version  1.0
     * @param string $collection collection name
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function initCounter($collection)
    {
        $object= new Counters($collection, 0);

        $driver = DriverMongo::getInstance();
        
        $result = $driver->insert(self::COLLECTION, $object);
        
        if($result && ($result->getInsertedCount()==1 || $result->getUpsertedCount() ==1)){
            $error = $result->getWriteErrors();
            if (empty($error))
                return $object;
        } elseif ($result && $result->getMatchedCount()==1){
            $error = $result->getWriteErrors();
            if (empty($error))
                 return $object;
        }
        throw  new DbException(DbException::DBERROR_CREATE);
        return false;
    }

    /**
     * 
     * Get the next sequence number
     * 
     * Increase the sequence number of that collection and return it
     *
     * @since  Version 
     * @param string $name collection name
     * @return integer
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function getNextSequence($name)
    {

        $query= array('_id'=>$name);
        $update= array('$inc'=> array('seq'=>1));

        $result=MongoDao::findAndModify(self::COLLECTION, $query, $update, array(), array('new'=>true, 'upsert'=> true));
        $result = json_decode(json_encode($result));
        return $result->seq;
    }

}

/* ---------------------------------------------------------------
   Interface definition
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */



/* ===============================================================
   End of Counters.php
   =============================================================== */