<?php
use Clms\Tools\PhpDao\Mongo\DriverMongo;

/* --------------------------------------------------------------- */
/**
 * FILE NAME   : bootstrap.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms bootstrap the sytem
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 2493 $)
 * CREATED     : Jan 14, 2013
 * LASTUPDATES : $Author: karyho $ on $Date: 2015-02-05 10:53:40 +0800 (Thu, 05 Feb 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) bootstrap.php              1.0 Jan 14, 2013
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
   Begin of bootstrap.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */

/**
 * Require the ClassAutoloader.php
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR
    . 'util'. DIRECTORY_SEPARATOR . 'ClassAutoloader.php';

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR
    . 'config' . DIRECTORY_SEPARATOR . 'config.php';



/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */

/**
 * @global ClassAutoloader $loader Set the global class loader
 */
$loader = new \Astri\Lib\Util\ClassAutoloader;
$loader->registerNamespace('Clms\Tools\PhpDao', dirname(__FILE__) . DIRECTORY_SEPARATOR
    . 'database');
$loader->register();

$mongo = DriverMongo::getInstance();

$mongo->setOptions($mongoOptions);
/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */



/* ===============================================================
   End of bootstrap.php
   =============================================================== */