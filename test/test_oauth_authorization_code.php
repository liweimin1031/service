<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : test_oauth_authorization_code.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Aug 31, 2015
 * LASTUPDATES : $Author: csdhong $ on $Date: 9:46:48 AM Aug 31, 2015 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) test_oauth_authorization_code.php              1.0 Aug 31, 2015
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
   Begin of test_oauth_authorization_code.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */



/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */
$oauthSettings= new stdClass();

$oauthSettings->url = 'https://demo.elana.hklms.org/oauth.php';
$oauthSettings->client_id = 'e48554d7cb358a8a65b9.las_user';
$oauthSettings->client_secret = '4e229b68394027f03bdc0a907cf36b359dfe4008';
$oauthSettings->redirect_uri = 'https://demo.elana.hklms.org/test/test_oauth_authorization_code.php';
$oauthSettings->state = 'xyz';


/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */


if(isset($_REQUEST['error'])){
    exit;
}
$code = isset($_REQUEST['code'])?$_REQUEST['code']:null;
$state = isset($_REQUEST['state'])?$_REQUEST['state']:null;
if(empty($code)){
    $url = test_oauth_authorization_code_url();
    
    header('Location:'. $url);
}else {
     if(!empty($state) && $state == $oauthSettings->state){
       
         $result = test_oauth_client_get_access_code($code);
         
         if($result){
             test_oauth_client_access_token($result->access_token);
             test_oauth_client_get_refersh_token($result->refresh_token);
              
         }
         
         
         
     } else {
         echo 'error for state';
     }
 }

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */

function test_oauth_authorization_code_url(){

    global $oauthSettings;
    
    
    $url = $oauthSettings->url."/authorize";
    
    $para = new \stdClass();
    $para->client_id = $oauthSettings->client_id;
    $para->scope = '';
    $para->redirect_uri = $oauthSettings->redirect_uri;
    $para->state = $oauthSettings->state;
    $para->response_type = 'code';
    
    
    $para = http_build_query($para);
    
    return $url.'?'.$para;
}

function test_oauth_client_get_access_code($code){
    global $oauthSettings;
    
    
    $url = $oauthSettings->url."/access";
    
    $para = new \stdClass();
    $para->client_id = $oauthSettings->client_id;
    $para->scope = '';
    $para->redirect_uri = $oauthSettings->redirect_uri;
    $para->client_secret = $oauthSettings->client_secret;
    $para->state = $oauthSettings->state;
    $para->grant_type = 'authorization_code';
    $para->code = $code;
    
    
    $para = http_build_query($para);
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $para);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $info = curl_exec($ch);
    $ee = curl_getinfo($ch);
    list($header, $body) = explode("\r\n\r\n", $info, 2);
    $result = false;
    if (!curl_errno($ch) && $ee['http_code']== 200) {
        $accessInfo = json_decode($body);
        $accessInfo->expires = time() + $accessInfo->expires_in;
        $accessInfo->token_type = ucwords($accessInfo->token_type);
       
        unset($accessInfo->expires_in);
        if ($accessInfo) {
            $accessCode = $accessInfo;
        }
        echo "Get access token ". $accessInfo->access_token .PHP_EOL;
        echo "Get access token type ". $accessInfo->token_type .PHP_EOL;
        
        $result = $accessInfo;
    } else {
        
        echo 'Get access token fail '.$ee['http_code'].PHP_EOL;
        echo $body . PHP_EOL;
    }
    
    curl_close($ch);
    return $result;
}

function test_oauth_client_get_refersh_token($refresh_token){
    global $oauthSettings;
    
    
    $url = $oauthSettings->url."/access";
    
    $para = new \stdClass();
    $para->client_id = $oauthSettings->client_id;
    $para->client_secret = $oauthSettings->client_secret;
    $para->grant_type = 'refresh_token';
    $para->refresh_token = $refresh_token;
    
    
    $para = http_build_query($para);
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $para);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $info = curl_exec($ch);
    $ee = curl_getinfo($ch);
    list($header, $body) = explode("\r\n\r\n", $info, 2);
    $result = false;
    if (!curl_errno($ch) && $ee['http_code']== 200) {
        $accessInfo = json_decode($body);
        $accessInfo->expires = time() + $accessInfo->expires_in;
        $accessInfo->token_type = ucwords($accessInfo->token_type);
        unset($accessInfo->expires_in);
        if ($accessInfo) {
            $accessCode = $accessInfo;
        }
        echo "Get refresh token ". $accessInfo->access_token .PHP_EOL;
        echo "Get refresh token type ". $accessInfo->token_type .PHP_EOL;
    
        $result = $accessInfo->access_token;
    } else {
    
        echo 'Get refresh token fail '.$ee['http_code'].PHP_EOL;
        echo $body . PHP_EOL;
    }
    
    curl_close($ch);
    return $result;
}



function test_oauth_client_access_token($token){

    
    $url= 'https://demo.elana.hklms.org/test/test_token.php';
    
    $ch = curl_init();
    
    $headers = array('Authorization: Bearer ' . $token, 'Content-Type: application/json');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    $info = curl_exec($ch);
    $ee = curl_getinfo($ch);
    list($header, $body) = explode("\r\n\r\n", $info, 2);
    if (!curl_errno($ch) && $ee['http_code']== 200) {
        
        echo $body.PHP_EOL;
    } else {
    
        echo 'Fail'. PHP_EOL;
        echo $body . PHP_EOL;
    }
    
    curl_close($ch);
}

/* ===============================================================
   End of test_oauth_authorization_code.php
   =============================================================== */
?>