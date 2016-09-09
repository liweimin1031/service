<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : test_oauth_api.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Aug 26, 2015
 * LASTUPDATES : $Author: csdhong $ on $Date: 3:31:43 PM Aug 26, 2015 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) test_oauth_api.php              1.0 Aug 26, 2015
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
   Begin of test_oauth_api.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
//require_once(dirname(__DIR__).DIRECTORY_SEPARATOR. 'inc.php');

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */

//$user = User::createUser('operator', 'lmsDEV-0', '陈大文', 'Chan Ta Man');

//var_dump($user);
$token = test_oauth_client_credentials();
//$token= '585098931b4aa2f2db6a45b945491363012bf37d';
if($token){
    test_client_credectial_access($token);
}

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */

function test_oauth_client_credentials(){
    
    $oauthSettings= new stdClass();
    $ch = curl_init();
    
    $oauthSettings->url = 'https://demo.elana.hklms.org/oauth.php';
    $oauthSettings->client_id = '19301769210302d5f082.las_api';
    $oauthSettings->client_secret = 'fdd43ec220c02cfb50d370530657b2dc59ce5631';
    /*
     $oauthServerUrl = 'https://cls.hkteducation.com/clms/api/oauth.php';
     $oauthSettings->client_id = 'b3afcceddb9289b91cfd.test.api';
     $oauthSettings->client_secret = '825d2c481fbf0a53504916ba3bee4626c6e089e2';
     */
    $url = $oauthSettings->url."/access";
    $para = new \stdClass();
    $para->grant_type = 'client_credentials';
    $para->client_id = $oauthSettings->client_id;
    $para->client_secret = $oauthSettings->client_secret;
    $para->scope = '';
    //$para->redirect_uri = $lemoUrl;
    //$para->code = $code;
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
        
        $result = $accessInfo->access_token;
    } else {
        
        echo 'Get access token fail '.$ee['http_code'].PHP_EOL;
        echo $body . PHP_EOL;
    }
    
    curl_close($ch);
    return $result;
}

function test_client_credectial_access($token){
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
       
        echo $body. PHP_EOL;
        
    } else {
        
        echo 'Fail'. PHP_EOL;
        echo $body . PHP_EOL;
    }
    
    curl_close($ch);
}





/* ===============================================================
   End of test_oauth_api.php
   =============================================================== */
?>