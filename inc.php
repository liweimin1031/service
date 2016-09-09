<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : inc.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS Main Include
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4061 $)
 * CREATED     : 28-JUL-2015
 * LASTUPDATES : $Author: yzlu $ on $Date: 2014-05-22 11:56:33 +0800 (Thu, 22 May 2014) $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)inc.php                  1.0 				28-JUL-2015
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
   Begin of inc.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__FILE__) . "/las/core/loader/ClassAutoloader.php");
require_once(dirname(__FILE__) . '/lib/oauth2-server-php-develop/src/OAuth2/Autoloader.php');

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */
unset($LAS_CFG);
$LAS_CFG = new stdClass;

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */
define('LAS_LAS_RELPATH',             '/las');
define('LAS_CONFIG_RELPATH',          '/config');
define('LAS_CORE_RELPATH',            '/core');
define('LAS_PORTAL_RELPATH',          '/portal');
define('LAS_MODULES_RELPATH',         '/modules');
define('LAS_DATA_RELPATH',            '/data');
define('LAS_DATATMP_RELPATH',         '/tmp');
define('LAS_JSLIB_RELPATH',           '/jslib');
define('LAS_LIB_RELPATH',             '/lib');
define('LAS_TOOLS_RELPATH',           '/tools');
define('LAS_RELEASE_RELPATH',         '/release');
define('LAS_REPORTING_RELPATH',         '/reporting');

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
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates     
 */

$LAS_USER = null;

$LAS_CFG->root = dirname(__FILE__);

$LAS_CFG->las_root = $LAS_CFG->root . LAS_LAS_RELPATH;
$LAS_CFG->core_root = $LAS_CFG->las_root . LAS_CORE_RELPATH;
$LAS_CFG->portal_root = $LAS_CFG->las_root . LAS_PORTAL_RELPATH;
$LAS_CFG->modules_root = $LAS_CFG->root . LAS_MODULES_RELPATH;
$LAS_CFG->reporting_root = $LAS_CFG->root . LAS_REPORTING_RELPATH;
$LAS_CFG->jslib_root = $LAS_CFG->root . LAS_JSLIB_RELPATH;
$LAS_CFG->lib_root = $LAS_CFG->root . LAS_LIB_RELPATH;
$LAS_CFG->config_root = $LAS_CFG->root . LAS_CONFIG_RELPATH;
$LAS_CFG->tools_root = $LAS_CFG->root . LAS_TOOLS_RELPATH;
$LAS_CFG->data_root = $LAS_CFG->config_root . LAS_DATA_RELPATH;
$LAS_CFG->theme = 'default';
$LAS_CFG->template_root = '';

$LAS_CFG->loader = new ClassAutoloader;
$LAS_CFG->loader->registerNamespace('Las\Modules', $LAS_CFG->modules_root);
$LAS_CFG->loader->registerNamespace('Las\Tools', $LAS_CFG->tools_root);
$LAS_CFG->loader->registerNamespace('Las', $LAS_CFG->las_root);
$LAS_CFG->loader->register();


// register oauth library
OAuth2\Autoloader::register();


// Read version value
$strVersion = file_get_contents(dirname(__FILE__) . '/version.json');
$version = json_decode($strVersion);
$LAS_CFG->version = $version->version;
$LAS_CFG->build = $version->build;


// Read default system value
/*require_once($LAS_CFG->config_root . '/default_values.php');
if ( isset($LAS_DEFAULTS) ) {
    foreach ( $LAS_DEFAULTS as $key => $value ) {
        $LAS_CFG->$key = $value;
    }
}*/

// Include config.php
require_once($LAS_CFG->config_root . '/config.php');

// Includes other config.php (if any)
foreach (glob($LAS_CFG->config_root . '/*_config.php') as $file) {
    require_once($file);
}

$LAS_CFG->datatmp_root = $LAS_CFG->data_root . LAS_DATATMP_RELPATH;

// URL related
if ( isset($_SERVER) && isset($_SERVER['HTTP_HOST']) ) {
    $host = $_SERVER['HTTP_HOST'];
    $protocol = 'http';
    if ( !empty($_SERVER['HTTPS']) ) {
        $protocol = 'https';
    }

    $LAS_CFG->wwwroot = $protocol . '://' . $host . $LAS_CFG->wwwpath;
}


$LAS_CFG->login_path = $LAS_CFG->wwwroot . '/index.php';
$LAS_CFG->logout_path = $LAS_CFG->wwwroot . '/logout.php';

// For error code and error message
require_once($LAS_CFG->root . '/errcode.php');
require_once($LAS_CFG->root . '/errmsg.php');


// Add this line for making $LAS_CFG to be a global variable to the files
// included by a function.
$GLOBALS['LAS_CFG'] = $LAS_CFG;
$GLOBALS['LAS_USER'] = $LAS_USER;


require_once($LAS_CFG->root . '/setup.php');

/* ===============================================================
   End of inc.php
   =============================================================== */
?>
