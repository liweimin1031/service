<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : setup.php
 * AUTHOR      : Patrick C. K. Wu
 * SYNOPSIS    :
 * DESCRIPTION : CLMS User Setup Script
 * SEE ALSO    :
 * VERSION     : 1.2 ($Revision: 4028 $)
 * CREATED     : 31-DEC-2013
 * LASTUPDATES : $Author: patrickw $ on $Date: 2014-05-13 16:56:29 +0800 (Tue, 13 May 2014) $
 * UPDATES     : 13-MAY-2014    - Support login to multiple CLMS on same server
                                  (Bug# 6275)
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)setup.php                1.0 31-DEC-2013
                                1.2 11-MAR-2014
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
   Begin of setup.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
use Las\Core\User\User;
use Las\Core\User\UserSession;
use Las\Core\Util\Cookie;


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
 * Check if user can stay signed in
 *
 * @since       Version 1.0.00
 * @param       nil
 * @return      bool
 * @see
 * @author      Patrick C. K. Wu
 * @testing
 * @warnings
 * @updates     
 */
function setup_check_hashlogin() {
    $hashkey = Cookie::getHashKey();
    $credential = Cookie::getCredential();
   
    if ( !empty($hashkey) && !empty($credential) ) {
        $user = UserSession::decodeHash($credential, $hashkey);
        
        if ( $user ) {
            $result = UserSession::checkSession();
            if ( !$result ) {
                UserSession::createSession(
                    $user->id, UserSession::WEB_LOGIN
                );
            }
            UserSession::updateSession();
            return(true);
        }
    }
    return(false);
}

/**
 * Program entry point
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
if ( PHP_SAPI != 'cli' ) {
    session_set_cookie_params(0, $LAS_CFG->wwwpath);
    session_name(str_replace('/', '_', $LAS_CFG->wwwpath));
    session_start();
}

if ( isset($_SESSION) && isset($_SESSION['logout']) ) {
    session_destroy();
}
else if ( isset($_SESSION) && isset($_SESSION['denied']) ) {
    unset($_SESSION['user']);
    unset($_SESSION['denied']);
}
else {
    $result = setup_check_hashlogin();
    if ( !$result ) {
        $result = UserSession::checkSession();
    }
    
    if (
        $result                         &&
        isset($_SESSION)                &&
        isset($_SESSION['user'])        &&
        isset($_SESSION['user']['uid'])
    ) {
        $uid = $_SESSION['user']['uid'];
        
        $user = User::getById($uid);
           
        if ( $user ) {
            $LAS_USER = $user;
            //$LAS_CFG->login_path = $LAS_CFG->wwwroot;
        }

    }
}


session_write_close();


/* ===============================================================
   End of setup.php
   =============================================================== */
?>
