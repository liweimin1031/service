<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : ajax.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Oct 12, 2015
 * LASTUPDATES : $Author: csdhong $ on $Date: 11:16:33 AM Oct 12, 2015 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) ajax.php              1.0 Oct 12, 2015
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
   Begin of ajax.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */



require_once dirname(__FILE__) . DIRECTORY_SEPARATOR.'lib'  . DIRECTORY_SEPARATOR.'bootstrap.php';

use Astri\Lib\Util\ClmsDOMDocument;
use Astri\Lib\Util\PhpUri;

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */
$version = 20151110;
if (version_compare(phpversion(), '5.4.0', '<')) {
     if(session_id() == '') {
        session_start();
     }
 }
 else
 {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
 }
$msg = new \stdClass();
$msg->success = false;
$msg->error = new \stdClass();

$oauthSettings= new stdClass();
//$oauthSettings->url = 'http://las-kary.hklms.org/';
$oauthSettings->url = 'http://kary2.hklms.org/';
$oauthSettings->client_id = '132b66f38af7386e8df0.las_dev';
$oauthSettings->client_secret = '1e3fd8c7c8b13afac6e16b67583c3095375fbb67';


$oauthSettings->url = 'https://trial.elana.org/';
$oauthSettings->client_id = 'e038e915f5ebcbf1593b.modern.trial';
$oauthSettings->client_secret = '2eb53206a50ceae549caa44b9de990862e36e4ad';

/*
$oauthSettings->url = 'https://elana.org/';
$oauthSettings->client_id = '0f4d9cdf988a8bc4e816.las_api_elana_org';
$oauthSettings->client_secret = '9f78f73a5c6a07d1af31fbacd701285c887111db';
 */

if(isset($_SESSION) && isset($_SESSION['version']) && $_SESSION['version'] == $version){
    
}else {
    session_unset();
}

