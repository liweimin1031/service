<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Time.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS Time Class
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 1009 $)
 * CREATED     : 29-JAN-2016
 * LASTUPDATES : $Author: michellehong $ on $Date: 2013-07-08 10:56:32 +0800 (Mon, 08 Jul 2013) $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)Time.php                 1.0 				29-JAN-2016
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
   Begin of Time.php
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
 * Main Time class
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
class Time {
    /**
     *
     * Get Current Date & Time
     *
     * @version 1.0
     * @since   Version    1.0
     * @param   Boolean    True: return datetime with microsecond
     * @return  String
     * @see
     * @author  Kary Ho
     * @warnings
     * @updates
     */
    public static function getCurrentDateTime($microsecond=false) {
    	if ($microsecond){
	    	$time = microtime(true);
	    	
	    	$micro = sprintf("%06d",($time - floor($time)) * 1000000);
	    	$datetime = new \DateTime( date('Y-m-d H:i:s.'.$micro, $time) );
	    	
	    	return $datetime->format("Y-m-d H:i:s.u");
    	}
    	else{
    		return date('Y-m-d H:i:s');
    	}
    }
}

/* ===============================================================
   End of Time.php
   =============================================================== */
?>