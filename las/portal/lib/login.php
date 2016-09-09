<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : op_login.php
 * AUTHOR      : Patrick C. K. Wu
 * SYNOPSIS    :
 * DESCRIPTION : Operator Login Authentication
 * SEE ALSO    :
 * VERSION     : 1.3 ($Revision: 3589 $)
 * CREATED     : 30-JUL-2013
 * LASTUPDATES : $Author: patrickw $ on $Date: 2014-03-06 12:19:12 +0800 (Thu, 06 Mar 2014) $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)op_login.php             1.0 30-JUL-2013
                                1.3 06-MAR-2014
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
   Begin of op_login.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(dirname(dirname(__DIR__)))).DIRECTORY_SEPARATOR . "inc.php";


use Las\Core\User\User;
use Las\Core\User\UserSession;
use Las\Core\Util\Cookie;
use Las\Core\Util\String;


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
 * @author      Patrick C. K. Wu
 * @testing
 * @warnings
 * @updates
 */

$loginname = $_REQUEST['loginname'];
$password = $_REQUEST['password'];
$staylogin = isset($_REQUEST['staylogin']) ? $_REQUEST['staylogin'] : null;

$USER = User::authenticate($loginname, $password);

if ( !$USER ) {
    $_SESSION['denied'] = true;

    // Bug# 6160 - Enable login fail message
    Cookie::setLastError(LAS_ERROR_ELOGIN);
}
else {
    $_SESSION['user']['uid'] = $USER->id;
    $_SESSION['user']['loginname'] = $USER->loginname;
    $_SESSION['user']['role'] = $USER->role;

    $session = UserSession::createSession($USER->id, UserSession::WEB_LOGIN);

    // Support stay signed in
    // Set Hash and Credential if needed
    if ( !empty($staylogin) ) {
        $hashkey = String::generateRandomString();
        $credential = UserSession::encodeHash($USER->loginname, $password, $hashkey
        );
        Cookie::setHashKey($hashkey);
        Cookie::setCredential($credential);
    }
}

if ( !isset($path) ) {
    $path = $LAS_CFG->wwwroot . '/' . 'index.php';
}

// Can't use redirect!
header("Location: $path");


/* ===============================================================
   End of op_login.php
   =============================================================== */
?>