if(isset($_SESSION) && isset($_SESSION['token_info'])){
    if ($_SESSION['token_info']->expires_time > time()){
        $oauthSettings->token_info->access_token =  $_SESSION["token_info"]->access_token;
        $oauthSettings->token_info->expires_time =$_SESSION["token_info"]->expires_time;
       
    } else {
        session_unset();
    }
}


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */
if ( isset($_SERVER['PATH_INFO']) ) {
    switch ($_SERVER['PATH_INFO']) {
        case '/getOpenRiceList':
           //$result ='{"success":true,"error":{},"data":[{"title":"\u6703\u62401\u865f\u00a0\u6dfa\u6c34\u7063\u00a0 ClubONE\u00a0Repulse\u00a0Bay\u00a0","href":"http:\/\/www.openrice.com\/zh\/hongkong\/restaurant\/%E6%B7%BA%E6%B0%B4%E7%81%A3-%E6%9C%83%E6%89%801%E8%99%9F-%E6%B7%BA%E6%B0%B4%E7%81%A3\/174000?tc=sr1","image":"http:\/\/static4.orstatic.com\/userphoto\/photo\/7\/64W\/017MRN8740BF305CA067FAn.jpg","location":"\u6dfa\u6c34\u7063","location_detail":"\r\n\u6dfa\u6c34\u7063\u6d77\u7058\u905316\u865f                    "},{"title":"\u6703\u62401\u865f\u00a0\u535a\u85dd\u6703\u00a0 ClubONE\u00a0Spotlight\u00a0","href":"http:\/\/www.openrice.com\/zh\/hongkong\/restaurant\/%E7%B4%85%E7%A3%A1-%E6%9C%83%E6%89%801%E8%99%9F-%E5%8D%9A%E8%97%9D%E6%9C%83\/18075?tc=sr1","image":"http:\/\/static4.orstatic.com\/userphoto\/photo\/0\/MQ\/004HNAABBC2315E2F840A2n.jpg","location":"\u7d05\u78e1","location_detail":"\r\n\u7d05\u78e1\u9ec3\u57d4\u82b1\u5712\u7b2c8\u671f\u87a2\u5e55\u57084\u6a13                    "},{"title":"\u6703\u62401\u865f\u00a0\u5fb7\u85dd\u6703\u00a0 ClubONE\u00a0Telford\u00a0","href":"http:\/\/www.openrice.com\/zh\/hongkong\/restaurant\/%E4%B9%9D%E9%BE%8D%E7%81%A3-%E6%9C%83%E6%89%801%E8%99%9F-%E5%BE%B7%E8%97%9D%E6%9C%83\/17541?tc=sr1","image":"http:\/\/static1.orstatic.com\/userphoto\/photo\/2\/2CX\/00GRWT48DA11A5941E0F84n.jpg","location":"\u4e5d\u9f8d\u7063","location_detail":"\r\n\u4e5d\u9f8d\u7063\u5fb7\u798f\u82b1\u5712P13-14\u865f                    "},{"title":"\u6703\u62401\u865f\u00a0\u842c\u6fe4\u00a0 ClubONE\u00a0River\u00a0View\u00a0","href":"http:\/\/www.openrice.com\/zh\/hongkong\/restaurant\/%E6%B2%99%E7%94%B0-%E6%9C%83%E6%89%801%E8%99%9F-%E8%90%AC%E6%BF%A4\/149438?tc=sr1","image":"http:\/\/static4.orstatic.com\/userphoto\/photo\/A\/85W\/01M1YGFB584AABE7067534n.jpg","location":"\u6c99\u7530","location_detail":"\r\n\u6c99\u7530\u5b89\u5e73\u88571\u865f\u9999\u6e2f\u6c99\u7530\u842c\u6021\u9152\u5e97\u5546\u58341\u6a13                    "},{"title":"\u6703\u62401\u865f\u00a0\u4e5d\u9f8d\u6771\u00a0 ClubONE\u00a0Kowloon\u00a0East\u00a0","href":"http:\/\/www.openrice.com\/zh\/hongkong\/restaurant\/%E8%A7%80%E5%A1%98-%E6%9C%83%E6%89%801%E8%99%9F-%E4%B9%9D%E9%BE%8D%E6%9D%B1\/48344?tc=sr1","image":"http:\/\/static4.orstatic.com\/userphoto\/photo\/4\/3MQ\/00PTNZA976B7FC36602ED7n.jpg","location":"\u89c0\u5858","location_detail":"\r\n\u89c0\u5858\u89c0\u5858\u9053410\u865f\u89c0\u9ede\u4e2d\u5fc32\u6a13                    "},{"title":"\u6703\u62401\u865f\u00a0\u5927\u821e\u81fa\u00a0 ClubONE\u00a0The\u00a0Grand\u00a0Stage\u00a0","href":"http:\/\/www.openrice.com\/zh\/hongkong\/restaurant\/%E4%B8%8A%E7%92%B0-%E6%9C%83%E6%89%801%E8%99%9F-%E5%A4%A7%E8%88%9E%E8%87%BA\/10399?tc=sr1","image":"http:\/\/static2.orstatic.com\/userphoto\/photo\/1\/14X\/0082Z582B6606BF13E11C6n.jpg","location":"\u4e0a\u74b0","location_detail":"\r\n\u4e0a\u74b0\u5fb7\u8f14\u9053\u4e2d323\u865f\u897f\u6e2f\u57ce2\u6a13                    "},{"title":"\u6703\u62401\u865f\u00a0\u79d1\u5b78\u5712\u00a0 ClubONE\u00a0on\u00a0the\u00a0PARK\u00a0","href":"http:\/\/www.openrice.com\/zh\/hongkong\/restaurant\/%E5%A4%A7%E5%9F%94-%E6%9C%83%E6%89%801%E8%99%9F-%E7%A7%91%E5%AD%B8%E5%9C%92\/455864?tc=sr1","image":"http:\/\/www.openrice.com\/images\/v4\/previewimg\/NoAvatar_restaurant.png","location":"\u5927\u57d4","location_detail":"\r\n\u5927\u57d4\u79d1\u5b78\u5712\u79d1\u6280\u5927\u9053\u897f12\u865f12W\u5927\u6a13\u5730\u4e0bS061-S066\u8216                    "},{"title":"\u6703\u62401\u865f\u00a0\u90f5\u8f2a\u5824\u5cb8\u00a0 ClubONE\u00a0Harbour\u00a0Front\u00a0","href":"http:\/\/www.openrice.com\/zh\/hongkong\/restaurant\/%E4%B9%9D%E9%BE%8D%E7%81%A3-%E6%9C%83%E6%89%801%E8%99%9F-%E9%83%B5%E8%BC%AA%E5%A0%A4%E5%B2%B8\/149445?tc=sr1","image":"http:\/\/static1.orstatic.com\/userphoto\/photo\/A\/85W\/01M1YE0F6A5581093557A7n.jpg","location":"\u4e5d\u9f8d\u7063","location_detail":"\r\n\u4e5d\u9f8d\u7063\u81e8\u6fa4\u88578\u865f\u50b2\u9a30\u5ee3\u5834\u5730\u4e0b                    "},{"title":"\u6703\u62401\u865f\u00a0\u4e5d\u9f8d\u534a\u5c71\u00a0 ClubONE\u00a0Kowloon\u00a0Peak\u00a0","href":"http:\/\/www.openrice.com\/zh\/hongkong\/restaurant\/%E4%BD%95%E6%96%87%E7%94%B0-%E6%9C%83%E6%89%801%E8%99%9F-%E4%B9%9D%E9%BE%8D%E5%8D%8A%E5%B1%B1\/173986?tc=sr1","image":"http:\/\/static2.orstatic.com\/userphoto\/photo\/A\/7YO\/01KMJIAA9BC432ABD57FD1n.jpg","location":"\u4f55\u6587\u7530","location_detail":"\r\n\u4f55\u6587\u7530\u8fb2\u5703\u905318\u865f1\u6a13\u5168\u5c64                    "},{"title":"\u6703\u62401\u865f\u00a0\u9280\u7058\u00a0 ClubONE\u00a0Water\u00a0Front\u00a0","href":"http:\/\/www.openrice.com\/zh\/hongkong\/restaurant\/%E9%A6%AC%E7%81%A3-%E6%9C%83%E6%89%801%E8%99%9F-%E9%8A%80%E7%81%98\/173993?tc=sr1","image":"http:\/\/static4.orstatic.com\/userphoto\/photo\/A\/85W\/01M1YH959F32C15D809D12n.jpg","location":"\u99ac\u7063","location_detail":"\r\n\u99ac\u7063\u73c0\u9e97\u7063\u81e8\u6d77\u5ee3\u5834L2                    "}]}';
           //$result ='{"success":true,"error":{},"data":[{"title":"\u5927\u908a\u7210\u706b\u934b\u5e97\u00a0 da\u00a0BINO\u00a0","href":"http:\/\/www.openrice.com\/zh\/hongkong\/restaurant\/%E4%B9%9D%E9%BE%8D%E5%9F%8E-%E5%A4%A7%E9%82%8A%E7%88%90%E7%81%AB%E9%8D%8B%E5%BA%97\/12958?tc=sr1"}]}';
            
           //echo $result;
           //exit;
           $para = $_REQUEST['openrice_search'];
           if(!empty($para)){
               $result = getOpenRiceSuggestion($para);
               if(!empty($result)){
                   $msg->success= true;
                   $msg->data = $result;
               }
           }
           break;
        case '/getTaskResult':
            $para = $_REQUEST['taskid'];
            $token = getOauthClientCredentials();
            if(!empty($para) && !empty($token)){
                $result = getTaskResult($para, $token);
                
                if(!empty($result)){
                   $msg->success = true;
                   $msg = $result;
                }else{
                    $msg->error->reason="Analysis response error";
                } 
            } else if (empty($token)){
                $msg->error->reason="Invalid access right for analysis server";
            } else {
                $msg->error->reason="Invalid parameters";
            }
            break;
        case '/submitTextAnalysisJob':
            $para = $_REQUEST['data'];
            $token = getOauthClientCredentials();
            if($token && $para){
                $api = 'api/text/TextAnalysis';
                $post_fields = new \stdClass();
                $post_fields->data = new \stdClass();
                $post_fields->data->data = $para;
                $result = submitAnalysisJob($post_fields, $token, $api, '');
                if(!empty($result) && $result!=='null'){
                    $msg->success= true;
                    $msg->data = $result;
                } else{
                    $msg->error->reason="Analysis response error";
                } 
            } else if (empty($token)){
                $msg->error->reason="Invalid access right for analysis server";
            } else {
                $msg->error->reason="Invalid parameters";
            }
            break;
        case '/submitSentimentAnalysisJob':
            $para = $_REQUEST['data'];
            $token = getOauthClientCredentials();
            if($token && $para){
                $api = 'api/text/SentimentAnalysis';
                $post_fields = new \stdClass();
                $post_fields->data = new \stdClass();
                $post_fields->data->data = $para;
                $result = submitAnalysisJob($post_fields, $token, $api, '');
                if(!empty($result) && $result!=='null'){
                    $msg->success= true;
                    $msg->data = $result;
                } else{
                    $msg->error->reason="Analysis response error";
                }
            } else if (empty($token)){
                $msg->error->reason="Invalid access right for analysis server";
            } else {
                $msg->error->reason="Invalid parameters";
            }
            break;
        case '/submitOpenriceAnalysisJob':
            $para = $_REQUEST['url'];
            $token = getOauthClientCredentials();
            
            if($token && $para){
                $post_fields = new \stdClass();
                $post_fields->data = new \stdClass();
                $post_fields->data->url = $para;
                $post_fields->data->data = 0;
                $api = 'api/text/OpenRiceAnalysis';
                $result = submitAnalysisJob($post_fields, $token, $api, $para);
                
                if(!empty($result) && $result!=='null'){
                    $msg->success= true;
                    $msg->data = $result;
                } else{
                    $msg->error->reason="Analysis response error";
                } 
            } else if (empty($token)){
                $msg->error->reason="Invalid access right for analysis server";
            } else {
                $msg->error->reason="Invalid parameters";
            }
            break;
        case '/getRestaurantInfo':
            $para = $_REQUEST['url'];
            if(!empty($para)){
                $result = getRestaurantInfo($para);
                if(!empty($result)){
                    $msg->success= true;
                    $msg->data = $result;
                }
            } else {
                $msg->error->reason="Invalid parameters";
            }
            break;
        default:
            
    }
}
if($msg->success ==false && get_object_vars($msg->error) == false){
    $msg->error->reason = 'Unknown server error';
}
echo json_encode($msg);
exit;

