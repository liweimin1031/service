<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : OauthRefreshToken.php
 * AUTHOR      : Sandy Wong
 * SYNOPSIS    :
 * DESCRIPTION : CLMS Oauth Refresh Token Object
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision$)
 * CREATED     : 23-JUL-2013
 * LASTUPDATES : $Author$ on $Date$
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)OauthRefreshToken.php          1.0 23-JUL-2013
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
   Begin of OauthRefreshToken.php
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
 */
/*
 * @author      Sandy Wong
 * @testing
 * @warnings
 * @updates     
 */
class OauthRefreshToken{
    
    public $refresh_token = null;
    public $client_id = null;
    public $user_id = null;
    public $expires = null;
    public $scope = null;
    
    const COLLECTION = 'oauth_refresh_token';
    /**
     *
     * This function get oauth refresh token
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  bool Returns refresh token details by refresh token; otherwise,
                <code>false</code> is returned
     * @see
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function getRefreshToken($refresh_token) {
        $query = array('refresh_token' => $refresh_token);
        
        $item = MongoDao::searchOne(self::COLLECTION, $query);
        if($item){
            unset($item['_id']);
            return json_decode(json_encode($item));
        }
        return false;
    }
    
    /**
     *
     * This function remove oauth refresh code
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  bool Returns <code>true</code> if success; otherwise,
                <code>false</code> is returned
     * @see
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function deleteRefreshToken($refresh_token) {
        
        $query = array('refresh_toen' => $refresh_token);
        
        try{
            MongoDao::deleteList(self::COLLECTION, $query);
            return true;
        }
        catch (DbException $e) {
            return false;
        }
    }
    
    public function save(){
        
        MongoDao::save(self::COLLECTION, $this);
        $object = $this;
        unset($object->_id);
        return $object;
    }
}


/* ===============================================================
   End of OauthRefreshToken.php
   =============================================================== */
?>
