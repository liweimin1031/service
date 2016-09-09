<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : LasOauthServer.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Aug 25, 2015
 * LASTUPDATES : $Author: csdhong $ on $Date: 6:35:57 PM Aug 25, 2015 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) LasOauthServer.php              1.0 Aug 25, 2015
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
   Begin of LasOauthServer.php
   =============================================================== */
namespace Las\Core\Oauth;


use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\RefreshToken;
use OAuth2\GrantType\OAuth2\GrantType;
use Las\Core\Util\Log;
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

class LasOauthServer {
    
    private $m_storage;
    private $m_config;
    private $m_server;
    
    public function __construct(){
        $this->m_storage = new OauthStorage;
        $this->m_config = array(
                'auth_code_lifetime'=>60
        );
        
        $this->m_server = new \OAuth2\Server($this->m_storage,$this->m_config);
        $this->m_server->addGrantType(new ClientCredentials($this->m_storage));
        $this->m_server->addGrantType(new AuthorizationCode($this->m_storage));
        $this->m_server->addGrantType(new RefreshToken($this->m_storage));
    }
    
    
    
    /**
     * 
     * Verify the Resource Request
     *
     * Currently we did not verify any scope of the resource request. May enhance 
     * this part later if we want to have scope information.
     * 
     * @since  Version 
     * @param unknown $request
     * @param unknown $response
     * @return boolean
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function verifyResourceRequest($request, $response){
        if (!$this->m_server->verifyResourceRequest($request, $response)) {
            $response->send();
            return false;
        }
        return true;
    }
    
    /**
     * Retrieve access token and verify it
     *
     * @return      the access token data: access_token, client_id, user_id, expires, scope
     * @since       Version 1.0.00
     * @see
     * @author      Sandy Wong
     */
    public function getAccessTokenData($noResponse=false) {
        // get server
        $server = $this->m_server;
    
        // handle request
        $request = \OAuth2\Request::createFromGlobals();
        $data = $server->getAccessTokenData($request);
        
       
        if (isset($data)) {
            // check if client disabled
            $client = $this->getClientDetails($data['client_id']);
            if ($client) {
                // return token data
                return $client;
            } else {
                //error_log("getAccessTokenData: cannot find OAuth client details");
                return false;
            }
        } else {
            // return error response
            if (isset($noResponse) && $noResponse === true) {
                return false;
            } else {
                $server->getResponse()->send();
            }
        }
    }
    
    /**
     * Retrieve client details by client id
     *
     * @return      the client details: client_id, client_name, user_type
     * @since       Version 1.0.00
     * @see
     * @author      Sandy Wong
     */
    public function getClientDetails($client_id) {
        // get storage
        $storage = $this->m_storage;
    
        $client_detail = $storage->getClientDetails($client_id);
        if ((bool)$client_detail == false) {
            Log::logSystemError('oauth', "getClientDetails: cannot find OAuth client details");
            
            return false;
        }
        
        
        if($client_detail['status'] != OauthClient::STATUS_ENABLE){
            Log::logSystemError('oauth', "getClientDetails: the client has been disabled");
            return false;
        }
    
        // unset unuse data
        unset($client_detail['client_secret']);
        unset($client_detail['redirect_uri']);
        unset($client_detail['public_key']);
        unset($client_detail['private_key']);
    
        for ($i=0; $i<=7; $i++) {
            unset($client_detail[$i]);
        }
    
        return (object)$client_detail;
    }
    
    /**
     * Send authrize code to client after user authorization
     *
     * @since       Version 1.0.00
     * @param       $is_authorized          Is user authorized client
     * @param       $user_id                The ID of user that doing authorization
     * @see
     * @author      Sandy Wong
     */
    public function sendAuthorizeCode($is_authorized, $user_id) {
        // get server
        $server = $this->m_server;
    
        // handle request
        $request = \OAuth2\Request::createFromGlobals();
        $response = new \OAuth2\Response();
        
        $server->handleAuthorizeRequest($request, $response, $is_authorized, $user_id);
        
        if($response->isSuccessful() || $response->isRedirection()){
            Log::logOauth('send authorization code', $user_id);
        } else {
            Log::logOauth('send authorization code fail' . json_encode($response->getParameters()));
        }
        // redirect to client uri
        $response->send();
        
        
    }
    /**
     * Verify authorization code, and send out access token
     *
     * @since       Version 1.0.00
     * @see
     * @author      Sandy Wong
     */
    public function sendAccessToken() {
        // get server
        $server = $this->m_server;
    
        // handle request
        $request = \OAuth2\Request::createFromGlobals();
        $response = new \OAuth2\Response();
        
        
        $response = $this->m_server->handleTokenRequest($request, $response);
        
        if($response->isSuccessful() || $response->isRedirection()){
            Log::logOauth('send access token');
        } else {
            Log::logOauth('send access token fail' . json_encode($response->getParameters()));
        }
        // send back to client
        $response->send();
        
        
        
    }
}

/* ===============================================================
   End of LasOauthServer.php
   =============================================================== */
?>