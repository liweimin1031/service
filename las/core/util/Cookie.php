<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Cookie.php
 * AUTHOR      : Patrick C. K. Wu
 * SYNOPSIS    :
 * DESCRIPTION : CLMS Cookie Class
 * SEE ALSO    :
 * VERSION     : 1.4 ($Revision: 7087 $)
 * CREATED     : 30-JUL-2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-05-04 11:32:10 +0800 (Mon, 04 May 2015) $
 * UPDATES     : 05-MAY-2014    - Storage usage alert (Bug# 6261)
                 13-MAY-2014    - Support login to multiple CLMS on same server
                                  (Bug# 6275)
                 28-AUG-2014    - Change storage checking period (Bug# 6380)
                 20-JAN-2015    - Session cookie timeout option (Bug# 6704)
                 16-FEB-2015    - Set default language if none (Bug# 6756)
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)Cookie.php               1.0 30-JUL-2013
                                1.2 14-JAN-2014
                                1.3 13-MAY-2014
                                1.4 16-FEB-2015
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
   Begin of Cookie.php
   =============================================================== */
namespace Las\Core\Util;


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
 * Main Cookie class
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
class Cookie {
    const LAS_COOKIE_CREDENTIAL = 'LASID';
    const LAS_COOKIE_HASHKEY    = 'LASHK';
    const LAS_COOKIE_LANGUAGE   = 'las_language';
    const LAS_COOKIE_LASTACCESS = 'las_lastaccess';
    const LAS_COOKIE_LASTERROR = 'las_lasterror';
    const LAS_COOKIE_LASTSTORAGECHECK = 'las_laststoragecheck';
    const LAS_COOKIE_LASTSTORAGEEXCEED = 'las_lastexceed';
    const LAS_COOKIE_SESSION    = 'las_session';
    const LAS_COOKIE_SESSIONKEY = 'las_sessionkey';
    const LAS_COOKIE_OAUTH_SCODE = 'las_oauth_scode';
    const LAS_COOKIE_OAUTH_SESSION = 'las_oauth_session';
    const LAS_COOKIE_OAUTH_SESSIONKEY = 'las_oauth_sessionkey';
    const LAS_COOKIE_OAUTH_USERINFO = 'las_oauth_userinfo';

    const LAS_COOKIE_SEP        = '-';


    /**
     *
     * This function get the value of cookie with <code>name</code>
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string name     The name of the cookie to be get 
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    private static function _get($name) {
        global $_COOKIE;

        if ( empty($_COOKIE[$name]) ) {
            return('');
        }
        else {
            return($_COOKIE[$name]);
        }
    }

    /**
     *
     * This function set the cookie with cookie <code>name</code> and
     * <code>value</code>
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string name     The name of the cookie to be set 
     * @param   string value    The value to be set
     * @param   int expire      The time the cookie expires
     * @param   string uid      The user ID (if any)
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    private static function _set($name, $value, $expire, $uid='') {
        $strName = $name;

        if ( $uid && !($uid == '') ) {
            $strName = $strName . Cookie::LAS_COOKIE_SEP . $uid;
        }

        if ( $expire == '' ) {
            $expire = 0;
        }

        setcookie($strName, $value, $expire, '/');
    }

    /**
     *
     * This function delete the cookie with cookie <code>name</code>
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string name     The name of the cookie to be delete
     * @param   string uid      The user ID (if any)
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function delete($name, $uid='') {
        Cookie::_set($name, "", time() - 3600, $uid);
    }

    /**
     *
     * This function get the cookie about credential
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   nil
     * @return  Returns the credential stored in cookie if any
     * @see     setCredential
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function getCredential() {
        return(Cookie::_get(Cookie::LAS_COOKIE_CREDENTIAL));
    }

    /**
     *
     * This function get the cookie about hash key
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   nil
     * @return  Returns the hash key stored in cookie if any
     * @see     setHashKey
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function getHashKey() {
        return(Cookie::_get(Cookie::LAS_COOKIE_HASHKEY));
    }

    /**
     *
     * This function get the cookie about language setting
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string uid      The user ID (if any)
     * @return  Returns the hash key stored in cookie if any
     * @see     setHashKey
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates 16-FEB-2015     - Set default language if none (Bug# 6756)
     */
    public static function getLanguage($uid='', $default=true) {
        global $LAS_CFG;
        $strName = Cookie::LAS_COOKIE_LANGUAGE;

        if ( $uid && !($uid == '') ) {
            $strName = $strName . Cookie::LAS_COOKIE_SEP . $uid;
        }
        $lang = Cookie::_get($strName);

        // 16-FEB-2015 - Set default language if none (Bug# 6756)
        if ( empty($lang) && ($default === true) ) {
            $lang = isset($LAS_CFG) ? $LAS_CFG->default_locale : 'en_utf8';
            Cookie::setLanguage($lang, $uid);
        }
        return($lang);
    }
    
    /**
     *
     * This function get the cookie about last error set
     *
     * @version 1.0
     * @since   Version 1.3
     * @param   string uid      The user ID (if any)
     * @return  Returns the last error stored in cookie if any
     * @see     setLastError
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function getLastError($uid='') {
        $strName = Cookie::LAS_COOKIE_LASTERROR;

        if ( $uid && !($uid == '') ) {
            $strName = $strName . Cookie::LAS_COOKIE_SEP . $uid;
        }
        return(Cookie::_get($strName));
    }
    
    /**
     *
     * This function get the cookie if last check of storage
     *
     * @version 1.0
     * @since   Version 1.4
     * @param   string uid      The user ID (if any)
     * @return  Returns the last check of storage in cookie if any
     * @see     setLastStorageCheck
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function getLastStorageCheck($uid='') {
        $strName = Cookie::LAS_COOKIE_LASTSTORAGECHECK;

        if ( $uid && !($uid == '') ) {
            $strName = $strName . Cookie::LAS_COOKIE_SEP . $uid;
        }
        return(Cookie::_get($strName));
    }
    
    /**
     *
     * This function get the cookie if last storage exceed alert set
     *
     * @version 1.0
     * @since   Version 1.3
     * @param   string uid      The user ID (if any)
     * @return  Returns the last error stored in cookie if any
     * @see     setLastStorageExceed
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function getLastStorageExceed($uid='') {
        $strName = Cookie::LAS_COOKIE_LASTSTORAGEEXCEED;

        if ( $uid && !($uid == '') ) {
            $strName = $strName . Cookie::LAS_COOKIE_SEP . $uid;
        }
        return(Cookie::_get($strName));
    }
    
    /**
     *
     * This function get the cookie about school code used for OAuth
     *
     * @version 1.0
     * @since   Version 1.2
     * @return  Returns the oauth session string stored in cookie if any
     * @see     setOauthScode
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function getOauthScode() {
        return(Cookie::_get(Cookie::LAS_COOKIE_OAUTH_SCODE));
    }

    /**
     *
     * This function get the cookie about oauth session
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  Returns the oauth session string stored in cookie if any
     * @see     setOauthSession
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function getOauthSession() {
        return(Cookie::_get(Cookie::LAS_COOKIE_OAUTH_SESSION));
    }
    
    /**
     *
     * This function get the cookie about oatuh session key
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  Returns the oauth session key string stored in cookie if any
     * @see     setOauthSessionKey
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function getOauthSessionKey() {
        return(Cookie::_get(Cookie::LAS_COOKIE_OAUTH_SESSIONKEY));
    }

    /**
     *
     * This function get the cookie about connect user information used for
     * OAuth
     *
     * @version 1.0
     * @since   Version 1.2
     * @return  Returns the connect user information
     * @see     setOauthUserInfo
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function getOauthUserInfo() {
        return(Cookie::_get(Cookie::LAS_COOKIE_OAUTH_USERINFO));
    }

    /**
     *
     * This function get the cookie about login session
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  Returns the session string stored in cookie if any
     * @see     setSession
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates 13-MAY-2014     - Support login to multiple CLMS on same server
                                  (Bug# 6275)
     */
    public static function getSession() {
        global $LAS_CFG;

        $key = str_replace('/', '_', $LAS_CFG->wwwpath)
             . '_' . Cookie::LAS_COOKIE_SESSION;
        return(Cookie::_get($key));
        //return(Cookie::_get(Cookie::LAS_COOKIE_SESSION));
    }

    /**
     *
     * This function get the cookie about login session key
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  Returns the session key string stored in cookie if any
     * @see     setSessionKey
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates 13-MAY-2014     - Support login to multiple CLMS on same server
                                  (Bug# 6275)
     */
    public static function getSessionKey() {
        global $LAS_CFG;

        $key = str_replace('/', '_', $LAS_CFG->wwwpath)
             . '_' . Cookie::LAS_COOKIE_SESSIONKEY;
        return(Cookie::_get($key));
        //return(Cookie::_get(Cookie::LAS_COOKIE_SESSIONKEY));
    }

    /**
     *
     * This function get the cookie about hash key
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string credential       The user ID (if any)
     * @return  nil
     * @see     getCredential
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function setCredential($credential='') {
        $strName = Cookie::LAS_COOKIE_CREDENTIAL;
        $expire = time() + (3600 * 24 * 60);        // 60 days

        if ( $credential === '' ) {
            Cookie::delete($strName);
        }
        else {
            Cookie::_set($strName, $credential, $expire);
        }
    }

    /**
     *
     * This function set the cookie about hash key
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string hashkey          The desired hash key to be set
     * @return  nil
     * @see     getHashKey
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function setHashKey($hashkey='') {
        $strName = Cookie::LAS_COOKIE_HASHKEY;
        $expire = time() + (3600 * 24 * 60);        // 60 days

        if ( $hashkey === '' ) {
            Cookie::delete($strName);
        }
        else {
            Cookie::_set($strName, $hashkey, $expire);
        }
    }

    /**
     *
     * This function set the cookie about language setting
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string language         The desired language to be set
     * @param   string uid              The user ID (if any)
     * @return  nil
     * @see     getLanguage
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function setLanguage($language, $uid='') {
        $strName = Cookie::LAS_COOKIE_LANGUAGE;
        $expire = time() + (3600 * 24 * 60);        // 60 days

        if ( $language === '' ) {
            Cookie::delete($strName, $uid);
        }
        else {
            Cookie::_set($strName, $language, $expire, $uid);
        }
    }

    /**
     *
     * This function set the cookie about last error set
     *
     * @version 1.0
     * @since   Version 1.3
     * @param   string error            The desired language to be set
     * @param   string uid              The user ID (if any)
     * @return  nil
     * @see     getLastError
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function setLastError($error='', $uid='') {
        $strName = Cookie::LAS_COOKIE_LASTERROR;
        $expire = time() + (3600);      // 60 mins

        if ( $error === '' ) {
            Cookie::delete($strName, $uid);
        }
        else {
            Cookie::_set($strName, $error, $expire, $uid);
        }
    }

    /**
     *
     * This function set the cookie about last storage check
     *
     * @version 1.0
     * @since   Version 1.4
     * @param   string uid      The user ID (if any)
     * @return  nil
     * @see     getLastStorageCheck
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function setLastStorageCheck($time='', $uid='') {
        $strName = Cookie::LAS_COOKIE_LASTSTORAGECHECK;
        $expire = time() + (3600 * 24); // 24 hours

        if ( $time === '' ) {
            Cookie::delete($strName, $uid);
        }
        else {
            Cookie::_set($strName, $time, $expire, $uid);
        }
    }

    /**
     *
     * This function set the cookie about last storage exceed alert set
     *
     * @version 1.0
     * @since   Version 1.3
     * @param   string uid              The user ID (if any)
     * @return  nil
     * @see     getLastStorageExceed
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function setLastStorageExceed($time='', $uid='') {
        $strName = Cookie::LAS_COOKIE_LASTSTORAGEEXCEED;
        $expire = time() + (3600 * 24); // 24 hours

        if ( $time === '' ) {
            Cookie::delete($strName, $uid);
        }
        else {
            Cookie::_set($strName, $time, $expire, $uid);
        }
    }

    /**
     *
     * This function set the cookie about oauth session
     *
     * @version 1.0
     * @since   Version 1.2
     * @param   string socde            The school code
     * @return  nil
     * @see     getOauthScode
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function setOauthScode($scode) {
        $strName = Cookie::LAS_COOKIE_OAUTH_SCODE;
        $expire = time() + 3600;        // 1 hour
        
        if ( $scode === '' ) {
            Cookie::delete($strName);
        }
        else {
            Cookie::_set($strName, $scode, $expire);
        }
    }

    /**
     *
     * This function set the cookie about oauth session
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string key              The oauth session
     * @param   int ttl                 The time to live of session
     * @return  nil
     * @see     getOauthSession
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function setOauthSession($session, $ttl) {
        $strName = Cookie::LAS_COOKIE_OAUTH_SESSION;
        
        if ( $session === '' ) {
            Cookie::delete($strName);
        }
        else {
            Cookie::_set($strName, $session, $ttl);
        }
    }

    /**
     *
     * This function set the cookie about oauth session key
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string key              The session key
     * @param   int ttl                 The time to live of session key
     * @return  nil
     * @see     getOauthSessionKey
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function setOauthSessionKey($key, $ttl) {
        $strName = Cookie::LAS_COOKIE_OAUTH_SESSIONKEY;

        if ( $key === '' ) {
            Cookie::delete($strName);
        }
        else {
            Cookie::_set($strName, $key, $ttl);
        }
    }

    /**
     *
     * This function set the cookie about connect user information
     *
     * @version 1.0
     * @since   Version 1.2
     * @param   string info     The serialized connect user information
     * @return  nil
     * @see     getOauthUserInfo
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function setOauthUserInfo($info) {
        $strName = Cookie::LAS_COOKIE_OAUTH_USERINFO;
        $expire = time() + 3600;        // 1 hour
        
        if ( $info === '' ) {
            Cookie::delete($strName);
        }
        else {
            Cookie::_set($strName, $info, $expire);
        }
    }

    /**
     *
     * This function set the cookie about session string
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string key              The session key
     * @param   int ttl                 The time to live of session key
     * @return  nil
     * @see     getSession
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates 13-MAY-2014     - Support login to multiple CLMS on same server
                                  (Bug# 6275)
                20-JAN-2015     - Session cookie timeout option (Bug# 6704)
     */
    public static function setSession($session, $ttl) {
        global $LAS_CFG, $LAS_USER;

        //$strName = Cookie::LAS_COOKIE_SESSION;
        $strName = str_replace('/', '_', $LAS_CFG->wwwpath)
                 . '_' . Cookie::LAS_COOKIE_SESSION;

        // 20-JAN-2015 - Session cookie timeout option (Bug# 6704)
        $expire = ($ttl) ? time() + $ttl : '';
        
        if ( $session === '' ) {
            Cookie::delete($strName);
        }
        else {
            Cookie::_set($strName, $session, $expire);
        }
    }

    /**
     *
     * This function set the cookie about language setting
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string key              The session key
     * @param   int ttl                 The time to live of session key
     * @return  nil
     * @see     getSessionKey
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates 13-MAY-2014     - Support login to multiple CLMS on same server
                                  (Bug# 6275)
                20-JAN-2015     - Session cookie timeout option (Bug# 6704)
     */
    public static function setSessionKey($key, $ttl) {
        global $LAS_CFG;

        //$strName = Cookie::LAS_COOKIE_SESSIONKEY;
        $strName = str_replace('/', '_', $LAS_CFG->wwwpath)
                 . '_' . Cookie::LAS_COOKIE_SESSIONKEY;

        // 20-JAN-2015 - Session cookie timeout option (Bug# 6704)
        $expire = ($ttl) ? time() + $ttl : '';

        if ( $key === '' ) {
            Cookie::delete($strName);
        }
        else {
            Cookie::_set($strName, $key, $expire);
        }
    }
    
    /**
     * 
     * This function set a custom variable to database
     *
     * @since  Version 1.0
     * @param string $name cookie name
     * @param string $value cookie value
     * @param integer $expire cookie expired time
     * @param string $uid clms user id
     * @return 
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function setCustomCookieValue($name, $value, $expire, $uid= '')
    {
        return self::_set($name, $value, $expire, $uid);
    }
    
    /**
     * 
     * This function get the custom cokkie values
     *
     * @since  Version 1.0
     * @param string $name cookie name
     * @param string $uid clms user id
     * @return Ambigous <\Clms\Core\Util\nil, string, unknown>
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function getCustomCookieValue($name, $uid='')
    {
        if(!empty($uid)){
            $name = $name.'-'. $uid;
        }
        return self::_get($name, $uid);
    }
    
}


/* ===============================================================
   End of Cookie.php
   =============================================================== */
?>
