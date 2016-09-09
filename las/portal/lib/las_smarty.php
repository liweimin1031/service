<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : las_smarty.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS Portal Login
 * SEE ALSO    :
 * VERSION     : 1.1 ($Revision: 4708 $)
 * CREATED     : 28-JUL-2015
 * LASTUPDATES : $Author: patrickw $ on $Date: 2014-09-01 15:53:43 +0800 (Mon, 01 Sep 2014) $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
  @(#)las_smarty.php            1.0 			28-JUL-2015
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
  Begin of las_smarty.php
  =============================================================== */


/* ---------------------------------------------------------------
  Included Library
  --------------------------------------------------------------- */
require_once(dirname(__FILE__) . '/../../../lib/smarty/libs/Smarty.class.php');


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
 * This function initialize Smarty
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
function las_init_smarty($path) {
    $cache = $path . '/cache';

    if (!isset($GLOBALS['smarty'])) {
        $GLOBALS['smarty'] = new Smarty;
        $GLOBALS['smarty']->template_dir = $path;
        $GLOBALS['smarty']->compile_dir = $cache;
    }
    return $GLOBALS['smarty'];
}


/* ===============================================================
  End of las_smarty.php
  =============================================================== */
?>