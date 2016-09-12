<?php 
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : DaoVersion.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : PHP Dao version checking
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Nov 1, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2:54:08 PM Nov 1, 2013 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) DaoVersion.php           1.0 Aug 6, 2013
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
   Begin of DaoVersion.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */

namespace Clms\Tools\PhpDao;

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
 * 
 * PHP DAO version definition
 * @package Php-Dao
 * @since  Version 
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class DaoVersion{
    
    /**
     * version number
     * @var  string
     * @since  Version 1.3
     * @author      Michelle Hong
     * @updates
     */
    const version= '1.2.3';
    
    /**
     * 
     * Get the current PHP DAO version
     *
     * @since  Version 1.2.2
     * @return string the DAO version number
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function getVersion(){
        return self::version;
    }
}

/*
 * =============================================================== 
 * End of DaoVersion.php
 * ===============================================================
 */