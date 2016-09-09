<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : auth_login.php
 * AUTHOR      : Patrick C. K. Wu
 * SYNOPSIS    :
 * DESCRIPTION : CLMS Login Authentication
 * SEE ALSO    :
 * VERSION     : 1.4 ($Revision: 6718 $)
 * CREATED     : 26-AUG-2015
 * LASTUPDATES : $Author: patrickw $ on $Date: 2015-02-16 14:56:41 +0800 (Mon, 16 Feb 2015) $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)auth_login.php           1.0 26-AUG-2015
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
   Begin of auth_login.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(dirname(dirname(__DIR__)))).DIRECTORY_SEPARATOR . "inc.php";

use Las\Core\Util\Cookie;
use Las\Core\Util\String;
use Las\Core\User\UserSession;
use Las\Core\User\User;

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
 * Program entry point
 *
 * @since       Version 1.0.00
 * @param       param1          Description about parameter goes here
 * @return      Description     about what will be returned (if any)
 * @see
 */
/*
 * @author      Patrick C. K. Wu
 * @testing
 * @warnings
 * @updates
 */

$loginname = $_REQUEST['loginname'];
$password = $_REQUEST['password'];
$caller = isset($_REQUEST['caller']) ? $_REQUEST['caller'] : null;
$redirect = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : null;
$lang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : null;
$staylogin = isset($_REQUEST['staylogin']) ? $_REQUEST['staylogin'] : null;


// Bug# 6161: Use school URL instead of school code
if ( isset($caller) && $caller === 'oauth' ) {
    
}

$USER = User::authenticate( $loginname, $password);

if ( !$USER ) {
    $_SESSION['denied'] = true;

    // Use for OAuth redirect
    if ( isset($caller) && isset($redirect) ) {
        if ( $caller === 'oauth' ) {
            $path = $LAS_CFG->login_path . '?'
                  . 'caller=oauth'
                  . '&redirect_url=' . $redirect;
        }
    }
    Cookie::setLastError(LAS_ERROR_ELOGIN);
}
else {
    $_SESSION['user']['uid'] = $USER->id;
    $_SESSION['user']['loginname'] = $USER->loginname;
    $_SESSION['user']['role'] = $USER->role;

    $session = UserSession::createSession(
        $USER->id, UserSession::APP_LOGIN
    );

    // Support stay signed in
    // Set Hash and Credential if needed
    if ( !empty($staylogin) ) {
        $hashkey = String::generateRandomString();
        $credential = UserSession::encodeHash(
            $USER->loginname, $password, $hashkey
        );
        
        Cookie::setHashKey($hashkey);
        Cookie::setCredential($credential);
    }

    // Set default language
    // 16-FEB-2015 - Set default language if none (Bug# 6756)
    /*$userLang = Cookie::getLanguage($USER->id, false);
    if ( empty($userLang) ) {
        Cookie::setLanguage($lang, $USER->id);
    }*/

    // Use for OAuth redirect and School Channel redirect
    if ( isset($caller) && isset($redirect) ) {
        // 25-NOV-2014  - Support web redirect(Bug# 6308)
        if ( ($caller === 'oauth') || ($caller === 'web') ) {
            $path = $LAS_CFG->wwwroot . '/' . $redirect;
        }
    }
}

// Can't use redirect!
header("Location: $path");


/* ===============================================================
   End of auth_login.php
   =============================================================== */
?>
