<?php
// [Example] http://your_server/test/test_mq_send_msg.php?taskid=5614e4268e9e5ab016eb88ef

require_once(dirname(__FILE__) . '/../inc.php');
require_once(dirname(__FILE__) . '/../lib/php-amqplib/vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

global $LAS_CFG;

$task_id = (isset($_REQUEST['taskid'])) ? $_REQUEST['taskid'] : null;

if (!empty($task_id)){
	$mq_settings = new \stdClass;
	$mq_settings->host          = $LAS_CFG->message_server['host'];
	$mq_settings->port          = $LAS_CFG->message_server['port'];
	$mq_settings->username      = $LAS_CFG->message_server['username'];
	$mq_settings->password      = $LAS_CFG->message_server['password'];
	$mq_settings->vhost         = $LAS_CFG->message_server['vhost'];
	$mq_settings->timeout       = $LAS_CFG->message_server['timeout'];
	$mq_settings->exchange_name = $LAS_CFG->message_server['taskQueue'];
	$mq_settings->callback      = $LAS_CFG->message_server['ackResultQueue'];
	
	$connection = new AMQPStreamConnection(
			$mq_settings->host,
			$mq_settings->port,
			$mq_settings->username,
			$mq_settings->password,
			$mq_settings->vhost
	);
	$channel = $connection->channel();
	
	$channel->queue_declare($mq_settings->callback, false, true, false, false);
	
	$data = new \stdClass;
	$data->success = true;
	$data->task_id = $task_id;
	$data = json_encode($data);
	$msg = new AMQPMessage($data);
	
	$channel->basic_publish($msg, '', $mq_settings->callback);
	echo "Sent = ", $data, "\n";
	
	$channel->close();
	$connection->close();
}
else{
	echo "Please provide taskid. \n";
}
?>