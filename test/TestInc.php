<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : TestInc.php
 * AUTHOR      : Patrick C. K. Wu
 * SYNOPSIS    :
 * DESCRIPTION : CLMS Main Include Test
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 351 $)
 * CREATED     : 31-MAY-2013
 * LASTUPDATES : $Author: patrickw $ on $Date: 2013-05-31 01:09:06 +0800 (Fri, 31 May 2013) $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)TestInc.php              1.0 31-MAY-2013
   by Patrick C. K. Wu


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */


/* ===============================================================
   Begin of TestInc.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__FILE__) . '/../inc.php');


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
class TestInc {
    public function __construct() {
        global $LAS_CFG, $LAS_USER;
        $link = '';

        header('Content-type: text/html; charset=utf-8');

        echo "\$LAS_CFG:<br />\n";
        foreach ( $LAS_CFG as $key => $value ) {
            if ( (gettype($value) !== 'object') && ($key !== 'salt') ) {
                echo "$key: $value<br />\n";
            }
        }
        if ( isset($LAS_USER) ) {
            echo "<br />\$LAS_USER:<br />\n";
            foreach ( $LAS_USER as $key => $value ) {
                if ( gettype($value) !== 'object' ) {
                    echo "$key: $value<br />\n";
                }
            }
            $link = "Click <a href=\""
                  . $LAS_CFG->logout_path . '?scode=' . $LAS_USER->scode
                  . "\">here</a> to logout.<br />\n";
            echo "$link";
        }
    }
}

$test = new TestInc;


/* ===============================================================
   End of TestInc.php
   =============================================================== */
?>
