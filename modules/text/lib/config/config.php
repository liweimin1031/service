<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : config.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lemo config file.
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 295 $)
 * CREATED     : Mar 15, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2013-05-18 22:45:52 +0800 (Sat, 18 May 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) config.php              1.0 Mar 15, 2013
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
   Begin of config.php
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
   Class definition
   --------------------------------------------------------------- */

$mongoOptions['dbhost'] = 'int-web01.hklms.org,int-web02.hklms.org,int-web03.hklms.org';
$mongoOptions['dbuser'] = 'elana';
$mongoOptions['database'] = 'elana';
$mongoOptions['dbpassword'] = 'elana';
$mongoOptions['replicaSet'] = 'clmsRepl';

/* ---------------------------------------------------------------
   Interface definition
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */



/* ===============================================================
   End of config.php
   =============================================================== */
