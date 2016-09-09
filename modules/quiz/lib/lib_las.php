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
use Las\Core\Util\Util;
use Las\Core\File\FileManager;
use Las\Core\File\FileException;
use Las\Core\Util\AccessToken;
use Las\Core\Task\TaskResultManager;
use Las\Core\Util\ExcelParser;

require_once($LAS_CFG->portal_root.'/lib/las_excel.php');
/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */
$LAS_QUIZ_API = array();

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */
define('LAS_QUIZ_HADOOP_PATH',  'quiz/');

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */

/**
 * This function is to upload files to Hadoop
 *
 * @since       Version         1.0.00
 * @param       object          $data     data object with task information
 * @param       object          $file     files to upload, please use _FILES
 * @return      
 *              
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function uploadFile($data, $file)
{
    // @todo step1: upload file to Hadoop
    $task_id = $data->task_id;
    foreach($file as $fv) {
        $fp = (object)$fv;
        break;
    }
    $excelParser = new ExcelParser();
    $qs = $excelParser->xls2obj('/tmp/'.$fp->name, 'questions', array('question_id','dimension','difficulty'));
    $tokens = array('student', 'gender', 'group','subgroup');
    if(!$qs->success) {
        return Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL_FILE);
    }
    foreach($qs->data as $q) {
        $tokens[] = $q['question_id'];
    }
    $result = $excelParser->xls2obj('/tmp/'.$fp->name, 'result', $tokens);
    if(!$result->success) {
        return Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL_FILE);
    }
    $qData = new \stdClass;
    $qData->questions = $qs->data;
    $comFlds = array('student', 'gender', 'group','subgroup');
    $qData->result = [];
    foreach($result->data as $d) {
        if(empty($d['student'])) {
            continue;
        }
        $rst = new \stdClass;
        foreach($comFlds as $f) {
            if(!empty($d[$f])) {
                $rst->$f = $d[$f];
            }
        }
        $rst->items = [];
        foreach($qs->data as $q) {
            $rst->items[] = $d[$q['question_id']];
        }
        $qData->result[] = $rst;
    }
    //should parse file to json txt
    //var_dump(json_encode($qData));
    $file_manager = new FileManager();
    $result = $file_manager->createFileWithData($task_id.'.txt', json_encode($qData), LAS_QUIZ_HADOOP_PATH);
    
    if ($result instanceof FileException){
        if ($result->getCode() === FileException::FILE_ALREADY_EXISTS){
            return Ajax::createErrorMsgByCode(LAS_ERROR_EEXIST);
        }
        else if ($result->getCode() === FileException::FILE_NOT_FOUND){
            return Ajax::createErrorMsgByCode(LAS_ERROR_ENOENT);
        }
        else if ($result->getCode() === FileException::PERMISSION_DENIED){
            return Ajax::createErrorMsgByCode(LAS_ERROR_EACCES);
        }
        else {
            return Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL_FILE);
        }
    }
    else if ($result === false){
        return Ajax::createErrorMsgByCode(LAS_ERROR_ESERVER);
    }
    else {
        return Ajax::createDataMsg($result);
    }
}

/**
 * This function is to get result
 *
 * @since       Version         1.0.00
 * @param       object          $taskObj          object of task
 * @return
 */
/*
 * @author      Yunzhao Lu
 * @testing
 * @warnings
 * @updates
 */
function getResult($taskObj)
{
    $taskId = $taskObj->_id->{'$id'};

    $taskResultManager = new TaskResultManager($taskId);

    $result = new \stdClass;
    //$result->task_id = $taskId;
    //$result->status = $taskObj->status;
    //$result->input = $taskObj->input;

    if ($taskResultManager !== false){
        $result = $taskResultManager->output->data;
        if($taskResultManager->progress->percentage == 100) {
            $result->status = 100;
        } else {
            $result->status = 201;
        }
        if(!empty($taskResultManager->error)) {
            $result->error = $taskResultManager->error;
            $result->error->reason = $taskResultManager->error->message;
        }
    }
    else {
        $result->error = null;
    }

    return $result;

}

/**
 * This function is to get report
 *
 * @since       Version         1.0.00
 * @param       string          $taskId          task id
 * @return
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
function getReport($taskId)
{
    //require_once(dirname(__FILE__) . '/../../../inc.php');
    global $LAS_CFG;
    require_once($LAS_CFG->portal_root . '/lib/las_smarty.php');
    $rSmarty = las_init_smarty($LAS_CFG->reporting_root);
    $rSmarty->error_reporting = E_ALL & ~E_NOTICE;
    //$rSmarty->assign('task_id', $taskId);
    $atk = new AccessToken();
    $token = $atk->createToken($taskId);
    $rSmarty->assign('data_token', $token->token);

    $rSmarty->display('tpl/report.tpl');

    return true;
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
	global $LAS_QUIZ_API;

	if ( $name && $url && $msgtype ) {
		$entry = new stdClass;

		$entry->url = $url;
		$entry->msgtype = $msgtype;
		$entry->rettype = $rettype;

		$LAS_QUIZ_API[$name] = $entry;
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
	global $LAS_CFG, $LAS_QUIZ_API;
	$las_api = $LAS_CFG->wwwroot . '/api/quiz';
		
	addInterface(
		'GetAPIs',
		$las_api . '/GetAPIs'
	);
		
	addInterface(
		'DoRaschModelAnalytics',
		$las_api . '/DoRaschModelAnalytics'
	);
		
	$GLOBALS['LAS_QUIZ_API'] = $LAS_QUIZ_API;
		
	if ( !empty($name) ) {
		if ( array_key_exists($name, $LAS_QUIZ_API) ) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		return json_encode($LAS_QUIZ_API);
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
