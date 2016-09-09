<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : TaskManager.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS task manager
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6353 $)
 * CREATED     : 10-AUG-2015
 * LASTUPDATES : $Author: mhshi $ on $Date: 2013-02-27 11:20:37 +0800 (Wed, 27 Feb 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)TaskManager.php                  1.0 				10-AUG-2015
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
   Begin of TaskManager.php
   =============================================================== */
namespace Las\Core\Task;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
use Las\Tools\Mongo\MongoDao;
use Las\Core\Task\TaskLogManager;
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
 *
 * The base class of all LAS task.
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

class TaskManager
{
	const TASK_COLLECTION = 'task';
	
	const TASK_STATUS_FINISH = 100;
    
    const TASK_STATUS_FILE_ACCEPT = 200;
    
    const TASK_STATUS_PROCESSING = 201;
    
    const TASK_STATUS_NO_DATA_FILE = 202;
    
    const TASK_STATUS_ACK_MQ = 210;
    
    const TASK_STATUS_ACK_SPARK = 211;
    
    /**
     *
     * @var string module name.
     * @version 1.0
     * @since Version 1.0
     */
    public $module = '';

    /**
     *
     * @var string api caller
     * @version 1.0
     * @since Version 1.0
     */
    public $caller = '';

    /**
     * 
     * @var string api url
     * @version 1.0
     * @since Version 1.0
     */
    public $api = '';

    /**
     *
     * @var object api input data
     * @version 1.0
     * @since Version 1.0
     */
    public $input = '';

    /**
     *
     * @var array api input data files. 
	 *            Each item in array shall be a file url
     * @version 1.0
     * @since Version 1.0
     */
    public $files = array();

	/**
     *
     * @var integer task status
     * @version 1.0
     * @since Version 1.0
     */
    public $status = 0;
    
    /**
     *
     * @var integer task status for message queue
     *      0: not ack message queue; 1: ack message queue
     * @version 1.0
     * @since Version 1.0
     */
    public $ackqueue = 0;
    
    /**
     *
     * @var integer task status for spark
     *      0: not ack spark; 1: ack spack
     * @version 1.0
     * @since Version 1.0
     */
    public $ackspark = 0;
    
    /**
     *
     * @var integer Task create time
     * @version 1.0
     * @since Version 1.0
     */
    public $timecreated;

    /**
     *
     * @var integer Task latest modified time
     * @version 1.0
     * @since Version 1.0
     */
    public $lastmodified;

    /**
     *
     * Task Manager constructor
     *
     * If an id is given in the constructObj, then load the data from database.
     * Otherwise, update the value with constructObj as a default value
     * The update only affects the properties defined in TaskManager. Other 
     * properties is not handled.
     * 
     * @version 1.0
     * @since  Version 1.0
     * @param string $id task id.
     *               This should be equals to mongoID value
     * @param object $constructObj constructor object. Value could include
     *               string $caller api caller
	 *               string $api api url
     *               array $files api input data files
     *               $id MongoId String
     * @see
     * @author       Kary Ho
     * @testing
     * @warnings
     * @comments
     * @updates
     */
    function __construct($id = null, $constructObj = null)
    {
    	if ( !empty($id) ) {
            $this->getTaskById($id);
        }
        
        if (is_array($constructObj)) {
        	$constructObj = (object) $constructObj;
        }
        
        if (isset($constructObj->module)) {
        	$this->module = $constructObj->module;
        }
        
        if (isset($constructObj->caller)) {
        	$this->caller = $constructObj->caller;
        }
        
        if (isset($constructObj->api)) {
        	$this->api = $constructObj->api;
        }
        
        if (isset($constructObj->input)) {
        	$this->input = $constructObj->input;
        }
        
        if (isset($constructObj->files)) {
        	if(empty($constructObj->files)) {
        		$this->files = array();
        	} else {
        		$this->files = $constructObj->files;
        	}
        }
        
        if (isset($constructObj->status)) {
        	$this->status = $constructObj->status;
        }
        
        if (isset($constructObj->ackqueue)) {
        	$this->ackqueue = $constructObj->ackqueue;
        }
        
        if (isset($constructObj->ackspark)) {
        	$this->ackspark = $constructObj->ackspark;
        }
    }

