<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : AccessToken.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS access token manager
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6353 $)
 * CREATED     : 10-AUG-2015
 * LASTUPDATES : $Author: mhshi $ on $Date: 2013-02-27 11:20:37 +0800 (Wed, 27 Feb 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)AccessToken.php                  1.0 				10-AUG-2015
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
   Begin of AccessToken.php
   =============================================================== */
namespace Las\Core\Util;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
use Las\Tools\Mongo\MongoDao;
use Las\Core\Task\TaskManager;

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
 * The class of all LAS task report.
 *
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */

class AccessToken
{
	const ACCESS_TOKEN_COLLECTION = 'access_token';
	
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
     * Access token constructor
     * 
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author       Kary Ho
     * @testing
     * @warnings
     * @comments
     * @updates
     */
    function __construct($constructObj = null)
    {
	}
	
	/**
     *
     * Create access task instance
     *
     * @version 1.0
     * @param  string  $taskId task id
	 * @since  Version 1.0
	 * @return
	 * @see
	 * @author      Kary Ho
	 * @testing
	 * @warnings
	 * @updates
     */
    public function createToken($taskId)
    {
    	$taskManager = new TaskManager($taskId);
    	$taskObj = $taskManager->getTaskById($taskId);
    	
    	if ($taskObj !== false){
    		$this->taskid = $taskId;
    		$this->module = $taskManager->module;
    		$this->caller = $taskManager->caller;
    		$this->api = $taskManager->api;
    		$this->timecreated = time();

    		MongoDao::save(self::ACCESS_TOKEN_COLLECTION, $this);
    		
    		if (isset($this->_id)) {
    			$tokenId = $this->_id->{'$id'};
    			
    			$baseTokenId = base64_encode($tokenId);
    			$key = md5($this->taskid . $this->module . $this->caller . $this->api);
    			$token = $baseTokenId .".". $key;
    			
    			$result = new \stdClass();
    			$result->taskid = $taskId;
	    		$result->module = $this->module;
	    		$result->caller = $this->caller;
	    		$result->api = $this->api;
	    		$result->status = $taskManager->status;
	    		$result->token = $token;
	    		$result->timecreated = $this->timecreated;
    			
    			return $result;
    		}
    	}

    	return false;
	}
	
	/**
	 *
	 * Access token validation
	 *
	 * @version 1.0
	 * @param  string $token access token
	 * @since  Version 1.0
	 * @see
	 * @author      Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function validateToken($token)
	{
		$keys = explode('.', $token);
		
		if (count($keys) == 2){
			$tokenId = base64_decode($keys[0]);
			
			$tokenObj = MongoDao::searchOneById(self::ACCESS_TOKEN_COLLECTION, $tokenId);
			
			if ($tokenObj) {
				$tokenObj = json_decode(json_encode($tokenObj));
				$key = md5($tokenObj->taskid . $tokenObj->module . $tokenObj->caller . $tokenObj->api);
				
				if ($key === $keys[1]){
					$result = new \stdClass();
					$result->id = $tokenId;
					$result->taskid = $tokenObj->taskid;
					$result->module = $tokenObj->module;
					$result->caller = $tokenObj->caller;
					$result->api = $tokenObj->api;
					$result->timecreated = $tokenObj->timecreated;
					
					return $result;
				}
			}
		}
		
		return false;
	}

    /**
     *
     * Delete access token from database
     *
     * @version 1.0
     * @param  string $token access token
     * @since  Version 1.0
     * @see
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function deleteToken($token)
    {
    	$tokenObj = self::validateToken($token);
    	
    	try {
    		$retVal = MongoDao::deleteById(self::ACCESS_TOKEN_COLLECTION, $tokenObj->id);

    		if ($retVal->n){
    			return true;
    		}
    		
    		return false;
    	}
    	catch (MongoCursorException $e) {
    		return false;
    	}
    }
}
/* ===============================================================
   End of AccessToken.php
   =============================================================== */
?>