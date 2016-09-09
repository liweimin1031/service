<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : TaskCallback.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS task callback
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6353 $)
 * CREATED     : 28-APR-2016
 * LASTUPDATES : $Author: mhshi $ on $Date: 2013-02-27 11:20:37 +0800 (Wed, 27 Feb 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)TaskCallback.php                  1.0 				10-AUG-2015
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
   Begin of TaskCallback.php
   =============================================================== */
namespace Las\Core\Task;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__FILE__) . '/../../../inc.php');

use Las\Core\Task\TaskResultManager;
use Las\Core\Util\Ajax;
use Las\Core\Util\Time;

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
 * Do task callback
 *
 * @since       Version 1.0.00
 * @param       data            task result object (task id, key & result)
 * @param       url             task callback url
 * @return
 * @see
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function doCallback($data, $url){
	global $LAS_CFG;

	error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskCallback][doCallback][Start][Task id :".$data->task_id."; URL: ".$url."]");

	$para = http_build_query($data);

	// Open connection
	$ch = curl_init();

	// Set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $para);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, $LAS_CFG->callback_timeout);  // in second

	// Execute POST
	$response_content = curl_exec($ch);

	error_log($response_content);

	// Total transaction time in seconds for last transfer
	$total_time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

	if ( !curl_errno($ch) ) {
		$iHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
		if ($iHttpCode !== 200) {
			$success = false;

			if ( $iHttpCode === 404 ) {
				$response_error = "HTTP code 404 Not Found";
			}
			else if ( $iHttpCode === 401 ) {
				$response_error = "HTTP code 401 Unauthorized";
			}
			else {
				$response_error = "HTTP code ".$iHttpCode;
			}
		}
		else{
			$success = true;
		}
	}
	else{
		$success = false;
		$response_error = curl_error($ch);
	}

	// Close connection
	curl_close($ch);

	$result = new \stdClass;
	$result->success = $success;

	if ($success){
		$result->data = $response_content;
	}
	else{
		$result->error = $response_error;
	}

	error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskCallback][doCallback][End][Task id :".$data->task_id."; URL: ".$url."; Total transaction time:".($total_time * 1000)."ms]");

	return $result;
}


/**
 * Program entry point
 *
 * @since       Version 1.0.00
 * @param       param1          Description about parameter goes here
 * @return      Description     about what will be returned (if any)
 * @see
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */

if ( !$argv ) {
	echo "Please run this script at server";
	exit;
}

global $LAS_CFG;

$taskId = $argv[1];
$key = $argv[2];
$url = $argv[3];
$success = $argv[4];

error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskCallback][Start][Task id :".$taskId."; URL: ".$url."]");

if ($success){
	$taskResultManager = new TaskResultManager($taskId);
	
	if ($taskResultManager !== false){
		if ( !empty($taskResultManager->error) ){
			// Error: Invalid argument
			$result = Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
		}
		else if ( (empty($taskResultManager->error)) && (!empty($taskResultManager->output)) ){
			$result = Ajax::createDataMsg($taskResultManager->output);
		}
	}
	else {
		// Error: Server error
		$result = Ajax::createErrorMsgByCode(LAS_ERROR_ESERVER);
	}
}
else {
	// Error: Server error
	$result = Ajax::createErrorMsgByCode(LAS_ERROR_ESERVER);
}

$para = new \stdClass;
$para->task_id = $taskId;
$para->key = $key;
$para->data = $result;

$count = $LAS_CFG->callback_retry + 1;

for ($i = 0; $i < $count; $i++){
	$callback_result = doCallback($para, $url);

	if ($callback_result->success){
		$content_length = 0;
		$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $callback_result->data));
			
		foreach ( $fields as $field ) {
			if (preg_match("/Content-Length: (\d+)/", $field, $matches)) {
				$content_length = (int) $matches[1];
			}
		}

		if (!empty($content_length)){
			$content_key = (count($fields)-1);

			$response_content = json_decode($fields[$content_key]);

			if (isset($response_content->success)){
				if ($response_content->success){
					$response_data = (isset($response_content->data)) ? $response_content->data : null;
					if (!empty($response_data)){
						error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskCallback][". ($i + 1) ."][OK][Task id :".$taskId."; Response: ".$fields[$content_key]."]");
					}
					else{
						error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskCallback][". ($i + 1) ."][OK][Task id :".$taskId."; Empty response]");
					}
						
					break;
				}
				else {
					$response_error = (isset($response_content->error)) ? $response_content->error : null;
					if (!empty($response_error)){
						error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskCallback][". ($i + 1) ."][Task id :".$taskId."; Error: ".$fields[$content_key]."]");
					}
					else{
						error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskCallback][". ($i + 1) ."][Task id :".$taskId."; Error: Unknown error]");
					}
				}
			}
		}
		else {
			error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskCallback][". ($i + 1) ."][OK][Task id :".$taskId."; Empty response]");
			break;
		}
	}
	else{
		error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskCallback][". ($i + 1) ."][Task id :".$taskId."; Error: ".$callback_result->error."]");
	}

	sleep($LAS_CFG->callback_retry_delay);
}

/* ===============================================================
   End of TaskCallback.php
   =============================================================== */
?>
