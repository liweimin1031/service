<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : command.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Jan 12, 2016
 * LASTUPDATES : $Author: csdhong $ on $Date: 4:37:42 PM Jan 12, 2016 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) command.php              1.0 Jan 12, 2016
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
   Begin of command.php
   =============================================================== */
namespace Clms\Tools\PhpDao\Mongo;


use MongoDB\Driver\Command;
use MongoDB\Driver\Query;
use MongoDB\Driver\WriteResult;
use MongoDB\Driver\Cursor;

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
class Operation{
	public static function dropCollection($collection){
		return new Command(['drop'=> $collection]);
	}
	
	public static function listCollection(){
		return new Command(['listCollections'=> 1]);
	}
	
	public static function getCollection($collection){
	    return new Command(['getCollection'=>$collection]);
	}
	
	
	public static function pingServer(){
		return new Command(['ping'=> 1]);
	}
	
	public static function createIndexes($collection, $indexes){
	    return new Command(['createIndexes'=> $collection, 'indexes'=> $indexes]);
	}
	
	/**
	 * 
	 * Generate a command for drop an index
	 *
	 * @since  Version 1.4
	 * @param string $collection collection name
	 * @param string $index index name
	 * @return \MongoDB\Driver\Command
	 * @see
	 * @author      Michelle Hong
	 * @testing
	 * @warnings
	 * @updates
	 */
	public static function dropIndexes($collection, $index){
	    return new Command(['dropIndexes'=> $collection, 'index'=> $index]);
	}
	
	
	
	/**
	 * 
	 * Function description goes here
	 *
	 * @version 1.4
	 * @since  Version 1.4
	 * @param array $filter query criteria
	 * @param array $options query options
	 * @param array $fields returned fields
	 * @see
	 * @author      Michelle Hong
	 * @testing
	 * @warnings
	 * @updates
	 */
	public static function find($filter= array(), $options = array(), $fields= array()){
	    if (!empty($fields)){
	        $projection = array();
	        foreach($fields as $field){
	            $projection[$field] = 1;
	        }
	        $options['projection'] = $projection;
	    }
	    return new Query($filter, $options);
	}
	
	
	/**
	 * 
	 * Get a count  command
	 *
	 * @version 1.4
	 * @since  Version 1,4 
	 * @param string $collection collection name
	 * @param array $filter count criteria
	 * @see
	 * @author      Michelle Hong
	 * @testing
	 * @warnings
	 * @updates
	 */
	public static function count($collection, $filter = array()){
	    return new Command(['count'=> $collection, 'query' => $filter]);
	}
	
	/**
	 * 
	 * Check if the command or query or bulkwrite is success
	 *
	 * @version 1.4
	 * @since  Version 1.4
	 * @param BulkWrite|Cursor $object
	 * @see
	 * @author      Michelle Hong
	 * @testing
	 * @warnings
	 * @updates
	 */
	public static function is_operation_success($object){
	    
	    if($object instanceof Cursor){
	        $result = $object->toArray()[0];
	        return $result->ok ==1;
	    } else if ($object instanceof WriteResult){
	        $error = $object->getWriteErrors();
	        if (empty($error)){
	            return true;
	        }
	        return false;
	    }
	    return false;
	}
}


/* ===============================================================
   End of command.php
   =============================================================== */
?>