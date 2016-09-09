<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : api.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS API entry page
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6048 $)
 * CREATED     : 10-AUG-2015
 * LASTUPDATES : $Author: patrickw $ on $Date: 2014-12-08 17:58:16 +0800 (Mon, 08 Dec 2014) $
 * UPDATES     : 
 * NOTES       : 
 */
/* ---------------------------------------------------------------
   @(#)api.php        1.0 		10-AUG-2015
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
   Begin of api.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__FILE__) . '/inc.php');

use Las\Core\Oauth\LasOauthServer;
use Las\Core\Util\Time;
use Las\Core\Util\Ajax;
use Las\Core\MessageManager\LasManager;
use Las\Core\Task\TaskManager;
use Las\Core\File\FileManager;

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */
$LAS_API = array();

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */
define('LAS_API_MSGTYPE_FILE',  'file');
define('LAS_API_MSGTYPE_FORM',  'form');
define('LAS_API_MSGTYPE_JSON',  'json');

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */

/**
 * Add interface to <code>LAS_API</code>
 *
 * @since       Version 1.0.00
 * @param       name            The interface name
 * @param       url             The interface endpoint
 * @param       msgtype         The message/argument format (json|form)
 * @param       rettype         The return value/message format (json|file)
 * @return      nil
 * @see
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function las_add_interface($name, $url, $msgtype='json', $rettype='json') {
	global $LAS_API;

	if ( $name && $url && $msgtype ) {
		$entry = new stdClass;

		$entry->url = $url;
		$entry->msgtype = $msgtype;
		$entry->rettype = $rettype;

		$LAS_API[$name] = $entry;
	}
}

/**
 * Get interface from <code>LAS_API</code>
 *
 * @since       Version 1.0.00
 * @param       name            The interface name
 * @return      
 * @see
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function las_get_interface($name = null) {
	global $LAS_CFG, $LAS_API;

	$las_api = $LAS_CFG->wwwroot . '/api';
	$report_api = $LAS_CFG->wwwroot . '/report';
	
	las_add_interface(
		'GetAPIs',
		$las_api . '/GetAPIs',
		LAS_API_MSGTYPE_JSON,
		LAS_API_MSGTYPE_JSON
	);
	
	las_add_interface(
		'UploadFile',
		$las_api . '/UploadFile',
		LAS_API_MSGTYPE_FILE,
		LAS_API_MSGTYPE_JSON
	);
	
	las_add_interface(
		'GetResult',
		$las_api . '/GetResult',
		LAS_API_MSGTYPE_JSON,
		LAS_API_MSGTYPE_JSON
	);
	
	las_add_interface(
		'GetReportUrl',
		$report_api . '/GetReportUrl',
		LAS_API_MSGTYPE_JSON,
		LAS_API_MSGTYPE_JSON
	);
	
	$GLOBALS['LAS_API'] = $LAS_API;
		
	if ( !empty($name) ) {
		if ( array_key_exists($name, $LAS_API) ) {
			return $LAS_API[$name]->url;
		}
	}
	else {
		return json_encode($LAS_API);
	}
}

/**
 * Send message to Message Queue server
 *
 * @since       Version 1.0.00
 * @param       object            Message object
 * @return      
 * @see
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function sendMessageToQueue($messageObj){
	global $LAS_CFG;
	
	$daemon_host = $LAS_CFG->las_daemon_server['host'];
	$daemon_port = $LAS_CFG->las_daemon_server['port'];
	$daemon_url = 'tcp://'.$daemon_host.':'.$daemon_port;
	
	$client_timeout = $LAS_CFG->las_daemon_server['timeout'];
	
	$exchange_name = $LAS_CFG->message_server['taskQueue'];
	$new_routing_key = $exchange_name . $messageObj->routing_key;
	$messageObj->routing_key = $new_routing_key;
	
	// Convert JSON message to String
	$message = json_encode($messageObj);
	
	$server = new LasManager($daemon_url);
	return $server->clientSendMessage($message, $client_timeout);
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

error_log("[".Time::getCurrentDateTime(true)."][LAS][API][Start]");
 
$oauthUtil = new LasOauthServer();
$token_data = $oauthUtil->getAccessTokenData();

if (!$token_data) {
	error_log("[".Time::getCurrentDateTime(true)."][LAS][API][Error: getAccessTokenData failed]");
	header('HTTP/1.0 401 Unauthorized');
	exit();
}
else {
	error_log("[".Time::getCurrentDateTime(true)."][LAS][API][getAccessTokenData success]");
	$caller = $token_data->client_name;
}

$data = false;

if ( isset($_REQUEST['data']) ) {
    $data = $_REQUEST['data'];
}

$json = Ajax::parseJson($data);

if ( isset($_SERVER['PATH_INFO']) ) {
	switch ($_SERVER['PATH_INFO']) {
		case '/GetAPIs':
			error_log("[".Time::getCurrentDateTime(true)."][LAS][API: GetAPIs][Start]");
			
			if ( !empty($_REQUEST['method']) ) {
				echo las_get_interface($_REQUEST['method']);
			}
			else {
				echo las_get_interface();
			}
			
			error_log("[".Time::getCurrentDateTime(true)."][LAS][API: GetAPIs][End]");
			exit;
			
		case '/UploadFile':
			error_log("[".Time::getCurrentDateTime(true)."][LAS][API: UploadFile][Start]");
			
			if ( (!empty($_FILES)) && (!empty($json->task_id)) && (isset($json->finish_upload)) ){
				if ($json->finish_upload === 1){
					$isFinishUpload = true;
				}
				else if ($json->finish_upload === 0){
					$isFinishUpload = false;
				}
				else{
					// Error: finish_upload value invalid
					echo Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
					
					error_log("[".Time::getCurrentDateTime(true)."][LAS][API: UploadFile][Task id: ".$json->task_id."][Error: finish_upload value invalid]");
					exit;
				}
				
				$taskId = $json->task_id;
				$taskManager = new TaskManager($taskId);
				
				if ($taskManager !== false){
					$module = strtolower($taskManager->module);
					
					global $LAS_CFG;
					$moduleLib = $LAS_CFG->modules_root . '/' . $module . '/lib/lib_las.php';
					$moduleFunc = 'uploadFile';
					
					if (file_exists($moduleLib)){
						require_once($moduleLib);
						
						if (function_exists($moduleFunc)){
							$fileUploaded = $moduleFunc($json, $_FILES);
							$uploadResult = json_decode($fileUploaded);
							
							if ($uploadResult->success === false){
								// Error: Upload file failed
								echo $fileUploaded;
								
								error_log("[".Time::getCurrentDateTime(true)."][LAS][API: UploadFile][Task id: ".$json->task_id."][Error: Upload file failed]");
								exit;
							}
							
							// Update task files
							$path = $uploadResult->data;
							$fileUpdated = $taskManager->updateFile($path);
							
							if ($fileUpdated){
								if ($isFinishUpload){
									// File upload finish, update task status
									$statusUpdated = $taskManager->updateStatus(TaskManager::TASK_STATUS_PROCESSING);
							
									if ($statusUpdated !== false){
										// Send task to message queue
										$routingKey = str_replace('/', '.', $taskManager->api);
											
										$messageObj = new \stdClass();
										$messageObj->task_id = $taskId;
										$messageObj->routing_key = $routingKey;
							
										$content = new \stdClass();
										$content->task_id = $taskId;
										$messageObj->content = $content;
							
										$msg_sent = sendMessageToQueue($messageObj);
											
										if ($msg_sent !== false){
											$result = new \stdClass();
											$result->status = TaskManager::TASK_STATUS_FILE_ACCEPT;
											
											echo Ajax::createDataMsg($result);
											
											error_log("[".Time::getCurrentDateTime(true)."][LAS][API: UploadFile][Task id: ".$json->task_id."][OK]");
											exit;
										}
									}
								}
								else {
									// File upload not finish, no need to update task status
									$result = new \stdClass();
									$result->status = TaskManager::TASK_STATUS_FILE_ACCEPT;
							
									echo Ajax::createDataMsg($result);
									
									error_log("[".Time::getCurrentDateTime(true)."][LAS][API: UploadFile][Task id: ".$json->task_id."][OK]");
									exit;
								}
							}
							
							// Delete file if cannot update task files / status
							$file_manager = new FileManager();
							$file_manager->deleteFile($path);
							
							// Delete task files
							$taskManager->deleteFile($path);
							
							// Revert task status
							$taskManager->updateStatus(TaskManager::TASK_STATUS_NO_DATA_FILE);
						}
					}
					
					// Error: Server error
					echo Ajax::createErrorMsgByCode(LAS_ERROR_ESERVER);
					
					error_log("[".Time::getCurrentDateTime(true)."][LAS][API: UploadFile][Task id: ".$json->task_id."][Error: Server error]");
					exit;
				}
				
				// Error: Invalid task id
				echo Ajax::createErrorMsgByCode(LAS_ERROR_TASK_ID_INVALID);
				
				error_log("[".Time::getCurrentDateTime(true)."][LAS][API: UploadFile][Task id: ".$json->task_id."][Error: Invalid task id]");
				exit;
			}
			
			// Error: Missing argument
			echo Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
			
			error_log("[".Time::getCurrentDateTime(true)."][LAS][API: UploadFile][Error: Missing argument]");
			exit;
			
		case '/GetResult':
			error_log("[".Time::getCurrentDateTime(true)."][LAS][API: GetResult][Start]");
			
			if ( !empty($json->task_id) ){
				$taskId = $json->task_id;
				$taskManager = new TaskManager($taskId);
				
				if ($taskManager !== false){
					$module = strtolower($taskManager->module);
					
					global $LAS_CFG;
					$moduleLib = $LAS_CFG->modules_root . '/' . $module . '/lib/lib_las.php';
					$moduleFunc = 'getResult';
						
					if (file_exists($moduleLib)){
						require_once($moduleLib);
					
						if (function_exists($moduleFunc)){
							$result = $moduleFunc($taskManager);
							if ($result !== false){
								if ( empty($result->error) ){
									echo Ajax::createDataMsg($result);
									error_log("[".Time::getCurrentDateTime(true)."][LAS][API: GetResult][Task id: ".$json->task_id."][OK]");
								}
								else{
									$error = $result->error;
									unset($result->error);
									echo Ajax::createErrorMsg($error->message, $error->code, $result);
									error_log("[".Time::getCurrentDateTime(true)."][LAS][API: GetResult][Task id: ".$json->task_id."][Error: ".$error->message."]");
								}
								exit;
							}
						}
					}
					
					// Error: Server error
					echo Ajax::createErrorMsgByCode(LAS_ERROR_ESERVER);
					
					error_log("[".Time::getCurrentDateTime(true)."][LAS][API: GetResult][Task id: ".$json->task_id."][Error: Server error]");
					exit;
				}
				
				// Error: Invalid task id
				echo Ajax::createErrorMsgByCode(LAS_ERROR_TASK_ID_INVALID);
				
				error_log("[".Time::getCurrentDateTime(true)."][LAS][API: GetResult][Task id: ".$json->task_id."][Error: Invalid task id]");
				exit;
			}
			
			// Error: Missing task id
			echo Ajax::createErrorMsgByCode(LAS_ERROR_TASK_ID_INVALID);
			
			error_log("[".Time::getCurrentDateTime(true)."][LAS][API: GetResult][Error: Missing task id]");
			exit;
			
		case '/Test':
			error_log("[".Time::getCurrentDateTime(true)."][LAS][API: Test][Start][Key: ".$json->key."; Callback: ".$json->callback."]");
			
			$json->data = new \stdClass();
		
			if ( (!empty($json->key)) && (!empty($json->callback)) ){
				$apiPaths = preg_split('@/@', $_SERVER['PATH_INFO'], NULL, PREG_SPLIT_NO_EMPTY);
		
				$module = strtolower($apiPaths[0]);
					
				$key = $json->key;
				$callback = $json->callback;
		
				$task = new \stdClass();
				$task->module = $module;
				$task->api = $_SERVER['PATH_INFO'];
				$task->caller = $caller;
				$task->input = $json;
					
				// Create task to database
				$taskManager = new TaskManager(null, $task);
				$taskObj = $taskManager->createInstance();
		
				if ($taskObj !== false){
					$taskId = $taskObj->_id->{'$id'};
					$taskStatus = $taskObj->status;
		
					if ($taskStatus === TaskManager::TASK_STATUS_PROCESSING){
						$routingKey = str_replace('/', '.', $_SERVER['PATH_INFO']);
		
						$messageObj = new \stdClass();
						$messageObj->task_id = $taskId;
						$messageObj->routing_key = $routingKey;
		
						$content = new \stdClass();
						$content->task_id = $taskId;
						$messageObj->content = $content;
		
						// Send message to Message Queue server
						$result = sendMessageToQueue($messageObj);
		
						if ($result === false){
							// Error: Server error
							echo Ajax::createErrorMsgByCode(LAS_ERROR_ESERVER);
							
							error_log("[".Time::getCurrentDateTime(true)."][LAS][API: Test][Error: Server error][Key: ".$key."; Callback: ".$callback."]");
							exit;
						}
					}
		
					// return default task format
					$result = new \stdClass;
					$result->task_id = $taskId;
					$result->status = $taskStatus;
		
					echo Ajax::createDataMsg($result);
		
					error_log("[".Time::getCurrentDateTime(true)."][LAS][API: Test][OK][Key: ".$key."; Callback: ".$callback."; Task id: ".$taskId."]");
				}
				else {
					// Error: Database record insert fail
					echo Ajax::createErrorMsgByCode(LAS_ERROR_DB_EINSERT);
		
					error_log("[".Time::getCurrentDateTime(true)."][LAS][API: Test][Error: Cannot save to database][Key: ".$key."; Callback: ".$callback."]");
				}
		
				exit;
			}
		
			// Error: Invalid argument
			echo Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
		
			error_log("[".Time::getCurrentDateTime(true)."][LAS][API: Test][Error: Invalid argument]");
			exit;
				
		default:
			// Modules API
			$apiPaths = preg_split('@/@', $_SERVER['PATH_INFO'], NULL, PREG_SPLIT_NO_EMPTY);
			
			if ( !empty($apiPaths) && (count($apiPaths) > 1) ){
				$module = strtolower($apiPaths[0]);
				$apiName = $apiPaths[1];
			
				global $LAS_CFG;
				$moduleLib = $LAS_CFG->modules_root . '/' . $module . '/lib/lib_las.php';
				$moduleFunc = 'isInterfaceExist';
			
				if (file_exists($moduleLib)){
					require_once($moduleLib);
			
					if (function_exists($moduleFunc)){
						if ($moduleFunc($apiName)){
							if ($apiName === 'GetAPIs'){
								if ( function_exists('getInterface') ) {
									echo getInterface();
									exit;
								}
							}
							else {
								if (!empty($json)){
									if ( function_exists('getExistingTask') ) {
										$taskObj = getExistingTask($json);
									
										if ($taskObj !== false) {
											$taskId = $taskObj->_id->{'$id'};
											$taskStatus = $taskObj->status;
									
											// Return existing result
											if (function_exists('getExistingResult')){
												$result = getExistingResult($taskObj);
												if ($result !== false){
													echo Ajax::createDataMsg($result);
									
													// Use orginal task status to check
													if ($taskStatus === TaskManager::TASK_STATUS_FINISH){
														// Update existing record
														$routingKey = str_replace('/', '.', $_SERVER['PATH_INFO']);
									
														$messageObj = new \stdClass();
														$messageObj->task_id = $taskId;
														$messageObj->routing_key = $routingKey;
									
														$content = new \stdClass();
														$content->task_id = $taskId;
														$messageObj->content = $content;
									
														// Send message to Message Queue server
														sendMessageToQueue($messageObj);
													}
									
													exit;
												}
											}
											else {
												// Error: Server error
												echo Ajax::createErrorMsgByCode(LAS_ERROR_ESERVER);
												exit;
											}
										}
									}
									
									$task = new \stdClass();
									$task->module = $module;
									$task->api = $_SERVER['PATH_INFO'];
									$task->caller = $caller;
									$task->input = $json;
									
									// Create task to database
									$taskManager = new TaskManager(null, $task);
									$taskObj = $taskManager->createInstance();
									
									if ($taskObj !== false){
										$taskId = $taskObj->_id->{'$id'};
										$taskStatus = $taskObj->status;
											
										if ($taskStatus === TaskManager::TASK_STATUS_PROCESSING){
											$routingKey = str_replace('/', '.', $_SERVER['PATH_INFO']);
									
											$messageObj = new \stdClass();
											$messageObj->task_id = $taskId;
											$messageObj->routing_key = $routingKey;
									
											$content = new \stdClass();
											$content->task_id = $taskId;
											$messageObj->content = $content;
									
											// Send message to Message Queue server
											$result = sendMessageToQueue($messageObj);
									
											if ($result === false){
												// Error: Server error
												echo Ajax::createErrorMsgByCode(LAS_ERROR_ESERVER);
												exit;
											}
										}
											
										if ( function_exists('getCustomTaskFormat') ) {
											$result = getCustomTaskFormat($taskObj);
										}
										else{
											// return default task format
											$result = new \stdClass;
											$result->task_id = $taskId;
											$result->status = $taskStatus;
										}
									
										echo Ajax::createDataMsg($result);
										exit;
									}
									else {
										// Error: Database record insert fail
										echo Ajax::createErrorMsgByCode(LAS_ERROR_DB_EINSERT);
										exit;
									}
								}
							}
						}
						else{
							// Error: Invalid argument
							echo Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
							exit;
						}
					}
				}
			
				// Error: Server error
				echo Ajax::createErrorMsgByCode(LAS_ERROR_ESERVER);
				exit;
			}
			
			// Error: Invalid argument
			echo Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
			exit;
	}
}

// Error: No PATH_INFO
echo Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
exit;

/* ===============================================================
   End of api.php
   =============================================================== */
?>
