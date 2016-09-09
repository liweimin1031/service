<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : LasManager.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS message manager
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6353 $)
 * CREATED     : 13-AUG-2015
 * LASTUPDATES : $Author: mhshi $ on $Date: 2013-02-27 11:20:37 +0800 (Wed, 27 Feb 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)LasManager.php                  1.0 				13-AUG-2015
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
   Begin of LasManager.php
   =============================================================== */
namespace Las\Core\MessageManager;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
use Las\Core\Util\Ajax;
use Las\Core\Util\Time;
use Las\Core\Task\TaskManager;

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */
define("LAS_MSGTYPE_REQUEST",     0);
define("LAS_MSGTYPE_RESPONSE",    1);
define("LAS_MSGTYPE_ERROR",       2);

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */

/**
 *
 * The common libraries of all LAS message.
 *
 * All the LAS modules should extend this class. 
 * The class defines common properties and functions of a task.
 *
 * For those modules which have special requirements, 
 * please override functions in extended class.
 
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */

class LasManager
{
	private $url;
	
	private $socket;
	
	private $clients;
	
	private $mq;
	
	private $mq_socket;
	
	/**
	 *
	 * LasManager Constructor
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
	function __construct($url){
		$this->url = $url;
		$this->clients = array();
		
		$this->mq = new MessageQueueManager();
		$this->mq_socket = $this->mq->getSocket();
	}
	
	/**
	 *
	 * Start LasManager for LAS daemon (lasd.php)
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
	function start(){
		error_log("[".Time::getCurrentDateTime(true)."][LAS][LasManager][Start]");
		
		$this->_context = stream_context_create();
		
		// Create an Internet or Unix domain server socket
		$this->socket = stream_socket_server(
				$this->url,
				$errorcode,
				$errormsg,
				STREAM_SERVER_BIND | STREAM_SERVER_LISTEN,
				$this->_context
		);
		
		if ( $this->socket === false ) {
			error_log("[".Time::getCurrentDateTime(true)."][LAS][LasManager][Error: Cannot create server socket: [".$errorcode."] ".$errormsg."]");
			return Ajax::createErrorMsgByCode(LAS_ERROR_ESERVER);
		}
		
		error_log("[".Time::getCurrentDateTime(true)."][LAS][LasManager][OK]");
		
		// Subscribe message from Message Server
		$this->mq->subscribe();

		while ( true ) {
			$reads = $this->getSockets();
			array_push($reads, $this->mq_socket);

// 			echo "~~~~ Array of read ~~~~\n";
// 			print_r($reads);
// 			echo "\n\n";

			$write = null;
			$except = null;

			//now call select
			if ( @stream_select($reads, $write, $except, null) === false ) {
				die("Could not accept socket \n");
			}
			
			foreach ( $reads as $read ) {
// 				echo "read = ". $read ."\n\n";
                
				//the web socket listening port
				if ( $read === $this->socket ) {
					array_push($this->clients, stream_socket_accept($this->socket));
				}
				else{
					//message queue listening port
					if ($read == $this->mq_socket){
                        $this->mq->wait();
					}
					else {
						// client
						$wssocket = new WSSocket($read);
						$output = $wssocket->readFrame();
						
						if ( $output ){
						
						}
						
						if ( ($key = array_search($read, $this->clients)) !== false ) {
							unset($this->clients[$key]);
						}
						
						$wssocket->close();
					}
				}
			}
		}
	}
	
	/**
	 *
	 * get self socket & all client sockets
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
	protected function getSockets()
	{
		$sockets = array();
	
		$sockets[] = $this->socket;
		foreach ( $this->clients as $client ) {
			$sockets[] = $client;
		}
	
		return ($sockets);
	}
	
	/**
	 *
	 * client send message to LAS manager
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
	function clientSendMessage($message, $timeout)
	{
		$msg = json_decode($message);
		$task_id = $msg->task_id;
		
		error_log("[".Time::getCurrentDateTime(true)."][LAS][LasManager][clientSendMessage][Start][Task id: ".$task_id."]");
		
		$socket = stream_socket_client(
				$this->url,
				$errorcode,
				$errormsg,
				$timeout,
				STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT
				);
	
		if ( $socket === false ) {
			error_log("[".Time::getCurrentDateTime(true)."][LAS][LasManager][clientSendMessage][Error: Cannot open socket connection: [$errorcode] $errormsg ][Task id: ".$task_id."]");
			return false;
		}
	
		$wssocket = new WSSocket($socket);
		$writeBytes = $wssocket->writeFrame($message, LAS_MSGTYPE_REQUEST);
		$wssocket->close();

		if ($writeBytes !== false){
			$messageObj = json_decode($message);
			$inQueue = $this->mq->publish($messageObj);
			
			if ($inQueue){
				$taskManager = new TaskManager($messageObj->task_id);
				$result = $taskManager->updateStatus(TaskManager::TASK_STATUS_ACK_MQ);
				
				if ($result === false){
					$taskManager->deleteInstance();
				}
				
				error_log("[".Time::getCurrentDateTime(true)."][LAS][LasManager][clientSendMessage][End][Task id: ".$task_id."]");
				return $result;
			}
		}
		
		error_log("[".Time::getCurrentDateTime(true)."][LAS][LasManager][clientSendMessage][Error: Cannot write data to socket][Task id: ".$task_id."]");
		return false;
	}
	
	/**
	 *
	 * Receive message from Message Queue
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
	function mqReceiveMsg($data){
		$taskManager = new TaskManager($data->task_id);
		$taskManager->callbackHandler($data->success);
	}
	
	/**
	 *
	 * Close socket
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
	function close($socket)
	{
		$result = fclose($socket);
        
        $log = "[".Time::getCurrentDateTime(true)."][LAS][LasManager][close]";
        $log .= ($result) ? "[OK]" : "[Error: Cannot close socket connection]";
        error_log($log);
	}
}

/* ===============================================================
   End of LasManager.php
   =============================================================== */
?>
