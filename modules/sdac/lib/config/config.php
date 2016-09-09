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

$mongoOptions['dbhost'] = 'int-db01.elana.org,int-db02.elana.org,int-db03.elana.org';
$mongoOptions['dbuser'] = 'las_dev';
$mongoOptions['database'] = 'las_dev';
$mongoOptions['dbpassword'] = 'DB41as-1';
$mongoOptions['replicaSet'] = 'lasRepl';

/* ---------------------------------------------------------------
   Interface definition
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */



/* ===============================================================
   End of config.php
   =============================================================== */
