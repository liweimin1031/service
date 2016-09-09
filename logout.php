<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : logout.php
 * AUTHOR      : Sandy H. Y. Wong
 * SYNOPSIS    :
 * DESCRIPTION : CLMS Logout Script
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 967 $)
 * CREATED     : 30-JUL-2013
 * LASTUPDATES : $Author: patrickw $ on $Date: 2013-07-04 15:58:54 +0800 (週四, 04 七月 2013) $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)logout.php               1.0 30-JUL-2013
   by Sandy H. Y. Wong


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */


/* ===============================================================
   Begin of logout.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once('inc.php');

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
 * Program entry point
 *
 * @since       Version 1.0.00
 * @param       nil
 * @return      nil
 * @see
 * @author      Sandy H. Y. Wong
 * @testing
 * @warnings
 * @updates     
 */

UserSession::deleteSession();
$_SESSION['logout'] = true;

//Remove hashkey and credential if any
Cookie::setCredential();
Cookie::setHashKey();

$path = $LAS_CFG->wwwroot . '/index.php';
// Can't use redirect!
header("Location: $path");


/* ===============================================================
   End of logout.php
   =============================================================== */
?>
