<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : ClmsDOMDocument.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Clms DomDocument to handle general html
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6700 $)
 * CREATED     : May 23, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-02-11 11:00:26 +0800 (Wed, 11 Feb 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) ClmsDOMDocument.php              1.0 May 23, 2013
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
   Begin of ClmsDOMDocument.php
   =============================================================== */
namespace Astri\Lib\Util;

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
   Class definition
   --------------------------------------------------------------- */

/**
 *
 * Html function to handle dom load and save issue
 *
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class ClmsDOMDocument extends \DOMDocument
{

    /**
     *
     * Load html source
     *
     * convert the source into utf-8 encoding
     * @version 1.0
     * @since Version 1.0
     * (non-PHPdoc)
     * @see DOMDocument::loadHTML()
     * @param string $source html source
     * @param string $encoding html encoding
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function loadHTML($source, $encoding="UTF-8")
    {
        libxml_use_internal_errors(true);
        $source = mb_convert_encoding($source, 'HTML-ENTITIES', $encoding);
        @parent::loadHTML($source);
        libxml_use_internal_errors(false);
    }

    /**
     *
     * Save only the body section of the html
     *
     * @version 1.0
     * @since  Version 1.0
     * @return string html string in the body section
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getBodyHTML()
    {
        if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
            $body = $this->getElementsByTagName('body')->item(0);
            $content = '';
            foreach ($body->childNodes as $childNode) {
                $content .= $this->saveHTML($childNode);
            }
            return $content;

        } else {
            $content = preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si",
                    "!</body></html>$!si"),
                    "",
                    $this->saveXML());

            return $content;
        }
    }
    
    public function getTextContent(){
        
        
       return trim(str_replace('\\', '', preg_replace('/ {2,}/', ' ', String::convertChar( $this->textContent))));
    }
    
    

    /**
     *
     * Get all the images in the DomDocument
     *
     * @version 1.0
     * @since  Version 1.0
     * @return array an array of url
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getImages()
    {
        $files= array();
        $images = $this->getElementsByTagName('img');
        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            if (!empty($src)) {
                $files[] = $src;
            }
        }
        return $files;
    }

}

/* ---------------------------------------------------------------
   Interface definition
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */



/* ===============================================================
   End of ClmsDOMDocument.php
   =============================================================== */