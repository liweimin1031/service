<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : OauthAuthorizationCode.php
 * AUTHOR      : Sandy Wong
 * SYNOPSIS    :
 * DESCRIPTION : CLMS Oauth Authorization Code Object
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision$)
 * CREATED     : 23-JUL-2013
 * LASTUPDATES : $Author$ on $Date$
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)OauthAuthorizationCode.php          1.0 23-JUL-2013
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
   Begin of OauthAuthorizationCode.php
   =============================================================== */
namespace Las\Core\Oauth;


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */

use Las\Tools\Mongo\MongoDao;
use Las\Tools\Mongo\Exception\DbException;

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
class OauthAuthorizationCode{
    
    const COLLECTION ='oauth_authorization_code';
    
    public $authorization_code = null;
    public $client_id = null;
    public $user_id = null;
    public $redirect_uri = null;
    public $expires = null;
    public $scope = null;
    
    /**
     *
     * This function get oauth authorization code
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  bool Returns authorization code details by authorization code; otherwise,
                <code>false</code> is returned
     * @see
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function getAuthorizationCode($auth_code) {
        $query = array('authorization_code' => $auth_code);
        
        try{
            $item =  MongoDao::searchOne(self::COLLECTION, $query);
            unset($item['_id']);
            return $item;
        }
        catch (DbException $e) {
            return false;
        }
    }
    
    /**
     *
     * This function remove oauth authorization code
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
    public static function deleteAuthorizationCode($auth_code) {
        
        $query = array('authorization_code' => $auth_code);
        
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
   End of OauthAuthorizationCode.php
   =============================================================== */
?>
