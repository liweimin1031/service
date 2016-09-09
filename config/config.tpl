<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : config.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS Configuration Include
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6018 $)
 * CREATED     : 28-JUL-2015
 * LASTUPDATES : $Author: yzlu $ on $Date: 2014-12-05 12:06:33 +0800 (Fri, 05 Dec 2014) $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)config.php               1.0 				28-JUL-2015
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
   Begin of config.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
use Las\Tools\Mongo\DriverMongo;

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
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
$LAS_CFG->vendor = 'las';

// MongoDB Setting
$LAS_CFG->db_mongo_options['dbhost']     = '10.6.72.95,10.6.72.72,10.6.72.113';
$LAS_CFG->db_mongo_options['dbuser']     = 'elana';
$LAS_CFG->db_mongo_options['database']   = 'elana';
$LAS_CFG->db_mongo_options['dbpassword'] = 'elana';
$LAS_CFG->db_mongo_options['replicaSet'] = 'clmsRepl';

$LAS_CFG->mongo = DriverMongo::getInstance();
$LAS_CFG->mongo->setOptions($LAS_CFG->db_mongo_options);

$LAS_CFG->wwwpath = '';
$LAS_CFG->wwwroot = 'http://las-kary.hklms.org';

// LAS Template Path
$LAS_CFG->template_root = $LAS_CFG->portal_root . '/tpl';

// Message Server Setting
$LAS_CFG->message_server['host']           = 'mq-dev1.hklms.org';
$LAS_CFG->message_server['port']           = '5672';
$LAS_CFG->message_server['webstomp']       = 'http://mq-dev1.hklms.org:15674/stomp/';
$LAS_CFG->message_server['username']       = 'elana';
$LAS_CFG->message_server['password']       = 'MQ4e1ana';
$LAS_CFG->message_server['vhost']          = '/';
$LAS_CFG->message_server['timeout']        = '60';     // in seconds
$LAS_CFG->message_server['taskQueue']      = 'elana';
$LAS_CFG->message_server['ackResultQueue'] = 'elana_ack_result';

// Hadoop Setting
$LAS_CFG->hadoop_server['host']     = 'spark-dev1.hklms.org';
$LAS_CFG->hadoop_server['port']     = '50070';
$LAS_CFG->hadoop_server['username'] = 'hadoop';
$LAS_CFG->hadoop_server['root']     = 'user/hadoop/';

// LAS Daemon Setting
$LAS_CFG->las_daemon_server['host']    = '127.0.0.1';
$LAS_CFG->las_daemon_server['port']    = '10844';
$LAS_CFG->las_daemon_server['timeout'] = '60';     // in seconds

$LAS_CFG->session_lifetime = 1200;
$LAS_CFG->enable_uscookie_timeout = 1200;

// Callback Setting
$LAS_CFG->callback_retry       = 2;
$LAS_CFG->callback_retry_delay = 5;     // in seconds
$LAS_CFG->callback_timeout     = 60;    // in seconds

/* ===============================================================
   End of config.php
   =============================================================== */
?>