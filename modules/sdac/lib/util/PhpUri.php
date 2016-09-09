<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : PhpUri.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Apr 2, 2015
 * LASTUPDATES : $Author: csdhong $ on $Date: 11:49:48 AM Apr 2, 2015 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) PhpUri.php              1.0 Apr 2, 2015
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
   Begin of PhpUri.php
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
   Function definition
   --------------------------------------------------------------- */
class PhpUri{
    /**
     * http(s)://
     * @var string
     */
    public $scheme;
    /**
     * www.example.com
     * @var string
     */
    public $authority;
    /**
     * /search
     * @var string
     */
    public $path;
    /**
     * ?q=foo
     * @var string
     */
    public $query;
    /**
     * #bar
     * @var string
     */
    public $fragment;
    private function __construct($string){
        preg_match_all('/^(([^:\/?#]+):)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?$/', $string ,$m);
        $this->scheme = $m[2][0];
        $this->authority = $m[4][0];
        $this->path = (empty($m[5][0]))?'/':$m[5][0];
        $this->query = $m[7][0];
        $this->fragment = $m[9][0];
    }
    private function to_str(){
        $ret = "";
        if(!empty($this->scheme)) $ret .= "$this->scheme:";
        if(!empty($this->authority)) $ret .= "//$this->authority";
        $ret .= $this->normalize_path($this->path);
        if(!empty($this->query)) $ret .= "?$this->query";
        if(!empty($this->fragment)) $ret .= "#$this->fragment";
        return $ret;
    }
    private function normalize_path($path){
        if(empty($path)) return '';
        $normalized_path = $path;
        $normalized_path = preg_replace('`//+`', '/' , $normalized_path, -1, $c0);
        $normalized_path = preg_replace('`^/\\.\\.?/`', '/' , $normalized_path, -1, $c1);
        $normalized_path = preg_replace('`/\\.(/|$)`', '/' , $normalized_path, -1, $c2);
        $normalized_path = preg_replace('`/[^/]*?/\\.\\.(/|$)`', '/' , $normalized_path, -1, $c3);
        $num_matches = $c0 + $c1 + $c2 + $c3;
        return ($num_matches > 0) ? $this->normalize_path($normalized_path) : $normalized_path;
    }
    /**
     * Parse an url string
     * @param string $url the url to parse
     * @return phpUri
     */
    public static function parse($url){
        $uri = new phpUri($url);
        return $uri;
    }
    /**
     * Join with a relative url
     * @param string $relative the relative url to join
     * @return string
     */
    public function join($relative){
        $uri = new phpUri($relative);
        switch(true){
        	case !empty($uri->scheme): break;
        	case !empty($uri->authority): break;
        	case empty($uri->path):
        	    $uri->path = $this->path;
        	    if(empty($uri->query)) $uri->query = $this->query;
        	case strpos($uri->path, '/') === 0: break;
        	default:
        	    $base_path = $this->path;
        	    if(strpos($base_path, '/') === false){
        	        $base_path = '';
        	    } else {
        	        $base_path = preg_replace ('/\/[^\/]+$/' ,'/' , $base_path);
        	    }
        	    if(empty($base_path) && empty($this->authority)) $base_path = '/';
        	    $uri->path = $base_path . $uri->path;
        }
        if(empty($uri->scheme)){
            $uri->scheme = $this->scheme;
            if(empty($uri->authority)) $uri->authority = $this->authority;
        }
        return $uri->to_str();
    }
    
    public static function rel2abs($rel, $base) {
        /* return if already absolute URL */
        if (parse_url ( $rel, PHP_URL_SCHEME ) != '')
            return ($rel);
            
            /* queries and anchors */
        if ($rel [0] == '#' || $rel [0] == '?')
            return ($base . $rel);
            
            /*
         * parse base URL and convert to local variables: $scheme, $host, $path
         */
        extract ( parse_url ( $base ) );
        
        /* remove non-directory element from path */
        $path = preg_replace ( '#/[^/]*$#', '', $path );
        
        /* destroy path if relative url points to root */
        if ($rel [0] == '/')
            $path = '';
            
            /* dirty absolute URL */
        $abs = '';
        
        /* do we have a user in our URL? */
        if (isset ( $user )) {
            $abs .= $user;
            
            /* password too? */
            if (isset ( $pass ))
                $abs .= ':' . $pass;
            
            $abs .= '@';
        }
        
        $abs .= $host;
        
        /* did somebody sneak in a port? */
        if (isset ( $port ))
            $abs .= ':' . $port;
        
        $abs .= $path . '/' . $rel;
        
        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array (
                '#(/\.?/)#',
                '#/(?!\.\.)[^/]+/\.\./#' 
        );
        for($n = 1; $n > 0; $abs = preg_replace ( $re, '/', $abs, - 1, $n )) {
        }
        
        /* absolute URL is ready! */
        return ($scheme . '://' . $abs);
    }
}


/* ===============================================================
   End of PhpUri.php
   =============================================================== */
?>