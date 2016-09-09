<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : TaskLogManager.php
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
   @(#)TaskLogManager.php                  1.0 				10-AUG-2015
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
   Begin of TaskLogManager.php
   =============================================================== */
namespace Las\Core\Task;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
use Las\Tools\Mongo\MongoDao;

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
 * The class of all LAS task log.
 *
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */

class TaskLogManager
{
	const TASK_LOG_COLLECTION = 'task_log';
	
	/**
     *
     * @var \MongoId task id
     * @version 1.0
     * @since Version 1.4
     */
    public $taskid;
	
	/**
     *
     * @var string module name.
     *             Please do not init the module yourself. It should be
     *             construct by the constructor based on class name
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
     * @var integer Task create time
     * @version 1.0
     * @since Version 1.0
     */
    public $timecreated;

    /**
     *
     * Task Manager constructor
     *
     * If an id is given in the constructObj, then load the data from database.
     * Otherwise, update the value with constructObj as a default value
     * The update only affects the properties defined in TaskLogManager. Other 
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
    function __construct($constructObj = null)
    {
        if (is_array($constructObj)) {
            $constructObj = (object) $constructObj;
        }
		
		if (isset($constructObj->taskid)) {
            $this->taskid = $constructObj->taskid;
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
	}

    /**
     *
     * Save the current task log to database
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
        return MongoDao::save(self::TASK_LOG_COLLECTION, $this);
    }
	
    /**
     * create task log instance
     *
     * @since       Version 1.0.00
     * @see
     *
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function createInstance()
    {
        $this->timecreated = time();
		$this->_saveInstance();
	}
	
	/**
	 * delete task log instance
	 *
	 * @since       Version 1.0.00
	 * @see
	 *
	 * @author      Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function deleteInstanceByTaskId($task_id)
	{
		$creteria = array('taskid' => $task_id);
        return MongoDao::deleteList(self::TASK_LOG_COLLECTION, $creteria);
	}
}
/* ===============================================================
   End of TaskLogManager.php
   =============================================================== */
?>