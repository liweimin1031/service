<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : OauthSession.php
 * AUTHOR      : Sandy Wong
 * SYNOPSIS    :
 * DESCRIPTION : CLMS OauthSession Object
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision$)
 * CREATED     : 22-JUL-2013
 * LASTUPDATES : $Author$ on $Date$
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)OauthSession.php          1.0 22-JUL-2013
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
   Begin of OauthSession.php
   =============================================================== */
namespace Las\Core\Oauth;


use Las\Tools\Mongo\MongoDao;
use Las\Tools\Mongo\Exception\DbException;
use Las\Core\Util\String;
use Las\Core\Util\Cookie;
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
 * Main OauthSession class
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
class OauthSession {
    const SESSION_LIFE_TIME = 1800; // 30 mins
    
    const COLLECTION = 'oauth_session';
    
    
    public $sessionkey = null;
    public $sessiontoken = null;
    public $response_type = null;
    public $client_id = null;
    public $redirect_uri = null;
    public $state = null;
    public $scope = null;
    public $timeout = null;

    /**
     *
     * This function get oauth sessions
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  bool Returns <code>true</code> if session is valid; otherwise,
                <code>false</code> is returned
     * @see
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function checkSession() {
        $session = Cookie::getOauthSession();
        $sessionkey = Cookie::getOauthSessionKey();
        
        try {
            $oauthSession = OauthSession::getBySession($sessionkey, $session);
            
            if ( $oauthSession ) {
                $_SESSION['oauth']['response_type'] = $oauthSession->response_type;
                $_SESSION['oauth']['client_id'] = $oauthSession->client_id;
                $_SESSION['oauth']['redirect_uri'] = $oauthSession->redirect_uri;
                $_SESSION['oauth']['state'] = $oauthSession->state;
                $_SESSION['oauth']['scope'] = $oauthSession->scope;
                return true;                
            }
        }
        catch (DbException $e) {
        }

        return(false);
    }

    /**
     *
     * This function save oauth session to DB
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string $responseType   The oauth response type
     * @param   string $clientId   The client id
     * @param   string $redirectURI   The client redirect URI
     * @param   string $state   The state
     * @param   string $scope   The scope
     * @return  nil
     * @see
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function createSession($responseType, $clientId, $redirectURI, $state, $scope) {
        $session = new OauthSession();
        $session->response_type = $responseType;
        $session->client_id = $clientId;
        $session->redirect_uri = $redirectURI;
        $session->state = $state;
        $session->scope = $scope;
        $session->sessionkey = String::generateRandomString(20);
        
        $session->sessiontoken = md5($session->sessionkey . ',' . $clientId);
        $session->timeout = time() + OauthSession::SESSION_LIFE_TIME;
        
        try {
            
            Cookie::setOauthSessionKey($session->sessionkey, $session->timeout);
            Cookie::setOauthSession($session->sessiontoken, $session->timeout);
            $session->save();
            unset($session->_id);
            return $session;
        }
        catch (DbException $e) {
            return(false);
        }
    }
    
    public function save(){
        return MongoDao::save(self::COLLECTION, $this);
    }

    /**
     *
     * This function delete the oauth session
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  nil
     * @see
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function deleteSession() {
        try {
            $sessiontoken = Cookie::getOauthSession();
            $sessionkey = Cookie::getOauthSessionKey();

            $keys = array (
                "sessiontoken"=>$sessiontoken,
                "sessionkey"=> $sessionkey
            );
            
            MongoDao::deleteList(self::COLLECTION, $keys);
            // delete cookie
            Cookie::setOauthSession('', 0);
            Cookie::setOauthSessionKey('', 0);
        }
        catch (DbException $e) {
        }
    }
    
    
    public static function getBySession($sessionkey, $sessiontoken)
    {
        $keys = array (
                "sessiontoken"=>$sessiontoken,
                "sessionkey"=>$sessionkey
        );
        $obj= MongoDao::searchOne(self::COLLECTION, $keys);
        if($obj){
            $obj= json_decode(json_encode($obj));
            unset($obj->_id);
            return $obj;
        } 
        return false;
        
    }
}


/* ===============================================================
   End of OauthSession.php
   =============================================================== */
?>