    /**
     *
     * Save the current task to database
     *
     * @version 1.0
     * @since  Version 1.0
     * @return \Lemo\Lib\Database\mixed
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    private function _saveInstance()
    {
    	return MongoDao::save(self::TASK_COLLECTION, $this);
    }
	
	/**
     *
     * Load the task detail to the object
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $id task id
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function getTaskById($id)
    {
    	$result = MongoDao::searchOneById(self::TASK_COLLECTION, $id);

        if (!empty($result)) {
            $this->_id= $result['_id'];
            $result = json_decode(json_encode($result));
            foreach ($result as $key => $value) {
                if ($key !== '_id') {
                    $this->$key = $value;
                }
            }
            //error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][getTaskById][OK][Task id: ".$id."]");
        }
        else {
        	error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][getTaskById][Error: Invalid task id : $id]");
            return false;
        }
    }
    
    /**
     *
     * Create task instance
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
    public function createInstance()
    {
    	error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][createInstance][Start]");
    	
    	$this->lastmodified = time();
        
		if (empty($this->timecreated)) {
            $this->timecreated = $this->lastmodified;
        }
        
        if (isset($this->input->data)){
        	$this->status = self::TASK_STATUS_PROCESSING;
        }
        else{
        	$this->status = self::TASK_STATUS_NO_DATA_FILE;
        }
        
		$this->_saveInstance();
		
		if ( !empty($this->getId()) ){
			$this->createTaskLog();
			
			error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][createInstance][OK][Task id: ".$this->getId()." ; Task status: ".$this->status."]");
			return $this;
		}
		else{
			error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][createInstance][Error: Create task failed]");
			return false;
		}
		
	}

    /**
     * 
     * Update task instance
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
    public function updateInstanceById($update, $options=array(), $fields=array())
    {
    	error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][updateInstanceById][Start]");
    	
		if (!empty($option)){
			$newOptions = array_merge($options, array('new' => true));
		}
		else{
			$newOptions = array('new' => true);
		}
    	
        try {
        	$retTask = MongoDao::findAndModify(
        		self::TASK_COLLECTION,
        		array (
    				'_id' => new \MongoId($this->getId())
    			),
        		array (
					'$set' => $update
    			),
        		$fields,
        		$newOptions
        	);
        	
        	error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][updateInstanceById][OK][Task id: ".$this->getId()."]");
        	return json_decode(json_encode($retTask));
        }
        catch (MongoResultException $e) {
    		error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][updateInstanceById][Error: Task id: ".$this->getId()."; (".$e->getCode().") ".$e->getMessage()."]");
    		return false;
    	}
    }

    /**
     *
     * Update task status
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
    public function updateStatus($status)
    {
    	if ($status === self::TASK_STATUS_ACK_MQ){
			$update = array (
    			'status' => self::TASK_STATUS_PROCESSING,
    			'ackqueue' => 1,
    			'lastmodified' => time()
    		);
    	}
   		else {
			$update = array (
   				'status' => $status,
   				'lastmodified' => time()
   			);
   		}

    	$retTask = $this->updateInstanceById($update);
    	
    	if ($retTask !== false){
    		$this->status = $retTask->status;
    		$this->ackqueue = $retTask->ackqueue;
    		 
    		$result = new \stdClass;
    		$result->task_id = $this->getId();
    		$result->status = $this->status;
    		 
    		return $result;
    	}
    	else {
    		return false;
    	}
    }
    
    /**
     *
     * Update task files
     *
     * @version 1.0
     * @since  Version 1.0
     * @param  string    $path    file path
     * @return 
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function updateFile($path)
    {
    	$curFiles = $this->files;
    	
    	if (!empty($path)){
    		if (!in_array($path, $curFiles)){
    			array_push($this->files, $path);
    			
				$update = array (
	   				'files' => $this->files,
	   				'lastmodified' => time()
	   			);
				
				$retTask = $this->updateInstanceById($update);
				
				if ($retTask !== false){
					return $retTask;
				}
				else {
					$this->files = $curFiles;
				}
    		}
    		else {
    			return true;
    		}
    	}
    	
		return false;
    }
    
    /**
     *
     * Delete task files
     *
     * @version 1.0
     * @since  Version 1.0
     * @param  string    $path    file path
     * @return
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function deleteFile($path)
    {
    	$newFiles = array();
    	$curFiles = $this->files;
    	 
    	if (!empty($path)){
    		$key = array_search($path, $curFiles);
    		
    		if ($key !== false){
    			foreach ($curFiles as $file){
    				if ($file !== $path){
						array_push($newFiles, $file);
    				}
    			}
    			
    			$update = array (
    				'files' => $newFiles,
    				'lastmodified' => time()
    			);
    			
    			$retTask = $this->updateInstanceById($update);
    			
    			if ($retTask !== false){
    				$this->files = $newFiles;
    				return true;
    			}
    		}
    		else {
    			return true;
    		}
    	}
    	
    	return false;
    }

    /**
     *
     * Delete current module from database
     *
     * Each module has to handle its own table and file system
     * before calling this function
     * @version 1.0
     * @param string $aid module id
     * @since  Version 1.0
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function deleteInstance()
    {
    	error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][deleteInstance][Start]");
    	
    	$result = MongoDao::deleteById(self::TASK_COLLECTION, $this->getId());
    	
    	if ($result) {
    		error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][deleteInstance][OK]");
    		return $this->deleteTaskLog();
    	}
    	else {
    		error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][deleteInstance][Error: Delete task failed]");
    		return false;
    	}
    }

	/**
     *
     * Get task id
     *
     *
     * @version 1.0
     * @since  Version 1.0
     * @return mixed The module MongoID value or null if not found
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function getId()
    {
        if (isset($this->_id)) {
            return $this->_id->{'$id'};
        }
        return null;
    }
	
	/**
     *
     * Add task log to DB
     *
     *
     * @version 1.0
     * @since  Version 1.0
     * @return mixed The module MongoID value or null if not found
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
	public function createTaskLog()
	{
		$log = new \stdClass();
		$log->taskid = $this->getId();
		$log->module = $this->module;
		$log->caller = $this->caller;
		$log->api = $this->api;
		$log->timecreated = time();
		
		$taskLogManager = new TaskLogManager($log);
		$taskLogManager->createInstance();
	}
	
	/**
	 *
	 * Add task log to DB
	 *
	 *
	 * @version 1.0
	 * @since  Version 1.0
	 * @return mixed The module MongoID value or null if not found
	 * @see
	 * @author      Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function deleteTaskLog()
	{
		$taskLogManager = new TaskLogManager();
		return $taskLogManager->deleteInstanceByTaskId($this->getId());
	}
	
	/**
	 *
	 * Push the task analysis result to external parties
	 *
	 *
	 * @version 1.0
	 * @since  Version    1.0
	 * @param  boolean    $success    task finish success
	 * @return 
	 * @see
	 * @author      Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function callbackHandler($success)
	{
		global $LAS_CFG;
		
		$taskId = $this->getId();
		
		error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][callbackHandler][Start][Task id: ".$taskId."]");
		
		if ( isset($this->input) ){
			$input = $this->input;
			
			if ( (isset($input->callback)) && (!empty($input->callback)) ){
				// Check callback url format
				if (filter_var($input->callback, FILTER_VALIDATE_URL) === FALSE) {
					error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][callbackHandler][Task id: ".$taskId."; Error: Invalid URL format]");
					return false;
				}
				else{
					$url = $input->callback;
				}
				
				if ( (isset($input->key)) && (!empty($input->key)) ){
					$key = $input->key;
					
					if ($success){
						if ($this->status === self::TASK_STATUS_FINISH){
							exec("php ".$LAS_CFG->core_root."/task/TaskCallback.php " . $taskId ." ". $key ." ". $url ." ". $success ." > /dev/null &");
							error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][callbackHandler][OK][Task id: ".$taskId."]");
							return true;
						}
					}
				}
				else{
					// Error: Key missing
					error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskManager][callbackHandler][Task id: ".$taskId."; Error: Authorization key missing]");
					return false;
				}
			}
		}
	}
}
/* ===============================================================
   End of TaskManager.php
   =============================================================== */
?>