<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : lib_las.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS API
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6048 $)
 * CREATED     : 29-SEP-2015
 * LASTUPDATES : $Author: patrickw $ on $Date: 2014-12-08 17:58:16 +0800 (Mon, 08 Dec 2014) $
 * UPDATES     : 
 * NOTES       : 
 */
/* ---------------------------------------------------------------
   @(#)lib_las.php        1.0 		29-SEP-2015
   by Kary Ho


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */


/* ===============================================================
   Begin of lib_las.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
use Las\Core\Util\Ajax;
use Las\Core\File\FileManager;
use Las\Core\File\FileException;
use Las\Core\Task\TaskManager;
use Las\Core\Task\TaskResultManager;
use Las\Tools\Mongo\MongoDao;

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */
$LAS_TEXT_API = array();

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */

/**
 * This function is to return existing task
 *
 * @since       Version         1.0.00
 * @param       object          $data     object of task search field
 * @return
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function getExistingTask($data)
{
	$query = array();
	
	if ( isset($data->url) ){
		$url = $data->url;
		$query = array('input.url' => $url);
	}
	
	if ( !empty($query) ){
		$result = MongoDao::searchOne(TaskManager::TASK_COLLECTION, $query);
		
		if ( !empty($result) ) {
			$result = json_decode(json_encode($result));
			return $result;
		}
	}
	
	return false;
}

/**
 * This function is to get result
 *
 * @since       Version         1.0.00
 * @param       string          $taskObj     object of task
 * @return
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function getExistingResult($taskObj)
{
	$taskId = $taskObj->_id->{'$id'};

	$taskResultManager = new TaskResultManager($taskId);

	$result = new \stdClass;
	$result->task_id = $taskId;
	$result->status = $taskObj->status;
	$result->input = $taskObj->input;

	if ($taskResultManager !== false){
		if ( (empty($taskResultManager->error)) && (!empty($taskResultManager->output)) ) {
			$result->status = TaskManager::TASK_STATUS_FINISH;
			
			$result->progress = $taskResultManager->progress;
			$result->output = $taskResultManager->output;
			$result->error = $taskResultManager->error;
			
			return $result;
		}
		else if ( (!empty($taskResultManager->error)) && (empty($taskResultManager->output)) ){
			$result->status = TaskManager::TASK_STATUS_PROCESSING;
		}
	}
	
	$result->progress = null;
	$result->output = null;
	$result->error = null;

	return $result;
}

/**
 * This function is to return custom task format
 *
 * @since       Version         1.0.00
 * @param       object          $taskObj     object of task
 * @return
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function getCustomTaskFormat($taskObj)
{
	$taskId = $taskObj->_id->{'$id'};
	
	$taskResultManager = new TaskResultManager($taskId);
	
	$result = new \stdClass;
	$result->task_id = $taskId;
	$result->status = $taskObj->status;
	$result->input = $taskObj->input;
	
	if ($taskResultManager !== false){
		$result->progress = $taskResultManager->progress;
		$result->output = $taskResultManager->output;
	}
	else {
		$result->progress = null;
		$result->output = null;
	}

	return $result;
}

/**
 * This function is to get result
 *
 * @since       Version         1.0.00
 * @param       string          $taskObj     object of task
 * @return
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function getResult($taskObj)
{
	$taskId = $taskObj->_id->{'$id'};
	
	$taskResultManager = new TaskResultManager($taskId);
	
	$result = new \stdClass;
	$result->task_id = $taskId;
	$result->status = $taskObj->status;
	$result->input = $taskObj->input;
	
	if ($taskResultManager !== false){
		$result->progress = $taskResultManager->progress;
		$result->output = $taskResultManager->output;
		$result->error = $taskResultManager->error;
	}
	else {
		$result->progress = null;
		$result->output = null;
		$result->error = null;
	}
	
	return $result;
}

/**
 * This function is to add interface
 *
 * @since       Version         1.0.00
 * @param       string          $name            The method name
 * @param       string          $url             The method endpoint
 * @param       string          $msgtype         The message/argument format (json|form)
 * @param       string          $rettype         The return value/message format (json|file)
 * @return
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function addInterface($name, $url, $msgtype='json', $rettype='json') {
	global $LAS_TEXT_API;

	if ( $name && $url && $msgtype ) {
		$entry = new stdClass;

		$entry->url = $url;
		$entry->msgtype = $msgtype;
		$entry->rettype = $rettype;

		$LAS_TEXT_API[$name] = $entry;
	}
}

/**
 * This function is to get interface
 *
 * @since       Version         1.0.00
 * @param       string          $name        interface name
 * @return
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function getInterface($name = null){
	global $LAS_CFG, $LAS_TEXT_API;
	$las_api = $LAS_CFG->wwwroot . '/api/text';
		
	addInterface(
		'GetAPIs',
		$las_api . '/GetAPIs'
	);
		
 	addInterface(
 		'OpenRiceAnalysis',
 		$las_api . '/OpenRiceAnalysis'
 	);
    addInterface ( 'TextAnalysis', $las_api . '/TextAnalysis' );
    
    addInterface ( 'AirportAnalysis',
 	  $las_api . '/AirportAnalysis'
 	);
 	
 	addInterface(
 	        'OpenriceKeyAnalysis',
 	        $las_api . '/OpenriceKeyAnalysis'
 	        );
 	
 	addInterface(
 	    'SentimentAnalysis',
 	    $las_api . '/SentimentAnalysis'
 	);
		
	$GLOBALS['LAS_TEXT_API'] = $LAS_TEXT_API;
		
	if ( !empty($name) ) {
		if ( array_key_exists($name, $LAS_TEXT_API) ) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		return json_encode($LAS_TEXT_API);
	}
}

/**
 * This function is to check if interface exist
 *
 * @since       Version         1.0.00
 * @param       string          $name        interface name
 * @return
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function isInterfaceExist($name){
	if ( !empty($name) ){
		return getInterface($name);
	}
	return false;
}

/* ===============================================================
   End of lib_las.php
   =============================================================== */
?>
