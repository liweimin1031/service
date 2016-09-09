#!/usr/bin/env php

<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : lasd.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS daemon for message server
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6353 $)
 * CREATED     : 28-AUG-2015
 * LASTUPDATES : $Author: mhshi $ on $Date: 2013-02-27 11:20:37 +0800 (Wed, 27 Feb 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
 @(#)lasd.php                  1.0 				28-AUG-2015
 by Kary Ho


 Copyright by ASTRI, Ltd., (ECE Group)
 All rights reserved.

 This software is the confidential and proprietary information
 of ASTRI, Ltd. ("Confidential Information").  You shall not
 disclose such Confidential Information and shall use it only
 in accordance with the terms of the license agreement you
 entered into with ASTRI.
 --------------------------------------------------------------- */


/* ===============================================================
   Begin of lasd.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__FILE__) . '/../inc.php');

use Las\Core\MessageManager\LasManager;

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */
global $LAS_CFG;

$daemon_host                = $LAS_CFG->las_daemon_server['host'];
$daemon_port                = $LAS_CFG->las_daemon_server['port'];
$daemon_url                 = 'tcp://'.$daemon_host.':'.$daemon_port;

// Read options from "/etc/init.d/lasd"
// Script: /path_to_las/sys/lasd.php --pidfile /var/run/lasd.pid  > /dev/null &
$shortsops = "";
$longopts = array("pidfile:");
$options = getopt($shortsops, $longopts);

$pidfile = $options['pidfile'];

if ( (isset($pidfile)) && (!empty($pidfile)) ){
	// Get LAS daemon process ID
	$pid = getmypid();
	
	// Write $pid to $pidfile
	file_put_contents($pidfile, $pid);
	
	$server = new LasManager($daemon_url);
	$server->start();
}
/* ===============================================================
   End of lasd.php
   =============================================================== */
?>