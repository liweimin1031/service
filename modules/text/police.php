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
use Astri\Lib\Database\MongoDao;

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */
$version = 20151126;
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


if(isset($_SESSION) && isset($_SESSION['version']) && $_SESSION['version'] == $version){
    
}else {
    session_unset();
}


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */
if ( isset($_SERVER['PATH_INFO']) ) {
    switch ($_SERVER['PATH_INFO']) {
        case '/getRecentPost':
            $result = getRecentPost();
            $msg->success = true;
            $msg->data = $result;
            break;
        
        case '/getPostComment':
            $post_id = $_REQUEST['post_id'];
            $page = $_REQUEST['page'];
            if($post_id){
                $result = getPostComment( $post_id, $page*10-10);
                if(!empty($result)){
                    $msg->success= true;
                    $msg->data = $result;
                } else{
                    $msg->error->reason="Analysis In Progress";
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




function getRecentPost(){
    $cursor = MongoDao::search('police_post', array());
    $result = array();
    foreach ($cursor as $doc) {
        $doc = json_decode(json_encode($doc));
        unset($doc->_id);
        $result[]= json_decode(json_encode($doc));
    }
    return $result;
    
}

function getPostComment($post_id, $start = 0){
    //$cursor = MongoDao::search('police_comment', array('post_id'=>$post_id, "output" => array('$exists'=>true,'$ne' => null)), $start, 10);
    $cursor = MongoDao::search('police_comment', array('post_id'=>$post_id), $start, 10, array('create_timestamp'=> -1));
    
    $result = array();
    foreach ($cursor as $doc) {
        $doc = json_decode(json_encode($doc));
        unset($doc->_id);
        $tempDoc = new \stdClass();
        $tempDoc->name = $doc->from->name;
        $tempDoc->message = $doc->message;
        
        $tempDoc->created_time = date('Y-m-d H:i:s', strtotime($doc->created_time));
        unset($doc->_id);
        if($doc->empty_result){
            $tempDoc->score_training = 0;
            $tempDoc->score_words = 0;
           
        } else {
            $tempDoc->score_training =  $doc->score_training;
            $tempDoc->score_words = $doc->score_words;
            $tempDoc->output = $doc->output;
        } 
        $result[]= $tempDoc;
    }
    return $result;
}



/* ===============================================================
   End of ajax.php
   =============================================================== */
?>