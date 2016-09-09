<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : MessageQueueManager.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : Message queue manager
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6353 $)
 * CREATED     : 13-AUG-2015
 * LASTUPDATES : $Author: mhshi $ on $Date: 2013-02-27 11:20:37 +0800 (Wed, 27 Feb 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)MessageQueueManager.php                  1.0 				13-AUG-2015
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
   Begin of MessageQueueManager.php
   =============================================================== */
namespace Las\Core\MessageManager;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__FILE__) . '/../../../lib/php-amqplib/vendor/autoload.php');

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Exception\AMQPProtocolConnectionException;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;

use Las\Core\Util\Time;

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */
define("MQ_EXCHANGE_TYPE",     		"topic");

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */

class MessageQueueManager
{
	/**
     *
     * @var string correlation id
     * @version 1.0
     * @since Version 1.0
     */
    private $correlation_id = '';

    /**
     *
     * @var string output queue
     * @version 1.0
     * @since Version 1.0
     */
    private $callback_queue = '';

    /**
     *
     * @var object AMQP connection
     * @version 1.0
     * @since Version 1.0
     */
    private $connection;
    
    /**
     *
     * @var object AMQP connection channel
     * @version 1.0
     * @since Version 1.0
     */
	private $channel;
	
	/**
	 *
	 * @var object AMQP connection channel
	 * @version 1.0
	 * @since Version 1.0
	 */
	private $exchange_name;
	
	/**
	 *
	 * Message Queue Constructor
	 *
	 * @version 1.0
	 * @since  Version 1.0
	 * @return 
	 * @see
	 * @author      Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	function __construct(){
		global $LAS_CFG;
		
		$host          			= $LAS_CFG->message_server['host'];
		$port          			= $LAS_CFG->message_server['port'];
		$username      			= $LAS_CFG->message_server['username'];
		$password      			= $LAS_CFG->message_server['password'];
		$vhost         			= $LAS_CFG->message_server['vhost'];
		
		$this->exchange_name    = $LAS_CFG->message_server['taskQueue'];
		$this->callback_queue   = $LAS_CFG->message_server['ackResultQueue'];
		
		error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][Connect][Start]");
				
		try {
			$this->connection = new AMQPStreamConnection($host, $port, $username, $password, $vhost);
			
			error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][Connect][AMQPStreamConnection->channel]");
			$this->channel = $this->connection->channel();
			
			error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][Connect][AMQPStreamConnection->channel->exchange_declare]");
			$this->channel->exchange_declare(
					$this->exchange_name, 
					MQ_EXCHANGE_TYPE, 
					$passive = false, 
					$durable = true, 
					$auto_delete = false
			);
			
			error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][Connect][OK]");
		}
		catch (AMQPRuntimeException $e) {
			error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][Connect][Error: " . $e->getMessage() . " (Code: ". $e->getCode() .")]");
		}
		catch (AMQPProtocolConnectionException $e) {
			error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][Connect][Error: " . $e->getMessage() . " (Code: ". $e->getCode() .")]");
		}
	}
	
	/**
	 *
	 * Get Message Queue socket
	 *
	 * @version 1.0
	 * @since  Version 1.0
	 * @return
	 * @see
	 * @author      Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	function getSocket(){
		error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][getSocket]");
		return $this->connection->getSocket();
	}
	
	/**
	 *
	 * Publish message to Message Queue
	 *
	 * @version 1.0
	 * @since  Version 1.0
	 * @return
	 * @see
	 * @author      Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	function publish($messageObj) {
		error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][publish][Start][Task id: ".$messageObj->task_id."; Routing key: ".$messageObj->routing_key."]");
		
        $msg = new AMQPMessage(
            json_encode($messageObj->content),
            array(
            	'delivery_mode' => 2,
				'correlation_id' => $messageObj->task_id,
                'reply_to' => '/reply-queue/'.$this->callback_queue
			)
        );

        try {
        	$keyPaths = explode('.', $messageObj->routing_key);
        	
        	$binding_queue = strtolower($keyPaths[0]) . '.' . strtolower($keyPaths[1]);
        	$binding_key = $binding_queue . '.*';
        	
        	$this->channel->queue_declare(
        			$binding_queue,
        			$passive = false,
        			$durable = true,
        			$exclusive = false,
        			$auto_delete = false
        	);
        			
        	$this->channel->queue_bind($binding_queue, $this->exchange_name, $binding_key);
        	
	        $this->channel->basic_publish(
	        		$msg, 
	        		$this->exchange_name, 
	        		$messageObj->routing_key,
	        		$mandatory = true);
	        
	        error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][publish][OK][Task id: ".$messageObj->task_id."; Routing key: ".$messageObj->routing_key."]");
	        return true;
        }
        catch (\Exception $e) {
        	error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][publish][Error: " . $e->getMessage() . "][Task id: ".$messageObj->task_id."; Routing key: ".$messageObj->routing_key."]");
        	return false;
        }
    }
    
    /**
     *
     * Subscribe message from Message Queue
     *
     * @version 1.0
     * @since  Version 1.0
     * @return
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    function subscribe(){
    	error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][subscribe][Start]");
    	
    	$this->response = null;
    	
    	try {
	    	list($this->callback_queue, ,) = $this->channel->queue_declare(
	    			$this->callback_queue, 
	    			$passive = false, 
	    			$durable = true, 
	    			$exclusive = false, 
	    			$auto_delete = false
	    	);
	    	
	    	$this->channel->basic_consume(
	    			$this->callback_queue, 
	    			$consumer_tag = '', 
	    			$no_local = false, 
	    			$no_ack = false, 
	    			$exclusive = false, 
	    			$nowait = false, 
	    			array($this, 'response')
	    	);
	    	
	    	error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][subscribe][OK]");
    	}
    	catch (AMQPProtocolChannelException $e) {
    		error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][subscribe][Error: " . $e->getMessage() . "]");
		}
    }
    
    /**
     *
     * Get response from Message Queue
     * 
     * Message format receive from Spark
     * {
	 *    "success": true | false,
	 *    "task_id" : "55de123dac123df2143"
	 * }
     * 
     * @version 1.0
     * @since  Version 1.0
     * @return
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    function response($message) {
    	$message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

    	$result = json_decode($message->body);
    	$task_id = $result->task_id;
    	
    	error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][response][OK][Task id: ".$task_id."]");
    	
    	LasManager::mqReceiveMsg($result);
    }
    
    /**
     *
     * Wait for a response from the channel
     *
     * @version 1.0
     * @since  Version 1.0
     * @return
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    function wait(){
    	error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][wait]");
    	return $this->channel->wait();
    }
    
    /**
     *
     * Close Message Queue conection
     *
     * @version 1.0
     * @since  Version 1.0
     * @return
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
	function close(){
		error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][closing]");
		
		$this->channel->close();
		$this->connection->close();
		
		error_log("[".Time::getCurrentDateTime(true)."][LAS][MessageQueueManager][closed]");
	}
}

/* ===============================================================
   End of MessageQueueManager.php
   =============================================================== */
?>