function getOauthClientCredentials(){
    global $oauthSettings, $version;
    $ch = curl_init();
    if (isset($oauthSettings->token_info) && $oauthSettings ->token_info->expires_time > (time()-60)){
         return $oauthSettings ->token_info->access_token;
    }
    error_log('[DL][Oauth][Token Request]:Start at '.time());
    $url = $oauthSettings->url."oauth.php/access";
    $para = new \stdClass();
    $para->grant_type = 'client_credentials';
    $para->client_id = $oauthSettings->client_id;
    $para->client_secret = $oauthSettings->client_secret;
    $para->scope = '';
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
    $result = false;
    if (!curl_errno($ch) && $ee['http_code']== 200) {
        list($header, $body) = explode("\r\n\r\n", $info, 2);
        
        $accessInfo = json_decode($body);
        $_SESSION["token_info"]->access_token =  $accessInfo->access_token;
        $_SESSION["token_info"]->expires_time = time()+ intval($accessInfo->expires_in);
        $_SESSION["version"] = $version;
        $result = $accessInfo->access_token;
        error_log('[DL][Oauth][Token Request]:OK');
    } else {
      //doing nothing
      //error_log(print_r($info,2)); 
      //error_log(print_r($ee,2));
      list($header, $body) = explode("\r\n\r\n", $info, 2);
      error_log('[DL][Oauth][Token Request]:'.$body); 
    }
    curl_close($ch);
    return $result;
}

