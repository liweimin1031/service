<?php
require_once(dirname(__FILE__) . '/../inc.php');

use Las\Core\Util\Time;
use Las\Core\Util\Ajax;

$log = "[".Time::getCurrentDateTime(true)."][LAS][Test][test_task_callback]";

$key = (isset($_REQUEST['key'])) ? $_REQUEST['key'] : null;
$task_id = (isset($_REQUEST['task_id'])) ? $_REQUEST['task_id'] : null;
$data = (isset($_REQUEST['data'])) ? $_REQUEST['data'] : null;

$auth_key = "1234567890";

if ( (empty($key)) || ($key !== $auth_key) ){
	$log .= "[Error: HTTP code 401 Unauthorized]";
	error_log($log);
	
	header('HTTP/1.0 401 Unauthorized');
	exit();
}
else if (empty($task_id)){
	$log .= "[Error: Task ID is missing]";
	error_log($log);
	
	echo Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
	exit();
}
else if (empty($data)){
	$log .= "[Error: Analytics data is missing]";
	error_log($log);
	
	echo Ajax::createErrorMsgByCode(LAS_ERROR_EINVAL);
	exit();
}

$log .= "[OK][Task id: ".$task_id.", Key: ".$key.", Analytics data: ".$data."]";
error_log($log);

echo Ajax::createDataMsg();
exit();
?>