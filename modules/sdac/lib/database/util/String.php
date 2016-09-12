<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : String.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms String utility definition
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Jan 7, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) String.php              1.0 Jan 7, 2013
   by Michelle Hong


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */

namespace Clms\Tools\PhpDao\Util;
/* ===============================================================
   Begin of String.php
   =============================================================== */


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
 *
 * String utility definition.
 *
 * This class provide several string conversion method based on regular
 * expression replacement.
 * @package Php-Dao
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing see AstriTest\Astri\Core\Util\StringTest
 * @warnings
 * @updates
 */
class String
{
    /**
     *
     * Returns a camelized string from a lower case and underscored string by
     * replacing slash with double-colon and upper-casing each letter preceded
     * by an underscore.
     *
     * @version 1.0
     * @since  Version 1.0
     * @param  string $wordToCheck
     * @return string a camelCaps format string
     * @see
     * @author      Michelle Hong
     * @testing see StringUtilsTest::testCamelize
     * @warnings
     * @updates
     */
    public static function camelize($wordToCheck)
    {
        $tmp = $wordToCheck;
        $tmp = self::pregtr(
            $tmp,
            array('#/(.?)#e'    => "'::'.strtoupper('\\1')",
                    '/(^|_|-)+(.)/e' => "strtoupper('\\2')")
        );

        return $tmp;
    }

    /**
     *
     * Returns a camelCaps format string from a lower case and underscored
     * string by replacing slash with double-colon and upper-casing each
     * letter preceded by an underscore.
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $wordToCheck
     * @return string a camelCaps string
     * @see
     * @author      Michelle Hong
     * @testing see StringUtilsTest::testCameCaps
     * @warnings
     * @updates
     */
    public static function camelCaps($wordToCheck)
    {
        return lcfirst(self::camelize($wordToCheck));
    }

    /**
     *
     * Returns an underscore-formatted version string in lower case.
     *
     * @version 1.0
     * @since  Version 1.0
     * @param  string $wordToCheck  String to underscore-formatted.
     * @return string Underscored string.
     * @see
     * @author Michelle Hong
     * @testing see StringUtilsTest::testUnderscore
     * @warnings
     * @updates
     */
    public static function underscore($wordToCheck)
    {
        $tmp = $wordToCheck;
        $tmp = str_replace('::', '/', $tmp);
        $tmp = self::pregtr(
            $tmp,
            array('/([A-Z]+)([A-Z][a-z])/' => '\\1_\\2',
                    '/([a-z\d])([A-Z])/'     => '\\1_\\2')
        );

        return strtolower($tmp);
    }
    /**
     *
     * Call a regular expression replacement
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $search The words that needs to be replace
     * @param array $replacePairs the key value replacement pair
     * @return mixed
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function pregtr($search, $replacePairs)
    {
        return preg_replace(
            array_keys($replacePairs),
            array_values($replacePairs),
            $search
        );
    }

}


/* ===============================================================
   End of String.php
   =============================================================== */