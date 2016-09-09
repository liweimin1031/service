<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : OauthAccessToken.php
 * AUTHOR      : Sandy Wong
 * SYNOPSIS    :
 * DESCRIPTION : CLMS Oauth Access Token Object
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision$)
 * CREATED     : 23-JUL-2013
 * LASTUPDATES : $Author$ on $Date$
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)OauthAccessToken.php          1.0 23-JUL-2013
   by Sandy Wong


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */


/* ===============================================================
   Begin of OauthAccessToken.php
   =============================================================== */
namespace Las\Core\Oauth;


use Las\Tools\Mongo\MongoDao;
use Las\Tools\Mongo\Exception\DbException;
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
 * Main OauthAccessToken class
 *
 * @since       Version 1.0.00
 * @param       nil
 * @return      nil
 * @see
 * @author      Sandy Wong
 * @testing
 * @warnings
 * @updates     
 */
class OauthAccessToken{
    
    public $access_token = null;
    public $client_id = null;
    public $user_id = null;
    public $expires = null;
    public $scope = null;
    
    const COLLECTION = 'oauth_access_token';
    /**
     *
     * This function get oauth access token
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  bool Returns access token details by access token; otherwise,
                <code>false</code> is returned
     * @see
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function getAccessToken($access_token) {
        $query = array('access_token' => $access_token);
        try{
            $doc = MongoDao::searchOne(self::COLLECTION, $query);
            unset($doc['_id']);
            return $doc;
        } catch(DbException $e){
            return false;
        }
        
    }
    
    public function save(){
        try {
            MongoDao::save ( self::COLLECTION, $this );
            unset ( $this->_id);
            return $this;
        } catch ( DbException $e ) {
            return false;
        }
    }
}


/* ===============================================================
   End of OauthAccessToken.php
   =============================================================== */
?>
