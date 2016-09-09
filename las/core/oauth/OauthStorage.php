<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : OauthStorage.php
 * AUTHOR      : Sandy Wong
 * SYNOPSIS    :
 * DESCRIPTION : provide PDO functions for OAuth Library
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision$)
 * CREATED     : 23-JUL-2013
 * LASTUPDATES : $Author$ on $Date$
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) OauthStorage.php                1.0 23-JUL-2013
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
  Begin of OauthStorage.php
  =============================================================== */
namespace Las\Core\Oauth;

/* ---------------------------------------------------------------
  Included Library
  --------------------------------------------------------------- */
use Las\Core\Oauth\OauthClient;
use Las\Core\Oauth\OauthAccessToken;
use Las\Core\Oauth\OauthAuthorizationCode;
use Las\Core\Oauth\OauthRefreshToken;
use Las\Core\Util\Log;



/* ---------------------------------------------------------------
  Global Variables
  --------------------------------------------------------------- */


/* ---------------------------------------------------------------
  Constant definition
  --------------------------------------------------------------- */

/* ---------------------------------------------------------------
  Function definition
  --------------------------------------------------------------- */
// register oauth library

class OauthStorage implements \OAuth2\Storage\AuthorizationCodeInterface,
\OAuth2\Storage\AccessTokenInterface, \OAuth2\Storage\ClientCredentialsInterface,
\OAuth2\Storage\RefreshTokenInterface
{
    const PASSPHRASE = "c9ukAswespasegethufr";
    
    const CLIENT_ID_LENGTH = 20;
    const CLIENT_SECRET_LENGTH = 40;
    
    public function __construct()
    {
        
    }
    
    public function setClientDetails($client_id, $data)
    {
        if ($client = $this->getClientDetailsAsObj($client_id)) {
            
            OauthClient::updateClient($client_id, $data);
            return true;
        } else {
            throw new OauthException(OauthException::LAS_ERROR_OAUTH_EINVAL);
        }
    }
    
    
    public function addClientDetails( $client_name, $redirect_uri = null, $client_type =null, $description= null,  $generateKeyPair = false){
        // generate client id, and check if it existed in database already
        $client_id = str_replace(" ", ".", $client_name);
        $client_id = self::generateRandomString(self::CLIENT_ID_LENGTH) . "." . $client_id;
        $unique = false;
        
        while ($unique == false) {
            // check if client existed
            $client_detail = $this->getClientDetailsAsObj($client_id);
            if ((bool)$client_detail == false) {
                $unique = true;
            } else {
                $client_id = self::generateRandomString(self::CLIENT_ID_LENGTH) . "." . $client_name;
            }
        }
        // generate client secret
        $client_secret = self::generateRandomString(self::CLIENT_SECRET_LENGTH);
        
        
        // generate RSA key pair if client lemo
        $public_key = "";
        $private_key = "";
        if ($generateKeyPair) {
            $result = self::generateKeyPair();
            if ((bool)$result == false) {
                throw new OauthException(OauthException::LAS_ERROR_OAUTH_EKEYPAIR);
            }
        
        
            $public_key = $result["public_key"];
            $private_key = $result["private_key"];
        }
        
        return OauthClient::createClient($client_id, $client_secret, $redirect_uri, $client_type, $client_name, $public_key, $private_key, $description);
    }
    
    public function setTemporyClientDetails($client_id, $data){
        $data = array('tmp'=>$data);
        return OauthClient::updateClient($client_id, $data);
    }
    
    public function removeClientDetails($client_id){
        return OauthClient::deleteClient($client_id);
    }
    
    
    public function listClientDetails(){
        return OauthClient::getAll();
    }
    
    
    
    
    /* ClientCredentialsInterface */
    public function checkClientCredentials($client_id, $client_secret = null)
    {
        $client = OauthClient::getClient($client_id);
        if ($client) {
            if (($client->status == OauthClient::STATUS_ENABLE) && ($client->client_secret == $client_secret)) {
                return true;
            }
        }
        return false;
    }
    
    public function getClientDetailsAsObj($client_id)
    {
        $client = OauthClient::getClient($client_id);
        if ($client) {
            return $client;
        }
        return false;
    }
    
    public function getClientDetails($client_id)
    {
        $client = OauthClient::getClient($client_id);
        if ($client) {
            
            if ($client->status == OauthClient::STATUS_ENABLE) {
                 return (array) $client; 
            } else {
            return false;
            }
        }
        return false;
    }
    
    
    
    public function checkRestrictedGrantType($client_id, $grant_type)
    {
        $client = OauthClient::getClient($client_id);
        if (isset($details->grant_type)) {
            // if have grant_types, use string "type 1:type 2:type 3"
            $grant_types = explode(":", $details->grant_type);
            return in_array($grant_type, $grant_types);
        }
        
        // if grant_types are not defined, then none are restricted
        return true;
    }
    
    /* AccessTokenInterface */
    public function getAccessToken($access_token)
    {
        $accessToken = OauthAccessToken::getAccessToken($access_token);
        if ($accessToken) {
            return (array)$accessToken;
        }
        return false;
    }
    
    public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null)
    {
        $accessToken = OauthAccessToken::getAccessToken($access_token);
        if (!$accessToken) {
            $accessToken = new OauthAccessToken;
            $accessToken->access_token = $access_token;
        }
        $accessToken->client_id = $client_id;
        $accessToken->user_id = $user_id;
        $accessToken->expires = $expires;
        $accessToken->scope = $scope;
        try {
            $accessToken->save();
            return true;
        } catch (DbException $e) {
            return(false);
        }
    }
    
    /* AuthorizationCodeInterface */
    public function getAuthorizationCode($code)
    {
        $authCode = OauthAuthorizationCode::getAuthorizationCode($code);
        if ($authCode) {
            return (array)$authCode;
        }
        return false;
    }
    
    public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null)
    {
        $authCode =  OauthAuthorizationCode::getAuthorizationCode($code);
        if (!$authCode) {
            $authCode = new OauthAuthorizationCode;
            $authCode->authorization_code = $code;
        }
        $authCode->client_id = $client_id;
        $authCode->user_id = $user_id;
        $authCode->redirect_uri = $redirect_uri;
        $authCode->expires = $expires;
        $authCode->scope = $scope;
        try {
            $authCode->save();
            return true;
        } catch (DbException $e) {
            return false;
        }
    }
    
    public function expireAuthorizationCode($code)
    {
        /*($stmt = $this->db->prepare(sprintf('DELETE FROM %s WHERE authorization_code = :code', $this->config['code_table']));
    
        return $stmt->execute(compact('code'));*/
        return  OauthAuthorizationCode::deleteAuthorizationCode($code);
    }
    
    /* RefreshTokenInterface */
    public function getRefreshToken($refresh_token)
    {
        $refreshToken = OauthRefreshToken::getRefreshToken($refresh_token);
        if ($refreshToken) {
            return (array)$refreshToken;
        } else {
            return false;
        }
    }
    
    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        // convert expires to datestring
        /*$expires = date('Y-m-d H:i:s', $expires);
    
        $stmt = $this->db->prepare(sprintf('INSERT INTO %s (refresh_token, client_id, user_id, expires, scope) VALUES (:refresh_token, :client_id, :user_id, :expires, :scope)', $this->config['refresh_token_table']));
    
        return $stmt->execute(compact('refresh_token', 'client_id', 'user_id', 'expires', 'scope'));*/
        
        $refreshToken = new OauthRefreshToken;
        $refreshToken->refresh_token = $refresh_token;
        $refreshToken->client_id = $client_id;
        $refreshToken->user_id = $user_id;
        $refreshToken->expires = $expires;
        $refreshToken->scope = $scope;
        try {
            $refreshToken->save();
            return true;
        } catch (DbException $e) {
            return false;
        }
    }
    
    public function unsetRefreshToken($refresh_token)
    {
        /*$stmt = $this->db->prepare(sprintf('DELETE FROM %s WHERE refresh_token = :refresh_token', $this->config['refresh_token_table']));
    
        return $stmt->execute(compact('refresh_token'));*/
        return  OauthRefreshToken::deleteRefreshToken($refresh_token);
    }

    public function isPublicClient($client_id){
        //To Do
    }
    
    public function getClientScope($client_id){
        //To Do
    }
        
    /**
     * Generate random string with specified length
     *
     * @param    length: length of random string
     * @return      the random strong
     * @since       Version 1.0.00
     * @see
     * @author      Sandy Wong
     */
    public static function generateRandomString($length= self::CLIENT_SECRET_LENGTH)
    {
        if (file_exists('/dev/urandom')) { // Get 100 bytes of random data
            $randomData = file_get_contents('/dev/urandom', false, null, 0, 100) . uniqid(mt_rand(), true);
        } else {
            $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
        }
        return substr(hash('sha512', $randomData), 0, $length);
    }
    
    
    /**
     * Generate RSA key pair
     *
     * @return      the key pair: publicKey, privateKey
     * @since       Version 1.0.00
     * @see
     * @author      Sandy Wong
     */
    public static function generateKeyPair() {
        $config = array(
                "private_key_bits" => 1024,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
                "config" => dirname(__FILE__) . "/openssl.cnf"
        );
    
        $result = openssl_pkey_new($config);
        if ((bool)$result == false) {
            // log error
            while (($e = openssl_error_string()) !== false) {
                //OauthUtil::logError($e);
                Log::logSystemError('oauth', $e);
            }
            return false;
        }
    
        // get key pair
        $pubkey = openssl_pkey_get_details($result);
        $keys["public_key"] = $pubkey['key'];;
        openssl_pkey_export($result, $privkey, self::PASSPHRASE, $config);
        $keys["private_key"] = $privkey;
        return $keys;
    }
}


/* ===============================================================
  End of OauthStorage.php
  =============================================================== */
?>