function submitAnalysisJob($post_fields, $token, $api, $log){
    global $oauthSettings;
    $ch = curl_init();
    error_log('[DL][Oauth][Job Submit][Token: '.$token.']:'.$log. ' Start' ); 
    $url = $oauthSettings->url. $api;
    $headers = array('Authorization: Bearer ' . $token, 'Content-Type: application/x-www-form-urlencoded');
    $post_fields = http_build_query($post_fields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    $info = curl_exec($ch);
    $ee = curl_getinfo($ch);
    $result = false;
    if (!curl_errno($ch) && $ee['http_code']== 200) {
        $job = json_decode($info);
        if($job->success){
           $result = $job->data;
           error_log('[DL][Oauth][Job Submit][Token: '.$token.']:Success' );
        } else {
           error_log('[DL][Oauth][Job Submit][Token: '.$token.']:Analysis Server response error' );
        }
         
    } else {
        error_log(print_R($ee,2)); 
        error_log('[DL][Oauth][Job Request][Token: '.$token.']:Network Error' );
    }
    
    curl_close($ch);
    return $result;
}

function getRestaurantInfo($url){
    $page = getHtmlPage ( $url );
    $xpath = new \DOMXpath ( $page );
    $title = '//div[@class="bigger-font-name"]//span';
    $subtitle = '//div[@class="smaller-font-name"]';
    $image = '//div[@class="door-photo-section"]//div[@class="photo"]';
    $location = '//div[@class="header-poi-district dot-separator"]';
    $location_detail = '//div[@class="address-info-section"]//div[@class="content"]//a';
    
    $temp = new \stdClass ();
    $temp_entries = $xpath->query ( $title );
    foreach ( $temp_entries as $entry ) {
        $temp->title = trim($entry->textContent);
        $temp->href = $url;
        break;
    }
    $temp_entries = $xpath->query ( $subtitle );
    foreach ( $temp_entries as $entry ) {
        $temp->title = $temp->title .' '. trim($entry->textContent);
        break;
    }
    $temp->title = str_replace("\r\n",'',  $temp->title);
    $temp_entries = $xpath->query ( $image );
    foreach ( $temp_entries as $entry ) {
        $imageurl = $entry->getAttribute ( 'style' );
        preg_match_all ( '#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', $imageurl, $image_array );
        if (! empty ( $image_array ) && ! empty ( $image_array [0] )) {
            $temp_imageArray = $image_array [0];
            $temp->image = PhpUri::rel2abs ( $temp_imageArray [0], $url );
        }
        if(empty($temp->image) && $entry->getAttribute('src')){
            if($entry->getAttribute('src') == '//static3.orstatic.com/images/v4/hk/sr2/NoAvatar_restaurant_SR2.png'){
                $temp->image = 'http://www.openrice.com/images/v4/previewimg/NoAvatar_restaurant.png';
            }else {
                $temp->image = PhpUri::rel2abs ( $entry->getAttribute('src'), $url );
            }
            
        }
        break;
    }
    $temp_entries = $xpath->query ( $location );
    foreach ( $temp_entries as $entry ) {
        $temp->location = trim(str_replace("\r\n",'',  $entry->textContent));
        
        break;
    }
    $temp_entries = $xpath->query ( $location_detail );
    foreach ( $temp_entries as $entry ) {
        $temp->location_detail = trim(str_replace("\r\n",'',  $entry->textContent));
    
        break;
    }
    
    return $temp;      
}
function getOpenRiceSuggestion($keyword) {
    $prefix = 'http://www.openrice.com/api/pois?uiLang=zh&uiCity=hongkong&page=1&&sortBy=Default&';
    $exploded = preg_split('@ @', $keyword, NULL, PREG_SPLIT_NO_EMPTY);
    $url = $prefix.'what='.implode('+',$exploded);
   
    $data = getJsonData ($url);
    $results= array();
    if ($data){
        error_log(print_r($data,2));
        $search_results = $data->searchResult->paginationResult->results;
        
        foreach($search_results as $element){
            $temp = new \stdClass();
            $temp->title = $element->nameUI;
            $temp->href = PhpUri::rel2abs($element->urlUI,$url);
            $temp->image= PhpUri::rel2abs($element->doorPhoto->url,$url);
            $temp->location = $element->district->name;
            $temp->location_detail = $element->addressUI->plainAddress;
            $temp->reviews= $element->reviewUrlUI;
            $results[]= $temp;
        }
    }
    return $results;
}

function getTaskResult($taskid, $token){
    global $oauthSettings;
    $ch = curl_init();
    error_log('[DL][Oauth][Get Task Result][Token: '.$token.']:[Task id: '.$taskid.']Start' );
    $url = $oauthSettings->url. '/api/GetResult';
    $headers = array('Authorization: Bearer ' . $token, 'Content-Type: application/x-www-form-urlencoded');
    $post_fields = new \stdClass();
    $post_fields->data = new \stdClass();
    $post_fields->data->task_id = $taskid;
    $post_fields = http_build_query($post_fields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    $info = curl_exec($ch);
    $ee = curl_getinfo($ch);
    $result = false;
    list($header, $body) = explode("\r\n\r\n", $info, 2);
    if (!curl_errno($ch) && $ee['http_code']== 200) {
        error_log('[DL][Oauth][Get Task Result][Token: '.$token.']:[Task id: '.$taskid.'] Success' );
        
        $result = json_decode($body);
        
    } else {
        error_log('[DL][Oauth][Get Task Result][Token: '.$token.']:[Task id: '.$taskid.'] Error' );
    }
    
    curl_close($ch);
    return $result;
}

function getJsonData($url){
    $ch = curl_init();
    error_log('[DL][Get JSON]'. $url );
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    $info = curl_exec($ch);
    $ee = curl_getinfo($ch);
    $result = false;
    list($header, $body) = explode("\r\n\r\n", $info, 2);
    if (!curl_errno($ch) && $ee['http_code']== 200) {
        error_log('[DL][Get JSON]'.$url.' Success' );
    
        $result = json_decode($body);
    
    } else {
        error_log('[DL][Get JSON]'.$url.' Error' );
    }
    
    curl_close($ch);
    return $result;
}

function getHtmlPage($url) {
    
        
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR. 'cache'. DIRECTORY_SEPARATOR. md5($url);
        
        if (file_exists($filename)){
            $content= file_get_contents($filename);
            $doc = new ClmsDOMDocument();
            $doc->loadHTML($content);
            return $doc;
        }
        
        
        $ch = curl_init ();
        
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_FAILONERROR, TRUE );
        
        $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.89 Safari/537.36';
        curl_setopt ( $ch,  CURLOPT_USERAGENT, $userAgent);
        
        if ($output = curl_exec ( $ch )) {
            $info = curl_getinfo ( $ch );
            
            if ($info ['http_code'] == 301) {
                
                return getHtmlPage ( $info ['redirect_url'] );
            } else {
               
                
                unset ( $matches );
                unset ( $charset );
                preg_match ( '@<meta\s+http-equiv="Content-Type"\s+content="([\w/]+)(;\s+charset=([^\s"]+))?@i', $output, $matches );
                
                if (isset ( $matches [3] ))
                    $charset = $matches [3];
                
                if (isset ( $charset ) && strtolower ( $charset ) !== 'utf-8') {
                    
                    $output = mb_convert_encoding ( $output, "utf-8", "big5" );
                }
                if(empty($output)){
                    
                }
                
                file_put_contents($filename, $output);
                $doc = new ClmsDOMDocument();
                $doc->loadHTML($output);
                return $doc;
            }
        } else {
            //var_dump ( $output );
        }
        return null;
    }
    
    
 

/* ===============================================================
   End of ajax.php
   =============================================================== */
?>