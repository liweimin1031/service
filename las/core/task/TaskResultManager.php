<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : TaskResultManager.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS task result manager
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6353 $)
 * CREATED     : 10-NOV-2015
 * LASTUPDATES : $Author: mhshi $ on $Date: 2013-02-27 11:20:37 +0800 (Wed, 27 Feb 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)TaskResultManager.php                  1.0 				10-NOV-2015
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
   Begin of TaskResultManager.php
   =============================================================== */
namespace Las\Core\Task;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
use Las\Tools\Mongo\MongoDao;
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
 * The class of all LAS task result.
 *
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */

class TaskResultManager
{
	const TASK_RESULT_COLLECTION = 'task_result';
	
	/**
	 *
	 * @var \MongoId task id
	 * @version 1.0
	 * @since Version 1.4
	 */
	public $taskid;
	
	/**
     *
     * @var object api output result
     * @version 1.0
     * @since Version 1.0
     */
    public $output = '';
	
    /**
     *
     * @var string task progress
     * @version 1.0
     * @since Version 1.0
     */
    public $progress;

    /**
     *
     * @var string error
     * @version 1.0
     * @since Version 1.0
     */
    public $error;
	
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
     * The update only affects the properties defined in TaskResultManager. Other 
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
    function __construct($taskId = null, $constructObj = null)
    {
    	if ( !empty($taskId) ) {
            $this->getTaskResult($taskId);
        }
        
        if (is_array($constructObj)) {
        	$constructObj = (object) $constructObj;
        }
        
        if (isset($constructObj->taskid)) {
        	$this->taskid = $constructObj->taskid;
        }
        
        if (isset($constructObj->output)) {
        	$this->output = $constructObj->output;
        }
        
        if (isset($constructObj->progress)) {
        	$this->progress = $constructObj->progress;
        }
        
        if (isset($constructObj->error)) {
        	$this->error = $constructObj->error;
        }
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
    public function getTaskResult($taskId)
    {
    	error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskResultManager][getTaskResult][Start]");
    	
    	$result = MongoDao::searchOne(self::TASK_RESULT_COLLECTION, array ('taskid' => $taskId));

        if (!empty($result)) {
            $this->_id= $result['_id'];
            $result = json_decode(json_encode($result));
            foreach ($result as $key => $value) {
                if ($key !== '_id') {
                    $this->$key = $value;
                }
            }
            error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskResultManager][getTaskResult][OK][Task id: ".$taskId."]");
        }
        else {
        	error_log("[".Time::getCurrentDateTime(true)."][LAS][TaskResultManager][getTaskResult][Error: No result for task id : $taskId]");
            return false;
        }
    }
}
/* ===============================================================
   End of TaskResultManager.php
   =============================================================== */
?>