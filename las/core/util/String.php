<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : String.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : CLMS String utility definition
 * SEE ALSO    :
 * VERSION     : 1.1 ($Revision: 6976 $)
 * CREATED     : 07-NOV-2013
 * LASTUPDATES : $Author: patrickw $ on $Date: 2015-03-20 11:07:40 +0800 (Fri, 20 Mar 2015) $
 * UPDATES     : 20-MAR-2015    - Support Google account name (Bug# 6840)
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) String.php              1.0 07-NOV-2013
                                1.1 20-MAR-2015
   by Michelle Hong


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */

namespace Las\Core\Util;
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
 * @package core\util
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
        $tmp = self::pregtr($tmp,
            array(
                '#/(.?)#e' => "'::'.strtoupper('\\1')",
                '/(^|_|-)+(.)/e' => "strtoupper('\\2')"
            )
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
     * Returns ture if the string contains double characters specified
     *
     * @version 1.0
     * @since   Version 1.1.00
     * @param   string $haystack     The string to search in.
     * @param   string $needle       The character to be checked
     * @return  Returns true if occurs; otherwise, false is returned
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    public static function hasDoubleChar($haystack, $needle) {
        $search = $needle . $needle;
        $result = strpos($haystack, $search);
        if ( $result !== false ) {
            $result = true;
        }
        return($result);
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
        $tmp = self::pregtr($tmp,
            array(
                '/([A-Z]+)([A-Z][a-z])/' => '\\1_\\2',
                '/([a-z\d])([A-Z])/' => '\\1_\\2'
            )
        );

        return strtolower($tmp);
    }
    
    /**
     * 
     * Return a string with lowercase format and change all the space
     * to underscore
     *
     * @since  Version 1.0
     * @param unknown $wordToCheck
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function lowerUnderscore($wordToCheck)
    {
        $tmp = str_replace('/\s+/g', '_', $wordToCheck);
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
        return preg_replace(array_keys($replacePairs),
            array_values($replacePairs), $search
        );
    }

    /**
     * This function generate and return a random string with specified
     * <code>length</code>
     *
     * @since   Version 1.0
     * @param   string $pool    The string of pool
     * @param   int $length     The length of the random string
     * @return  Returns the random string
     * @see
     * @author      Yunzhao Lu
     * @testing
     * @warnings
     * @updates
     */
    public static function generateRandomFromPool($length=20, $pool='') {
        if(empty($pool)) {
            $pool = '012345678'
                  . 'abcdefghijklmnopqrstuvwxyz'
                  . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $iLen = strlen($pool);
        $result = '';

        mt_srand((double) microtime() * 1000000);
        for ( $i=0; $i<$length; $i++ ) {
            $result .= substr($pool, (mt_rand() % ($iLen)), 1);
        }
        return($result);
    }

    /**
     * This function generate and return a random string with specified
     * <code>length</code>
     *
     * @since   Version 1.0
     * @param   int $length     The length of the random string
     * @return  Returns the random string
     * @see
     * @author      Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates
     */
    public static function generateRandomString($length=20) {
        $haystack = '012345678'
                  . 'abcdefghijklmnopqrstuvwxyz'
                  . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $iLen = strlen($haystack);
        $result = '';

        mt_srand((double) microtime() * 1000000);
        for ( $i=0; $i<$length; $i++ ) {
            $result .= substr($haystack, (mt_rand() % ($iLen)), 1);
        }
        return($result);
    }

    /**
     * This function generate and return a random salt with specified
     * <code>length</code>
     *
     * @since   Version 1.0
     * @param   int $length     The length of the salt
     * @return  Returns the random string
     * @see
     * @author      Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates
     */
    public static function generateSalt($length=20) {
        $haystack = '012345678'
                  . 'abcdefghijklmnopqrstuvwxyz'
                  . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                  . '~,./<>?!@#%^&*_+-=;: ';
        $iLen = strlen($haystack);
        $result = '';

        mt_srand((double) microtime() * 1000000);
        for ( $i=0; $i<$length; $i++ ) {
            $result .= substr($haystack, (mt_rand() % ($iLen)), 1);
        }
        return($result);
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
            $strVBS = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'uuid.vbs';
            $result = exec('cscript //NoLogo "' . $strVBS . '"');
            $result = preg_replace("/^.*\{([^)]*)\}.*$/", '$1', $result);
            $result = strtolower($result);
        } else {
            $result = exec('uuidgen -t');
        }

        return ($result);
    }

    /**
     * This function check if the <code>url</code> is valid with the
     * <code>scheme</code> if any
     *
     * @since   Version 1.0
     * @param   string $url     The URL to be checked
     * @param   array $schemes  The schemes allowed
     * @return  Returns <code>true</code> is the URL is valid; otherwise,
                <code>false</cdoe> is returned
     *         
     * @see
     * @author  Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates
     */
    public static function parseURL($url, $schemes=null) {
        $result = false;
        $objURL = parse_url($url);

        if ( $objURL && isset($objURL['scheme']) && isset($objURL['host']) ) {
            if ( isset($schemes) && is_array($schemes) ) {
                foreach ($schemes as $scheme) {
                    if ( strcasecmp($objURL['scheme'], $scheme) == 0 ) {
                        $result = true;
                        break;
                    }
                }
            }
        }

        return ($result);
    }

    /**
     *
     * Check if a string is start with a needle
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $haystack
     * @param string $needle
     * @return boolean if match is found, return true, otherwise false
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function startsWith($haystack, $needle, $ignoreCase= false)
    {
        if ($ignoreCase) {
            $haystack = strtolower ($haystack);
            $needle= strtolower ($needle);
        }
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }



    /**
     *
     * Convert a html content title in utf-8 format
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $content html string
     * @return boolean|string|unknown
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function getHtmlTitle($content)
    {
        $tempArray = explode("</head>", $content, 2);
        list($header) = $tempArray;
        //
        preg_match("/\<title\>(.*)\<\/title\>/", $content, $title);
        if (isset($title[1])) {
            $target = $title[1];
        } else {
            return false;
        }//

        preg_match('@<meta\s+content="([\w/]+)(;\s+charset=([^\s"]+))?@i',
            $header, $matches
        );

        if (isset($matches[3])) {
            $charset = $matches[3];
            return iconv($charset, "utf-8", $target);
        } else {
            preg_match(
                '@<meta\s+http-equiv=("|\')Content-Type("|\')\s+content=("|\')([\w/]+)(;\s*+charset=([^\s("|\')]+))?@i',
                $header, $matches
            );
            if (isset($matches[6])) {
                $charset = $matches[6];
                if (strtolower($charset) == 'utf-8') {
                    return $target;
                }
                return iconv($charset, "UTF-8//IGNORE//TRANSLIT", $target);
            } else {
                preg_match(
                    '@<meta\s+http-equiv=Content-Type\s+content="([\w/]+)(;\s*+charset=([^\s"]+))?@i',
                    $header, $matches
                );
                if (isset($matches[3])) {
                    $charset = $matches[3];
                    if (strtolower($charset) == 'utf-8') {
                        return $target;
                    }
                    return iconv($charset, "UTF-8//IGNORE//TRANSLIT", $target);
                }
            }
        }

        //bad fix here for example in tudou <meta charset="GBK">
        preg_match('@charset\s*=\s*["|\']\s*([^\s"\']+)["|\']?@i', $header,
            $matches
        );
        if (isset($matches[1])) {
            $charset = trim($matches[1]);
            if (strtolower($charset) == 'utf-8') {
                return $target;
            }
            return iconv($charset, "UTF-8//IGNORE//TRANSLIT", $target);
        }

        return $target;
    }

    /**
     *
     * Function to check the image extention
     * @param string $full_path_to_image
     * @return string file extrension
     * @since Ver 2.2
     * Internal Use Only
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $imgFile (path)
     * @return extension
     * @see
     * @author      Yunzhao Lu
     * @testing
     * @warnings
     * @updates
     */
    public static function getImgExt($imgFile) {
        $extension = 'png';
        if($iType = exif_imagetype($imgFile))
        {
            $extension = image_type_to_extension($iType, false);
        }
        $REP = array(
            'jpeg' => 'jpg',
            'tiff' => 'tif',
        );
        $extension = str_replace(array_keys($REP),
                                 array_values($REP),
                                 $extension);

        return $extension;
    }
    
    /**
     *
     * Check if a string is end with a needle
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $haystack
     * @param string $needle
     * @param string $ignoreCase
     * @return boolean if match is found, return true, otherwise false
     * @see
     * @author      Sandy Wong
     * @testing
     * @warnings
     * @updates
     */
    public static function endsWith($haystack, $needle, $ignoreCase= false)
    {
        $length = strlen($needle);
        $index = strlen($haystack) - $length;
        if ($index < 0) {
            return false;
        }
        if ($ignoreCase) {
            $haystack = strtolower ($haystack);
            $needle= strtolower ($needle);
        }
        return (substr($haystack, $index, $length) === $needle);
    }
    
}

/* ===============================================================
   End of String.php
   =============================================================== */
