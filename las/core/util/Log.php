<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Log.php
 * AUTHOR      : Patrick C. K. Wu
 * SYNOPSIS    :
 * DESCRIPTION : CLMS Log Class
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6049 $)
 * CREATED     : 09-DEC-2014
 * LASTUPDATES : $Author: patrickw $ on $Date: 2014-12-09 09:55:24 +0800 (Tue, 09 Dec 2014) $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)Log.php                  1.0 09-DEC-2014
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
   Begin of Log.php
   =============================================================== */
namespace Las\Core\Util;


use Las\Core\User\User;
/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */


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
 * Main Log class
 *
 * @since       Version 1.0.00
 * @param       nil
 * @return      nil
 * @see
 */
/*
 * @author      Patrick C. K. Wu
 * @testing
 * @warnings
 * @updates     
 */
class Log {
    const LOG_TAG               = 'CLS';

    const LOG_AUTH_APPLOGIN     = 'applogin';
    const LOG_AUTH_WEBLOGIN     = 'weblogin';
    const LOG_AUTH_OAUTHLOGIN   = 'oauthlogin';
    const LOG_AUTH_LOGOUT       = 'logout';

    const LOG_PARTNER_AUTH      = 'auth';
    const LOG_PARTNER_LOG       = 'log';
    const LOG_PARTNER_SYS       = 'sys';
    const LOG_PARTNER_OAUTH     = 'oauth';

    /**
     *
     * This function create user information object of current user
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string scode    The school code
     * @param   string uid      The user ID
     * @return  Returns the user information object
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    private static function _getUserInfo($uid=null) {
        global $LAS_USER;

        $user = new \StdClass;
        if (!empty($uid) ) {
            $tmpUser = User::getById($uid);
            
        }
        else if ( isset($LAS_USER) ) {
            $tmpUser = $LAS_USER;
        }

        if ( isset($tmpUser) ) {
            $user->user = $tmpUser->loginname ;
            switch ( $tmpUser->role ) {
                case User::OPERATOR:
                    $user->role = 'admin';
                    break;
                /*case User::STUDENT:
                    $user->role = 'student';
                    break;
                case User::TEACHER:
                    $user->role = 'teacher';
                    break;
                case User::ADMIN:
                    $user->role = 'administrator';
                    break;*/
                default:
                    $user->role = 'unknown';
                    break;
            }
        }

        return ($user);
    }

    /**
     *
     * This function create standard success message object with
     * <code>data</code>
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   object data     The data to be included
     * @return  Returns the JSON message in string
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function _log($partner, $request, $msg) {
        global $LAS_CFG;

        if (
            !isset($LAS_CFG) || !isset($LAS_CFG->enable_log)  ||
            $LAS_CFG->enable_log
        ) {
            $strLog  = '[' . Log::LOG_TAG . ']'
                     . '[' . $partner . ']'
                     . '[' . $request . ']'
                     . '[' . $msg . ']';

            error_log($strLog);
        }
    }

    /**
     *
     * This function create standard success message object with
     * <code>data</code>
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string type     APPLOGIN | WEBLOGIN | LOGOUT
     * @param   string uid      The user ID
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function logAuth($type, $uid=null) {
        global $LAS_USER;

        if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
            if (
                ($type === Log::LOG_AUTH_APPLOGIN)      ||
                ($type === Log::LOG_AUTH_WEBLOGIN)      ||
                ($type === Log::LOG_AUTH_LOGOUT)
            ) {
                $partner = Log::LOG_PARTNER_AUTH;
                $request = $type;
                $msg = self::_getUserInfo($uid);
                $msg->time = time();
                $msg->agent = $_SERVER['HTTP_USER_AGENT'];
                Log::_log($partner, $request, json_encode($msg));
            }
        }
    }

    /**
     *
     * This function create standard success message object with
     * <code>data</code>
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string data     The page name
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function logPageAccess($page) {
        global $LAS_USER;

        if ( isset($LAS_USER) ) {
            $partner = Log::LOG_PARTNER_LOG;
            $msg = self::_getUserInfo();
            $msg->time = time();
            Log::_log($partner, $page, json_encode($msg));
        }
    }
    
    
    public static function logSystemError($module, $error){
        $msg = self::_getUserInfo();
        $msg->time = time();
        $partner = Log::LOG_PARTNER_SYS;
        if($error instanceof \Exception) {
            $msg->error = $error->getMessage();
            $msg->errorLocation = 'line '. $error->getLine(). ' in File ' . $error->getFile();
        } else {
            $msg->error = $error;
        }
        Log::_log($partner, $module, json_encode($msg));
        
    }
    
    public static function logOauth($action, $uid = null){
        $msg = self::_getUserInfo();
        $msg->time = time();
        $partner = Log::LOG_PARTNER_OAUTH;
        Log::_log($partner, $action, json_encode($msg));
    }
}


/* ===============================================================
   End of Log.php
   =============================================================== */
?>
