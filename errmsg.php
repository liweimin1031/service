<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : errmsg.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Error Message for LAS
 * SEE ALSO    :
 * VERSION     : 1.3 ($Revision: 5025 $)
 * CREATED     : 24-AUG-2015
 * LASTUPDATES : $Author: yzlu $ on $Date: 2014-09-19 16:13:15 +0800 (Fri, 19 Sep 2014) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)errmsg.php               1.0 24-AUG-2015
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
   Begin of errmsg.php
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


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */

/**
 * Program entry point
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
// Assign error message to $LAS_CFG
$LAS_CFG->ErrorMsg = array();

$LAS_CFG->ErrorMsg[LAS_SUCCESS] = 'Success';
$LAS_CFG->ErrorMsg[LAS_ERROR_UNKNOWN] = 'Unknown error';

$LAS_CFG->ErrorMsg[LAS_ERROR_EPERM] = 'Operation not permitted';
$LAS_CFG->ErrorMsg[LAS_ERROR_ENOENT] = 'No such file or directory';
$LAS_CFG->ErrorMsg[LAS_ERROR_EINTR] = 'Interrupted';
$LAS_CFG->ErrorMsg[LAS_ERROR_EIO] = 'I/O error';
$LAS_CFG->ErrorMsg[LAS_ERROR_EEXIST] = 'File exists';
$LAS_CFG->ErrorMsg[LAS_ERROR_ENOTDIR] = 'Not a directory';
$LAS_CFG->ErrorMsg[LAS_ERROR_EISDIR] = 'Is a directory';
$LAS_CFG->ErrorMsg[LAS_ERROR_EFBIG] = 'File too large';
$LAS_CFG->ErrorMsg[LAS_ERROR_ENOSYS] = 'Not implemented';
$LAS_CFG->ErrorMsg[LAS_ERROR_ETIMEDOUT] = 'Timed out';
$LAS_CFG->ErrorMsg[LAS_ERROR_EACTNOTFOUND] = 'Activity not found';
$LAS_CFG->ErrorMsg[LAS_ERROR_ELOGIN] = 'Login failed';
$LAS_CFG->ErrorMsg[LAS_ERROR_ENETWORK] = 'Network error';
$LAS_CFG->ErrorMsg[LAS_ERROR_EINVAL_FILE] = 'Invalid file';
$LAS_CFG->ErrorMsg[LAS_ERROR_EACCES] = 'Permission denied';
$LAS_CFG->ErrorMsg[LAS_ERROR_ESERVER] = 'Server error';
$LAS_CFG->ErrorMsg[LAS_ERROR_EINVAL_DATA] = 'Input data not fit';
$LAS_CFG->ErrorMsg[LAS_ERROR_EINVAL] = 'Invalid argument';

$LAS_CFG->ErrorMsg[LAS_ERROR_DB_EINSERT] = 'Insert error';
$LAS_CFG->ErrorMsg[LAS_ERROR_DB_EDELETE] = 'Delete error';
$LAS_CFG->ErrorMsg[LAS_ERROR_DB_EUPDATE] = 'Update error';
$LAS_CFG->ErrorMsg[LAS_ERROR_DB_ESELECT] = 'Select error';

$LAS_CFG->ErrorMsg[LAS_ERROR_APP_EPERM] = 'Operation not permitted';
$LAS_CFG->ErrorMsg[LAS_ERROR_APP_EAGAIN] = 'Login again';
$LAS_CFG->ErrorMsg[LAS_ERROR_APP_EACCES] = 'Permission denied';
$LAS_CFG->ErrorMsg[LAS_ERROR_APP_EINVAL] = 'Invalid argument';
$LAS_CFG->ErrorMsg[LAS_ERROR_APP_ENOSYS] = 'Not implemented';

$LAS_CFG->ErrorMsg[LAS_ERROR_OAUTH_ECLIENT_NAME] = 'Missing Client name';
$LAS_CFG->ErrorMsg[LAS_ERROR_OAUTH_ECLIENT_TYPE] = 'Missing Client type';
$LAS_CFG->ErrorMsg[LAS_ERROR_OAUTH_ERETURN_URI]  = 'Missing return uri';
$LAS_CFG->ErrorMsg[LAS_ERROR_OAUTH_EKEYPAIR]  = 'Cannot Generate key pair';
$LAS_CFG->ErrorMsg[LAS_ERROR_OAUTH_EINVAL]       = 'Invalid Oauth ID';


// NOTE TO DEVELOPERS!
// Add your error code - error message mapping for your module/component just
// like this
require_once($LAS_CFG->core_root . '/task/errmsg.php');



/* ===============================================================
   End of errmsg.php
   =============================================================== */
?>
