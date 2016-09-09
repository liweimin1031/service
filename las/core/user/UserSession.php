<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : UserSession.php
 * AUTHOR      : Patrick C. K. Wu
 * SYNOPSIS    :
 * DESCRIPTION : CLMS UserSession Object
 * SEE ALSO    :
 * VERSION     : 1.3 ($Revision: 6649 $)
 * CREATED     : 29-JUL-2013
 * LASTUPDATES : $Author: patrickw $ on $Date: 2015-02-05 12:25:32 +0800 (Thu, 05 Feb 2015) $
 * UPDATES     : 30-JUL-2014    - Provide session period to Apps API (Bug# 6313)
                 08-DEC-2014    - Enable log (Bug# 6596)
                 30-JAN-2015    - Session cookie timeout option (Bug# 6704)
                 05-FEB-2015    - Import DAO DbException class (Bug# 6549)
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)UserSession.php          1.0 29-JUL-2013
                                1.1 30-JUL-2014
                                1.2 08-DEC-2014
                                1.3 05-FEB-2015
   by Patrick C. K. Wu


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */


/* ===============================================================
   Begin of UserSession.php
   =============================================================== */
namespace Las\Core\User;


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */

use Las\Core\Util\Cookie;
use Las\Core\Util\Log;
use Las\Core\Util\String;
use Las\Tools\Mongo\Exception\DbException;
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
 * Main UserSession class
 *
 * @since       Version 1.0.00
 * @param       nil
 * @return      nil
 * @see
 * @author      Patrick C. K. Wu
 * @testing
 * @warnings
 * @updates     
 */
class UserSession{
    const WEB_LOGIN             = 1;
    const APP_LOGIN             = 2;
    const LOGOUT_BIT            = 0x1000;
    const TIMELOGOUT            = 0x1000;
    const FORCELOGOUT           = 0x3000;
    const USERLOGOUT            = 0x5000;
    const LOGOUTTYPE_MASK       = 0xF000;
    
    public $userid = null;
    public $sessionkey = null;
    public $sessiontoken = null;
    public $sessiontype = null;
    public $timeout = null;
    public $timecreated = null;
    public $lastmodified = null;

    const COLLECTION = 'user_session';
    
    
    const HASH_IV       = 'ASTRI_HASH_IVINITIALIZATION_VECTOR';
    /**
     *
     * This function calculate the lifetime of the session according to
     * school config (if any) or $LAS_CFG->lifetime
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    private static function _calcTTL() {
        global $LAS_CFG;
        $ttl = $LAS_CFG->session_lifetime;
        return($ttl);
    }
    
    private function save(){
        if(empty($this->timecreated)){
            $this->timecreated = time();
        }
        
        $this->lastmodified = time();
        return MongoDao::save(self::COLLECTION, $this);
    }
    
    private function updateBySession($sessionkey, $sessiontoken, $sessiontype){
        $keys = array (
                "sessiontoken"=>$sessiontoken,
                "sessionkey"=>$sessionkey
        );
        
        $update = array('$set' => array('sessiontype' => $sessiontype));
        MongoDao::findAndModify(self::COLLECTION, $keys, $update);
    }
    
    private function getBySession($sessionkey, $sessiontoken){
        $keys = array (
                "sessiontoken"=>$sessiontoken,
                "sessionkey"=>$sessionkey
        );
        $doc = MongoDao::searchOne(self::COLLECTION, $keys);
        if($doc){
            unset($doc['_id']);
            return json_decode(json_encode($doc));
        }
         return false;
       
    }
    
    public static function getSessionHistory($timecreated, $userid)
    {
        $keys = array (
                "userid"=>$userid,
                "timecreated"=>array('$gt' =>$timecreated)
        );
        $cursor =MongoDao::search(self::COLLECTION, $keys);
        foreach($cursor as $doc){
            unset($doc['_id']); 
            $result [] = json_decode(json_encode($doc));
        }
        
        return $result;
    }
    
    public static function getByUserSessionType($sessiontype, $timeout, $userid)
    {
        $keys = array (
                "userid"=>$userid,
                "sessiontype"=>$sessiontype,
                "timeout"=>array('$gt' =>$timeout)
        );
        $cursor =MongoDao::search(self::COLLECTION, $keys);
        foreach($cursor as $doc){
            unset($doc['_id']); 
            $result [] = json_decode(json_encode($doc));
        }
        
        return $result;
    }

    /**
     *
     * This function convert standard user entry in PHP object to internal
     * User entry
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  bool Returns <code>true</code> if session is valid; otherwise,
                <code>false</code> is returned
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function checkSession() {
        if ( PHP_SAPI == 'cli' ) {
            return;
        }

        $session = Cookie::getSession();
        $sessionkey = Cookie::getSessionKey();

        $userSession = UserSession::getBySession($sessionkey, $session);
        
        try {
            if ( $userSession ) {
                if ( $userSession->timeout < time() ) {
                    // Session timeout!
                    // Keep the session record as login record
                    //$userSession->sessiontoken = '';
                    $userSession->sessiontype |=UserSession::TIMELOGOUT;
                    UserSession::updateBySession($sessionkey, $session, $userSession->sessiontype);
                    unset($_SESSION['user']);
                }
                else if (
                    ($userSession->sessiontype & UserSession::LOGOUT_BIT) 
                ) {
                    unset($_SESSION['user']);
                }
                else {
                    $_SESSION['user']['uid'] = $userSession->userid;
                    return(true);
                }
            }
        }
        catch (DbException $e) {
        }

        return(false);
    }

    /**
     *
     * This function convert standard user entry in PHP object to internal
     * User entry
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   int $uid        The user ID
     * @param   int $type       The session type (login type)
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates 08-DEC-2014     - Enable log (Bug# 6596)
                30-JAN-2015     - Session cookie timeout option (Bug# 6704)
     */
    public static function createSession($uid, $type) {
        global $LAS_CFG;

        $ttl = UserSession::_calcTTL();
        // 30-JAN-2015 - Session cookie timeout option (Bug# 6704)
        if ( $LAS_CFG->enable_uscookie_timeout ) {
            $ttl2 = $ttl + 2 * 60;  // 2 minutes buffer
        }
        else {
            $ttl2 = $ttl + 31536000;// 365 dyas later
        }

        $session = new UserSession;
        $session->userid = $uid;
        $session->sessionkey = String::generateRandomString(20);
        $session->sessiontoken = md5($session->sessionkey . ',' . $uid);
        $session->sessiontype = $type;
        $session->timeout = time() + $ttl;

        try {
            // 08-DEC-2014 - Enable log (Bug# 6596)
            if ( $type === UserSession::WEB_LOGIN ) {
                Log::logAuth(Log::LOG_AUTH_WEBLOGIN, $uid);
            }
            else if ( $type === UserSession::APP_LOGIN ) {
                Log::logAuth(Log::LOG_AUTH_APPLOGIN,  $uid);
            }

            // Check if multiple login is enabled
            if (
                isset($LAS_CFG->multiple_login)        &&
                ($LAS_CFG->multiple_login !== 1)
            ) {
                $sessions = UserSession::getActiveSession($uid, $type);
                foreach ( $sessions as $actSession ) {
                    $actSession->sessiontype |= UserSession::FORCELOGOUT;
                    $actSession->save();
                }
            }

            $session->save();
            Cookie::setSessionKey($session->sessionkey, $ttl2);
            Cookie::setSession($session->sessiontoken, $ttl2);
            unset($session->_id);
            
            return($session);
        }
        catch (DbException $e) {
            return(false);
        }
    }

    /**
     *
     * This function convert standard user entry in PHP object to internal
     * User entry
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates 08-DEC-2014     - Enable log (Bug# 6596)
     */
    public static function deleteSession() {
        if ( PHP_SAPI == 'cli' ) {
            return;
        }

        try {
            // 08-DEC-2014 - Enable log (Bug# 6596)
            Log::logAuth(Log::LOG_AUTH_LOGOUT);

            $session = Cookie::getSession();
            $sessionkey = Cookie::getSessionKey();

            $userSession = UserSession::getBySession($sessionkey, $session);
            if (
                $userSession  &&
                !($userSession->sessiontype & UserSession::LOGOUT_BIT)
            ) {
                // Keep the session record as login record
                //$userSession->sessiontoken = '';
                $userSession->sessiontype |= UserSession::USERLOGOUT;
                
                UserSession::updateBySession($sessionkey, $session, $userSession->sessiontype);
            }
        }
        catch (DbException $e) {
        }
    }

    /**
     *
     * This function gets active session(s) for a particular user with
     * <code>uid</code>
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   int $uid        The user ID
     * @param   int $type       The session type (login type)
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function getActiveSession($uid, $type) {
        global $LAS_CFG;

        $ttl = UserSession::_calcTTL();
        $timeout = time() - $ttl;

        $records = UserSession::getByUserSessionType($type, $timeout, $uid);
        return($records);
    }

    /**
     *
     * This function calculate the lifetime of the session according to
     * school config (if any) or $LAS_CFG->lifetime
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function getSessionPeriod() {
        return(UserSession::_calcTTL());
    }

    /**
     *
     * This function convert standard user entry in PHP object to internal
     * User entry
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates 30-JAN-2015     - Session cookie timeout option (Bug# 6704)
     */
    public static function updateSession() {
        if ( PHP_SAPI == 'cli' ) {
            return;
        }

        global $LAS_CFG;

        $session = Cookie::getSession();
        $sessionkey = Cookie::getSessionKey();

        $userSession = UserSession::getBySession($sessionkey, $session);
        try {
            if ( $userSession ) {
                if ( $userSession->timeout < time() ) {
                    // Session timeout!
                    // Keep the session record as login record
                    //$userSession->sessiontoken = '';
                    $userSession->sessiontype |= UserSession::TIMELOGOUT;
                    UserSession::updateBySession($sessionkey, $session, $userSession->sessiontype);
                    unset($_SESSION['user']);
                }
                else if (
                    ($userSession->sessiontype & UserSession::LOGOUT_BIT)
                ) {
                    unset($_SESSION['user']);
                }
                else {
                    $_SESSION['user']['uid'] = $userSession->userid;
                    $ttl = UserSession::_calcTTL();

                    // 30-JAN-2015 - Session cookie timeout option (Bug# 6704)
                    if ( $LAS_CFG->enable_uscookie_timeout ) {
                        $ttl2 = $ttl + 2 * 60;  // 2 minutes buffer
                    }
                    else {
                        $ttl2 = $ttl + 31536000;// 365 dyas later
                    }

                    $userSession->timeout = time() + $ttl;
                    try {
                        $userSession->save();
                        Cookie::setSessionKey($userSession->sessionkey, $ttl2);
                        Cookie::setSession($userSession->sessiontoken, $ttl2);
                    }
                    catch (DbException $e) {
                    }
                }
            }
        }
        catch (DbException $e) {
        }
    }
    
    /**
     *
     * This function encode the user information <code>scode</code>,
     * <code>username</code>, <code>password</code> with <code>hashkey</code>
     * to credential
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string $username        The loginname
     * @param   string $password        The password
     * @param   string $hashkey         The hash key
     * @return  string The credential
     * @see
     * @author  Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates
     */
    public static function encodeHash($username, $password, $hashkey) {
        if (
                isset($username)    &&
                isset($password)    &&
                isset($hashkey)
        ) {
            $iv = self::HASH_IV;
            $key = $hashkey;
    
            $text = $username . ',' . $password . ','
                    . md5($key . ':' . $username)
                    . ',';
            $crypttext = mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv
            );
            $crypttext = base64_encode($crypttext);
            return($crypttext);
        }
        else {
            return(false);
        }
    }
    
    
    /**
     *
     * This function decode and validate the <code>credential</code> encoded
     * with <code>hashkey</code>
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   object $credential      The credential to be validate
     * @param   object $hashkey         The hash key
     * @return  object Returns user object on success; otherwise,
     <code>false</code>
     * @see
     * @author  Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates
     */
    public static function decodeHash($credential, $hashkey) {
        if ( isset($credential) && isset($hashkey) ) {
            $iv = User::QRCODE_IV;
            $key = $hashkey;
            $crypttext = base64_decode($credential, true);
            $text = mcrypt_decrypt(
                    MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv
            );
            $tokens = explode(',', $text);
    
            if ( count($tokens) === 5 ) {
                $username = $tokens[1];
                $password = $tokens[2];
    
                $user = User::authenticate($username, $password);
                if (
                        $user && !($user->deleted)
                ) {
                    $checksum = md5($key . ':' . $username
                    );
                    if ( strcmp($tokens[3], $checksum) === 0 ) {
                        return($user);
                    }
                }
            }
        }
        return(false);
    }
}


/* ===============================================================
   End of UserSession.php
   =============================================================== */
?>
