<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Ajax.php
 * AUTHOR      : Patrick C. K. Wu
 * SYNOPSIS    :
 * DESCRIPTION : LAS Ajax Class
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 1009 $)
 * CREATED     : 16-JUN-2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2013-07-08 10:56:32 +0800 (Mon, 08 Jul 2013) $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)Ajax.php                 1.0 16-JUN-2013
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
   Begin of Ajax.php
   =============================================================== */
namespace Las\Core\Util;


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
   Function definition
   --------------------------------------------------------------- */

/**
 * Main Ajax class
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
class Ajax {
    /**
     *
     * This function create standard success message object with
     * <code>data</code>
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   object data     The data to be included
     * @return  Returns the JSON message in string
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function createDataMsg($data=true) {
        $msg = new \StdClass;
        $msg->success = true;
        $msg->data = $data;

        $result = json_encode($msg);
        return($result);
    }

    /**
     *
     * This function create standard error message object with
     * <code>reason</code>.  If error <code>code</code> is not provided,
     * <code>LAS_ERROR_UNKNOWN</code> will be used.
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string reason   The reason of error in string
     * @param   int code        The error code
     * @param   object data     The data to be included
     * @return  Returns the JSON message in string
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function createErrorMsg($reason, $code=LAS_ERROR_UNKNOWN, $data=null) {
        if ( isset($reason) ) {
            $msg = new \StdClass;
            $msg->success = false;
            $msg->data = "";
            $msg->error = new \StdClass;
            $msg->error->code = $code;
            $msg->error->reason = $reason;
            if ( !empty($data) ) {
                $msg->data = $data;
            }

            $result = json_encode($msg);
            return($result);
        }
        return(false);
    }

    /**
     *
     * This function create standard error message object with
     * <code>reason</code>.  If error <code>code</code> is not provided,
     * <code>LAS_ERROR_UNKNOWN</code> will be used.
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string reason   The reason of error in string
     * @param   int code        The error code
     * @param   object data     The data to be included
     * @return  Returns the JSON message in string
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function createErrorMsgByCode($code=0, $data=null) {
        global $LAS_CFG;

        $string = $LAS_CFG->ErrorMsg[$code];
        if ( !isset($string) ) {
            $string = $LAS_CFG->ErrorMsg[LAS_ERROR_UNKNOWN];
        }
        return(Ajax::createErrorMsg($string, $code, $data));
    }

    /**
     *
     * This function parse the JSON message from HTTP request
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string data     The JSON data in string format
     * @return  Returns the data in object format
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function parseJson($data) {
        $result = false;
        if ( isset($data) ) {
            $strJson = json_encode($data);
            if ( isset($strJson) ) {
                $json = json_decode($strJson);
                if ( gettype($json) == 'string' ) {
                    //$json = stripcslashes($json);
                    $json = json_decode($json);
                }
                $result = $json;
            }
        }

        return($result);
    }

}

/* ===============================================================
   End of Ajax.php
   =============================================================== */
?>
