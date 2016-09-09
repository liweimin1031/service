<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : HTML.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : HTML related utility
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 7196 $)
 * CREATED     : 29-JAN-2014
 * LASTUPDATES : $Author: yzlu $ on $Date: 2015-06-10 11:59:56 +0800 (Wed, 10 Jun 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) HTML.php                1.0 29-JAN-2014
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
   Begin of HTML.php
   =============================================================== */
namespace Astri\Lib\Util;
use Astri\Lib\UtilClmsDOMDocument;

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
 * CLS HTML Utility
 *
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class HTML
{

    /**
     *
     * Extract all images from html
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $html HTML string
     * @return array Returns an array of image list
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function extractImages($html)
    {
        $result=array();
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $tags = $doc->getElementsByTagName('img');
        foreach ($tags as $tag) {
            $result[]= $tag->getAttribute('src');
        }
        return $result;
    }

    /**
     *
     * Function description goes here
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $html HTML code
     * @param int $length Max length of the return string. NULL means no cut.
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function getPureText($html, $length = 200)
    {
        // ----- remove control characters -----
        $html = str_replace("\r", '', $html);    // --- replace with empty space
        $html = str_replace("\n", ' ', $html);   // --- replace with space
        $html = str_replace("\t", ' ', $html);   // --- replace with space

        // ----- remove multiple spaces -----
        $html = trim(preg_replace('/ {2,}/', ' ', $html));
        
        
        if($length == null){
            return trim(strip_tags($html));
        }
        

        return mb_strimwidth(trim(strip_tags($html)), 0, $length, '...', "utf-8");

    }

    /**
     *
     * Function remove styles, css, javascripts in html
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  $html
     * @param   $html    input html
     * @param   $tags  array('head', 'script', 'link', 'style', 'title', 'meta')
     * @see
     * @author  Yunzhao Lu
     * @testing
     * @warnings
     * @updates
     */
    public static function removeStyleSheets($html, $tags) {
        $document = new \DOMDocument();
        libxml_use_internal_errors(true);
        $config = array(
           'indent'         => true,
           'output-xhtml'   => true,
           'wrap'           => 0);
        $tidy = tidy_parse_string($html, $config, 'UTF8');
        $tidy->cleanRepair();
        @$document->loadHTML($tidy);
        libxml_use_internal_errors(false);
        foreach($tags as $tag) {
            self::removeNodeByType($document, $tag);
        }
    
        $xpath = new \DOMXPath($document);
        $body = $xpath->query('/html/body');
        $result= self::get_inner_html($body->item(0));
        return $result;
    }

    /**
     *
     * Function remove tag in doc
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   $doc     document
     * @param   $tagName HTML tag
     * @return  
     * @see
     * @author  Yunzhao Lu
     * @testing
     * @warnings
     * @updates
     */
    public static function removeNodeByType($doc, $tagName) {
        $nodes = $doc->getElementsByTagName($tagName);
        while ($nodes->length > 0) {
            $node = $nodes->item(0);
            self::removeNode($node);
        }
    }
    /**
     *
     * Function remove nodes
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   $node object
     * @return  
     * @see
     * @author  Yunzhao Lu
     * @testing
     * @warnings
     * @updates
     */
    public static function removeNode(&$node) {
        $pnode = $node->parentNode;
        self::removeChildren($node);
        $pnode->removeChild($node);
    }
    /**
     *
     * Function remove children of node
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   $node      node
     * @return  
     * @see
     * @author  Yunzhao Lu
     * @testing
     * @warnings
     * @updates
     */
    public static function removeChildren(&$node) {
        while ($node->firstChild) {
            while ($node->firstChild->firstChild) {
                self::removeChildren($node->firstChild);
            }

            $node->removeChild($node->firstChild);
        }
    }
    /**
     *
     * Function gets internal html
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   $node
     * @return  html in node
     * @see
     * @author  Yunzhao Lu
     * @testing
     * @warnings
     * @updates
     */
    public static function get_inner_html($node) {
        $innerHTML= '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveHTML( $child );
        }
    
        return $innerHTML;
    }
}

/* ---------------------------------------------------------------
   Interface definition
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */



/* ===============================================================
   End of HTML.php
   =============================================================== */
