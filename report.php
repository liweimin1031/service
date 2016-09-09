<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : report.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS Report API entry page
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6048 $)
 * CREATED     : 18-NOV-2015
 * LASTUPDATES : $Author: patrickw $ on $Date: 2014-12-08 17:58:16 +0800 (Mon, 08 Dec 2014) $
 * UPDATES     : 
 * NOTES       : 
 */
/* ---------------------------------------------------------------
   @(#)report.php        1.0 		18-NOV-2015
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
   Begin of report.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__FILE__) . '/inc.php');

use Las\Core\Oauth\LasOauthServer;
use Las\Core\Util\Ajax;
use Las\Core\Util\AccessToken;
use Las\Core\Task\TaskManager;

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
if ( isset($_SERVER['PATH_INFO']) ) {
	switch ($_SERVER['PATH_INFO']) {
		case '/GetReportUrl':
			// Check Oauth
			$oauthUtil = new LasOauthServer();
			$token_data = $oauthUtil->getAccessTokenData();
			
			if (!$token_data) {
				header('HTTP/1.0 401 Unauthorized');
				exit();
			}
			else {
				$caller = $token_data->client_name;
			}
			
			$data = false;
			
			if ( isset($_REQUEST['data']) ) {
				$data = $_REQUEST['data'];
			}
			
			$json = Ajax::parseJson($data);
			
			if ( !empty($json->task_id) ){
				$taskId = $json->task_id;
				
				$accessToken = new AccessToken();
				$tokenObj = $accessToken->createToken($taskId);
				
				if ($tokenObj !== false){
					$reportUrl = $LAS_CFG->wwwroot . '/report/GetReport?token=';
					
					$result = new \stdClass();
					$result->status = $tokenObj->status;
					
					if ($tokenObj->status === TaskManager::TASK_STATUS_FINISH){
						$result->url = $reportUrl . $tokenObj->token;
					}
					else {
						$result->url = "";
					}
					
					echo Ajax::createDataMsg($result);
					exit;
				}
				
				// Error: Invalid task id
				echo Ajax::createErrorMsgByCode(LAS_ERROR_TASK_ID_INVALID);
				exit;
			}
			
			// Error: Missing task id
			echo Ajax::createErrorMsgByCode(LAS_ERROR_TASK_ID_INVALID);
			exit;
			
		case '/GetReport':
			if ( !empty($_REQUEST['token']) ) {
				$token = $_REQUEST['token'];
				
				$tokenObj = AccessToken::validateToken($token);
				
				if ($tokenObj !== false){
					AccessToken::deleteToken($token);
					
					$taskId = $tokenObj->taskid;
					$module = strtolower($tokenObj->module);
						
					global $LAS_CFG;
					$moduleLib = $LAS_CFG->modules_root . '/' . $module . '/lib/lib_las.php';
					$moduleFunc = 'getReport';
					
					if (file_exists($moduleLib)){
						require_once($moduleLib);
					
						if (function_exists($moduleFunc)){
							$moduleFunc($taskId);
							exit;
						}
					}
					
					// Error: Server error
					echo Ajax::createErrorMsgByCode(LAS_ERROR_ESERVER);
					exit;
				}
				else {
					header('HTTP/1.0 401 Unauthorized');
					exit();
				}
			}
			
			// Error: Missing argument
			echo Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
			exit;
			
		default:
			// Error: Invalid argument
			echo Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
			exit;
	}
}

// Error: No PATH_INFO
echo Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
exit;

/* ===============================================================
   End of report.php
   =============================================================== */
?>
