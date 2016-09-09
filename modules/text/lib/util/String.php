<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : String.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms String utility definition
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 60 $)
 * CREATED     : Jan 7, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2013-03-20 11:14:49 +0800 (Wed, 20 Mar 2013) $
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

namespace Astri\Lib\Util;
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
 *
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

    /**
     * This function generate UUID string
     *
     * @since       Version 1.0
     * @return      Returns UUID string on success; otherwise, <code>false</cdoe>
     *              is returned
     * @see
     * @author      Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates
     */
    public static function generateUUID()
    {
        $result = false;

        if (preg_match('/^win/i', PHP_OS)) {
            $strVBS = dirname(__FILE__).DIRECTORY_SEPARATOR.'uuid.vbs';
            $result = exec('cscript //NoLogo "' . $strVBS . '"');
            $result = preg_replace("/^.*\{([^)]*)\}.*$/", '$1', $result);
            $result = strtolower($result);
        } else {
            $result = exec('uuidgen -t');
        }

        return ($result);
    }

    /**
     *
     * Remove all the style html tag except p, ul, li, b ans strong tag
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $content HTML content to clean
     * @return string A cleaned string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function cleanStyle($content)
    {
        $allow = '<p><ul><li><b><strong>';
        return strip_tags($content, '');
    }
    
    public static function startsWith($string, $tester){
        if (0 === strpos($string, $tester)) {
           return true;
        } else {
            return false;
        }
    }
    
    public static function convertChar($str)
    {
        $dbc = array(
                '０' , '１' , '２' , '３' , '４' ,
                '５' , '６' , '７' , '８' , '９' ,
                'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,
                'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' ,
                'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,
                'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' ,
                'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,
                'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' ,
                'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,
                'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' ,
                'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,
                'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' ,
                'ｙ' , 'ｚ' , '－' , '　'  , '：' ,
                '．' , '，' , '／' , '％' , '＃' ,
                '！' , '＠' , '＆' , '（' , '）' ,
                '＜' , '＞' , '＂' , '＇' , '？' ,
                '［' , '］' , '｛' , '｝' , '＼' ,
                '｜' , '＋' , '＝' , '＿' , '＾' ,
                '￥' , '￣' , '｀'
    
        );
    
        $sbc = array( //半角
                '0', '1', '2', '3', '4',
                '5', '6', '7', '8', '9',
                'A', 'B', 'C', 'D', 'E',
                'F', 'G', 'H', 'I', 'J',
                'K', 'L', 'M', 'N', 'O',
                'P', 'Q', 'R', 'S', 'T',
                'U', 'V', 'W', 'X', 'Y',
                'Z', 'a', 'b', 'c', 'd',
                'e', 'f', 'g', 'h', 'i',
                'j', 'k', 'l', 'm', 'n',
                'o', 'p', 'q', 'r', 's',
                't', 'u', 'v', 'w', 'x',
                'y', 'z', '-', ' ', ':',
                '.', ',', '/', '%', ' #',
                '!', '@', '&', '(', ')',
                '<', '>', '"', '\'','?',
                '[', ']', '{', '}', '\\',
                '|', '+', '=', '_', '^',
                '￥','~', '`'
    
        );
        return str_replace( $dbc, $sbc, $str );  //全角到半角
    
    }


}


/* ===============================================================
   End of String.php
   =============================================================== */