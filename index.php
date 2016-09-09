<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : index.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS index page
 * SEE ALSO    :
 * VERSION     : 1.1 ($Revision: 6048 $)
 * CREATED     : 10-AUG-2015
 * LASTUPDATES : $Author: patrickw $ on $Date: 2014-12-08 17:58:16 +0800 (Mon, 08 Dec 2014) $
 * UPDATES     : 
 * NOTES       : 
 */
/* ---------------------------------------------------------------
   @(#)index.php        1.0 		10-AUG-2015
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
   Begin of index.php
   =============================================================== */

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__FILE__) . '/inc.php');
require_once($LAS_CFG->portal_root . '/lib/las_smarty.php');

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

$smarty = las_init_smarty($LAS_CFG->template_root);
$smarty->error_reporting = E_ALL & ~E_NOTICE;

function _get_lasterror() {
    $error = Cookie::getLastError();

    // Clear the last error
    Cookie::setLastError();
    return($error);
}


// Check if there is an error message
$smarty->assign('error', _get_lasterror());

//Support web redirect
if ( isset($_GET['caller']) && isset($_GET['redirect_url']) ) {
    $caller = $_GET['caller'];
    $redirect = $_GET['redirect_url'];
}

if ( isset($caller) && ($caller === 'web') && isset($redirect) ) {
    $smarty->assign('caller', $caller);
    $smarty->assign('redirect', $redirect);
}

if(isset($LAS_USER)){
    //If the user has sign in before
    $smarty->assign('user_id', $LAS_USER->id);
    $smarty->assign('user_cname', $LAS_USER->name->cname);
    $smarty->assign('user_ename', $LAS_USER->name->ename);
    $smarty->assign('role', $LAS_USER->role);
    
    $smarty->assign('wwwroot', $LAS_CFG->wwwroot);
    $smarty->assign('login_path', $LAS_CFG->login_path);
    $smarty->assign('logout_path', $LAS_CFG->logout_path);
    
    $smarty->display('page_layout_admin.tpl');
    

} else if ( isset($caller) && ($caller === 'oauth') ) {
    $smarty->assign('caller', $caller);
    $smarty->assign('redirect', $redirect);
    $smarty->display('oauth.tpl');
} else {
    
    //go to login page for web
    $smarty->assign('wwwroot', $LAS_CFG->wwwroot);
    $smarty->assign('login_path', $LAS_CFG->login_path);
    $smarty->assign('logout_path', $LAS_CFG->logout_path);
    
    $smarty->display('login.tpl');
}


/* ===============================================================
   End of index.php
   =============================================================== */
?>
