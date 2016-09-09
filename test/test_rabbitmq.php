<?php
require_once(dirname(__FILE__) . '/../inc.php');
require_once(dirname(__FILE__) . '/../lib/php-amqplib/vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessageHandler {
	const EXCHANGE_TYPE = "topic";
	
	private $connection;
	private $channel;
	private $correlation_id;
	private $exchange_name;
	private $routing_key;
	private $message;
	private $callback_queue;
	private $response;
	
	
	public function __construct($constructObj = null) {
		echo "MessageHandler Called <br />\r\n";
		
		if (is_array($constructObj)) {
			$constructObj = (object) $constructObj;
		}
		
		if (isset($constructObj->correlation_id)) {
			$this->correlation_id = $constructObj->correlation_id;
		}
		
		if (isset($constructObj->exchange_name)) {
			$this->exchange_name = $constructObj->exchange_name;
		}
		
		if (isset($constructObj->routing_key)) {
			$this->routing_key = $constructObj->routing_key;
		}
		
		if (isset($constructObj->message)) {
			$this->message = $constructObj->message;
		}
	}
	
	public function onConnect(){
		echo "MessageHandler onConnect() Called <br />\r\n";

		global $LAS_CFG;
	
		$host = $LAS_CFG->message_server['host'];
		$port = $LAS_CFG->message_server['port'];
		$username = $LAS_CFG->message_server['username'];
		$password = $LAS_CFG->message_server['password'];
	
		$this->connection = new AMQPConnection($host, $port, $username, $password);
		$this->channel = $this->connection->channel();
	
		$this->channel->exchange_declare(
			$this->exchange_name, self::EXCHANGE_TYPE, false, false, false
		);
	
		list($this->callback_queue, ,) = $this->channel->queue_declare(
			"", false, false, true, false
		);
	
		$this->channel->basic_consume(
			$this->callback_queue, '', false, false, false, false, array($this, 'onResponse')
		);
	}
	
	// Callback function
	public function onResponse($rep) {
		echo "MessageHandler onResponse() Called <br />\r\n";
		
        if($rep->get('correlation_id') == $this->correlation_id) {
            $this->response = $rep->body;
        }
    }
	
	public function onPublish() {
		echo "MessageHandler onPublish() Called <br />\r\n";
		
        $this->response = null;

        $msg = new AMQPMessage(
            json_encode($this->message),
            array(
				'correlation_id' => $this->correlation_id,
                'reply_to' => $this->callback_queue
			)
        );
        $this->channel->basic_publish($msg, $this->exchange_name, $this->routing_key);
        
		while(!$this->response) {
            $this->channel->wait();
        }
		
        return $this->response;
    }
	
	public function onClose(){
		echo "<br />MessageHandler onClose() Called <br />\r\n";
		
		$this->channel->close();
		$this->connection->close();
	}
}

// $task_id = uniqid();

// $message = new \stdClass();
// $message->task_id = $task_id;

// $messageObj = new \stdClass();
// $messageObj->correlation_id = $task_id;
// $messageObj->exchange_name = 'elana';
// $messageObj->routing_key = 'elana.quiz.DoAnalytics';
// $messageObj->message = $message;

// $msg = new MessageHandler($messageObj);
// $msg->onConnect();
// $result = $msg->onPublish();
// var_dump($result);
// $msg->onClose();

?>