<?php
require_once(dirname(__FILE__) . '/../inc.php');

use Las\Core\MessageManager\LasManager;

global $LAS_CFG;

$daemon_host = $LAS_CFG->las_daemon_server['host'];
$daemon_port = $LAS_CFG->las_daemon_server['port'];
$daemon_url = 'tcp://'.$daemon_host.':'.$daemon_port;

$client_timeout = $LAS_CFG->las_daemon_server['timeout'];

$mq_settings = new \stdClass;
$mq_settings->host          = $LAS_CFG->message_server['host'];
$mq_settings->port          = $LAS_CFG->message_server['port'];
$mq_settings->username      = $LAS_CFG->message_server['username'];
$mq_settings->password      = $LAS_CFG->message_server['password'];
$mq_settings->vhost         = $LAS_CFG->message_server['vhost'];
$mq_settings->timeout       = $LAS_CFG->message_server['timeout'];
$mq_settings->exchange_name = $LAS_CFG->message_server['taskQueue'];
$mq_settings->callback      = $LAS_CFG->message_server['ackResultQueue'];

$task_id = '560258b38e9e5af7038b4571';

$messageObj = new \stdClass();
$messageObj->task_id = $task_id;
$messageObj->routing_key = 'elana.quiz.DoAnalytics';

$content = new \stdClass();
$content->task_id = $task_id;
$messageObj->content = $content;

$message = json_encode($messageObj);

$server = new LasManager($daemon_url, $mq_settings);
$server->clientSendMessage($message, $client_timeout);

?>