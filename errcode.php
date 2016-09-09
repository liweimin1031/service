<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : errcode.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Error Code for LAS
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4977 $)
 * CREATED     : 24-AUG-2015
 * LASTUPDATES : $Author: yzlu $ on $Date: 2014-09-17 15:50:05 +0800 (Wed, 17 Sep 2014) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
 @(#)errcode.php              1.0 24-AUG-2015
 1.3 25-AUG-2014
 by Michelle Hong


 Copyright by ASTRI, Ltd., (ECE Group)
 All rights reserved.

 This software is the confidential and proprietary information
 of ASTRI, Ltd. ("Confidential Information").  You shall not
 disclose such Confidential Information and shall use it only
 in accordance with the terms of the license agreement you
 entered into with ASTRI.
 --------------------------------------------------------------- */


/* ===============================================================
 Begin of errcode.php
 =============================================================== */


/* ---------------------------------------------------------------
 Included Library
 --------------------------------------------------------------- */


/* ---------------------------------------------------------------
 Global Variables
 --------------------------------------------------------------- */


/* ---------------------------------------------------------------
 Constant definition
 --------------------------------------------------------------- */
define('LAS_ERRORCODE_PREFIX', 'LAS_ERROR_');

define('LAS_SUCCESS',          0);                        // No error
define('LAS_ERROR_UNKNOWN',    -1);                       // Unknown error
//define('LAS_ERROR_UNKNOWN',    0xFFFFFFFF);               // Unknown error

define('LAS_ERROR_SYS_START',  		0x00000000);
define('LAS_ERROR_EPERM',      		LAS_ERROR_SYS_START | 1);  // Op not permitted
define('LAS_ERROR_ENOENT',     		LAS_ERROR_SYS_START | 2);  // No such file/dir
define('LAS_ERROR_EINTR',      		LAS_ERROR_SYS_START | 4);  // Interrupted
define('LAS_ERROR_EIO',        		LAS_ERROR_SYS_START | 5);  // I/O error
define('LAS_ERROR_EEXIST',     		LAS_ERROR_SYS_START | 17); // File exists
define('LAS_ERROR_ENOTDIR',    		LAS_ERROR_SYS_START | 20); // Not a dir
define('LAS_ERROR_EISDIR',     		LAS_ERROR_SYS_START | 21); // Is a dir
define('LAS_ERROR_EFBIG',      		LAS_ERROR_SYS_START | 27); // File too large
define('LAS_ERROR_ENOSYS',     		LAS_ERROR_SYS_START | 38); // Not implemented
define('LAS_ERROR_ETIMEDOUT',  		LAS_ERROR_SYS_START | 110); // Timed out
define('LAS_ERROR_EACTNOTFOUND', 	LAS_ERROR_SYS_START | 111); // Activity not found
define('LAS_ERROR_ELOGIN',     		LAS_ERROR_SYS_START | 112); // Login failed
define('LAS_ERROR_ENETWORK',   		LAS_ERROR_SYS_START | 113); // Network error
define('LAS_ERROR_EINVAL_FILE',   	LAS_ERROR_SYS_START | 203); // Invalid file
define('LAS_ERROR_EACCES',    		LAS_ERROR_SYS_START | 300); // Permission denied
define('LAS_ERROR_ESERVER',   		LAS_ERROR_SYS_START | 301); // Server error
define('LAS_ERROR_EINVAL_DATA',   	LAS_ERROR_SYS_START | 302); // Input data not fit
define('LAS_ERROR_EINVAL',     		LAS_ERROR_SYS_START | 303); // Invalid argument

define('LAS_ERROR_DB_START',   		0x00010000);
define('LAS_ERROR_DB_EINSERT', 		LAS_ERROR_DB_START | 1);   // Insert error
define('LAS_ERROR_DB_EDELETE', 		LAS_ERROR_DB_START | 2);   // Delete error
define('LAS_ERROR_DB_EUPDATE', 		LAS_ERROR_DB_START | 3);   // Update error
define('LAS_ERROR_DB_ESELECT', 		LAS_ERROR_DB_START | 4);   // Select error

define('LAS_ERROR_APP_START',  		0x00020000);
define('LAS_ERROR_APP_EPERM',  		LAS_ERROR_APP_START | 1);  // Op not permitted
define('LAS_ERROR_APP_EAGAIN', 		LAS_ERROR_APP_START | 11); // Login again
define('LAS_ERROR_APP_EACCES', 		LAS_ERROR_APP_START | 13); // Permission denied
define('LAS_ERROR_APP_EINVAL', 		LAS_ERROR_APP_START | 22); // Invalid arg
define('LAS_ERROR_APP_ENOSYS', 		LAS_ERROR_APP_START | 38); // Not implemented

define('LAS_ERROR_OAUTH_START',  	   0x00030000);
define('LAS_ERROR_OAUTH_ECLIENT_NAME', LAS_ERROR_OAUTH_START | 1); // Missing client name
define('LAS_ERROR_OAUTH_ECLIENT_TYPE', LAS_ERROR_OAUTH_START | 2); // Missing client type
define('LAS_ERROR_OAUTH_ERETURN_URI',  LAS_ERROR_OAUTH_START | 3); // Missing return uri
define('LAS_ERROR_OAUTH_EKEYPAIR',     LAS_ERROR_OAUTH_START | 4); // Cannot generate key pair
define('LAS_ERROR_OAUTH_EINVAL',       LAS_ERROR_OAUTH_START | 5); // Invalid Oauth client ID


// NOTE TO DEVELOPERS!
// Add your own START VALUE (0xYYYY0000) for your module/component just like
// this
define('LAS_ERROR_TASK_START',         0x00040000);
require_once($LAS_CFG->core_root . '/task/errcode.php');


// Assign error code to $LAS_CFG
$LAS_CFG->LAS_SUCCESS = LAS_SUCCESS;

$constants = get_defined_constants(true);
$constants = $constants['user'];
foreach ( $constants as $name => $value ) {
    if ( strpos($name, LAS_ERRORCODE_PREFIX) === 0 ) {
        $LAS_CFG->$name = $value;
    }
}


/*---------------------------------------------------------------
    Function definition
 --------------------------------------------------------------- */


/*===============================================================
  End of errcode.php
 =============================================================== */
