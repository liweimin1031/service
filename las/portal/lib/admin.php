<?php

/* --------------------------------------------------------------- */
/**
 * FILE NAME   : op.php
 * AUTHOR      : Sandy Wong
 * SYNOPSIS    :
 * DESCRIPTION : Operation Portal Network API
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision$)
 * CREATED     : 05-JUL-2013
 * LASTUPDATES : $Author$ on $Date$
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)op.php           1.0 05-JUL-2013
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
   Begin of op.php
   =============================================================== */

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__DIR__) . '/../../inc.php');

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */
$CS_METHODS = array('/getAllSchool', '/getUserRoles', '/getSchoolRoleUser', '/getParents', '/deleteParent', '/getChildren', '/changeUserPassword');
$FS_METHODS = array('/getAllSchool', '/getSchoolFSpace', '/addOauthClient', '/getAllOauthClient', '/updateOauthClient', '/deleteOauthClient', '/newOauthClientSecret', '/addOauthServer', '/getAllOauthServer', '/deleteOauthServer', '/updateOauthServer');
$UPG_METHODS = array('/cancelUpgrade', '/checkNewVersion', '/getAllOauthClient', '/getReleaseNote', '/getSoftwareReleases', '/getSoftwareVersion', '/getUpgradeConfiguration', '/updateUpgradeConfiguration', '/upgradeSoftware', '/isCloudVersion');

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */

use Las\Core\Util\Ajax;
use Las\Core\Oauth\OauthStorage;
use Las\Core\Oauth\OauthException;
use Las\Tools\Mongo\Exception\DbException;
use Las\Core\Oauth\OauthClient;

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */
function admin_add_oauthclient($data) {
    $client_type = isset($data->client_type)? OauthClient::getValidClientType($data->client_type): null;
    $client_name = isset($data->client_name)?$data->client_name: null;
    $redirect_uri = isset($data->redirect_uri)?$data->redirect_uri: null;
    $description = isset($data->description)? $data->description: '';
    
    if(empty($client_name)){
       throw new OauthException(OauthException::LAS_ERROR_OAUTH_ECLIENT_NAME);
    }
    if(empty($client_type)){
        throw new OauthException(OauthException::LAS_ERROR_OAUTH_ECLIENT_TYPE);
    }
    
    if(empty($redirect_uri) && OauthClient::isRedirectURIRequired($client_type)){
        throw new OauthException(OauthException::LAS_ERROR_OAUTH_ERETURN_URI);
    }
    $generateKeyPair = OauthClient::isKeyPairSupprted($client_type);
    $storage = new OauthStorage();
    $result =  $storage->addClientDetails($client_name, $redirect_uri, $client_type, $description, $generateKeyPair);

    $data = array();
    $data['client_id'] = $result->client_id;
    $data['client_secret'] = $result->client_secret;
    
    if ($generateKeyPair) {
        $data['public_key'] = $result->public_key;
    }
    
    return $data;
    
}


function admin_delete_oauthclient($data) {

    if (!isset($data) || !isset($data->client_id) || empty($data->client_id)) {
        throw new OauthException(OauthException::LAS_ERROR_APP_EINVAL);
    }
    $client_ids = $data->client_id;

    if (!is_array($client_ids)) {
        $client_ids = array();
        $client_ids[0] = $data->client_id;
    }
    $storage = new OauthStorage();
    foreach ($client_ids as $client_id) {
        $storage->removeClientDetails($client_id);
    }
    return true;
}

function admin_get_alloauthclient() {
    // get all school list first
    //$schools = School::getAll();
    $storage = new OauthStorage();
    $clients = $storage->listClientDetails();

    foreach ($clients as $client) {
        //unset($client->client_secret);
        unset($client->private_key);
        unset($client->tmp);
    }

    return $clients;
}


function admin_new_oauthclientsecret() {
    $secret = OauthStorage::generateRandomString();
    return $secret;

}

function admin_new_oauthkeypair($data) {
    
    if (!isset($data) || (!isset($data->client_id))) {
        throw new OauthException(OauthException::LAS_ERROR_APP_EINVAL);
    }

    // check if it is a valid oauth client with key pair support
    $storage = new OauthStorage();
    
    $client = $storage->getClientDetailsAsObj($data->client_id);
    // If the client does not exist or the type does not need the key pair
    //if (!$client || !OauthClient::isKeyPairSupprted($data->client_id)) {
    if (!$client ) {
        throw new OauthException(OauthException::LAS_ERROR_OAUTH_EINVAL);
    } 
    // generate key pairs
    $keypair = OauthStorage::generateKeyPair();
    
    // save to db for temp store the private key
    $storage->setTemporyClientDetails($data->client_id, $keypair);
    
        // return to public key
    unset($keypair["private_key"]);
    return $keypair;
}


function admin_update_oauthclient($data) {
    if (!isset($data) || !isset($data->client_id)) {
        throw new OauthException(OauthException::LAS_ERROR_APP_EINVAL);
    }

    $storage = new OauthStorage();
    $client = $storage->getClientDetailsAsObj($data->client_id);
    $client_id = $data->client_id;
    if ($client) {
        if (isset($data->public_key) && !empty($data->public_key)) {
            if ($client->public_key != $data->public_key) {
                //$keyPair = OauthKeyPair::getKeyPair($client->client_id, $data->public_key);
                $keyPair = $client->tmp;
                //if (count($keyPair) === 1) {
                if ($keyPair) {
                    //$client->public_key = $keyPair->public_key;
                    //$client->private_key = $keyPair->private_key;
                    if ($keyPair->public_key === $data->public_key) {
                        $data->private_key = $keyPair->private_key;
                        $data->tmp = '';
                    } else {
                       throw new OauthException(OauthException::LAS_ERROR_OAUTH_EKEYPAIR);
                    }
                } else {
                    // cannot find private key
                    throw new OauthException(OauthException::LAS_ERROR_APP_EINVAL);
                    
                }
            }
        }
        $data = $storage->setClientDetails($client_id, $data);
        return $data;
    } else {

        throw new OauthException(OauthException::LAS_ERROR_OAUTH_EINVAL);
    }
}



function admin_add_oauthserver($data) {
    if (!isset($data) || 
        !isset($data->code) || 
        !isset($data->name) || 
        !isset($data->client_id) || 
        !isset($data->client_secret) ||
        !isset($data->redirect_uri) ||
        !isset($data->authorize_url) ||
        !isset($data->access_url) ||
        !isset($data->information_url)) {
        $result = Ajax::createErrorMsgByCode(LAS_ERROR_APP_EINVAL);
        echo $result;
        exit;
    }
    
    // check if code unique
    $oauthServer = OauthServer::getOauthServer($data->code);
    if ($oauthServer) {
        $result = Ajax::createErrorMsgByCode(LAS_ERROR_OPERATOR_CONNECT_CODE_EXIST);
        echo $result;
        exit;
    }
    
    // new oauth server in db
    $oauthServer = new OauthServer;
    $oauthServer->code = $data->code;
    $oauthServer->name = $data->name;
    $oauthServer->description = $data->description;
    $oauthServer->client_id = $data->client_id;
    $oauthServer->client_secret = $data->client_secret;
    $oauthServer->redirect_uri = $data->redirect_uri;
    $oauthServer->authorize_url = $data->authorize_url;
    $oauthServer->access_url = $data->access_url;
    $oauthServer->information_url = $data->information_url;
    
    try {
        
        $oauthServer->save();
        
        $result = Ajax::createDataMsg();
    } catch (DbException $e) {
        $result = Ajax::createErrorMsgByCode(LAS_ERROR_DB_EINSERT);
    }
    
    echo $result;
    exit;
}




function admin_delete_oauthserver($data) {
    if (!isset($data) || !isset($data->code)) {
        $result = Ajax::createErrorMsgByCode(LAS_ERROR_APP_EINVAL);
        echo $result;
        exit;
    }
    $codes = json_decode($data->code);
    if (!is_array($codes)) {
        $codes[0] = $codes;
    }
    foreach ($codes as $code) {
        $oauthServer = OauthServer::getOauthServer($code);
        if ($oauthServer) {
            $oauthServer->delete();
        }
    }
    $result = Ajax::createDataMsg();
    echo $result;
    exit;
}


function admin_get_alloauthserver() {
    $oauthServers = OauthServer::getAll();
    $result = Ajax::createDataMsg($oauthServers);
    echo $result;
    exit;
    
}


function admin_get_oauthclientpkey() {
    //if (!isset($data) || !isset($data->client_id)) {
    if (!isset($_GET['client_id'])) {
        $result = Ajax::createErrorMsgByCode(LAS_ERROR_APP_EINVAL);
        echo $result;
        exit;
    }
    $client_id = $_GET['client_id'];
    
    $client = OauthClient::getByClientID($client_id);
    if ($client) {
        $client = is_array($client) ? $client[0] : $client;
        
        // check if client have public key
        if (isset($client->public_key) && strlen($client->public_key)>0) {
            // set header to allow browser to download the file
            header('Cache-Control: maxage=120');
            header('Expires: '.date(DATE_COOKIE,time()+120)); // Cache for 2 mins
            header('Pragma: public');
            header('Content-type: application/force-download');
            header('Content-Transfer-Encoding: Binary');
            header('Content-Type: text/plain');
            //header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $client->client_name . '.PUB"');
                    
            echo $client->public_key;
            exit;
        } else {
            // the client have no public key
            $result = Ajax::createErrorMsgByCode(LAS_ERROR_OPERATOR_OAUTH_NO_PK);
            echo $result;
            exit;
        }
    } else {
        $result = Ajax::createErrorMsgByCode(LAS_ERROR_OPERATOR_OAUTH_INVALID_ID);
        echo $result;
        exit;
    }
}










function admin_update_oauthserver($data) {
    if (!isset($data) || !isset($data->code)) {
        $result = Ajax::createErrorMsgByCode(LAS_ERROR_APP_EINVAL);
        echo $result;
        exit;
    }
    
    // find oauth server
    $oauthServer = OauthServer::getOauthServer($data->code);
    if ($oauthServer) {
        if (isset($data->name)) {
            $oauthServer->name = $data->name;
        }
        if (isset($data->description)) {
            $oauthServer->description = $data->description;
        }
        if (isset($data->client_id)) {
            $oauthServer->client_id = $data->client_id;
        }
        if (isset($data->client_secret)) {
            $oauthServer->client_secret = $data->client_secret;
        }
        if (isset($data->redirect_uri)) {
            $oauthServer->redirect_uri = $data->redirect_uri;
        }
        if (isset($data->authorize_url)) {
            $oauthServer->authorize_url = $data->authorize_url;
        }
        if (isset($data->access_url)) {
            $oauthServer->access_url = $data->access_url;
        }
        if (isset($data->information_url)) {
            $oauthServer->information_url = $data->information_url;
        }
        
        try {
            $oauthServer->save();
        
            $result = Ajax::createDataMsg();
        } catch (DbException $e) {
            $result = Ajax::createErrorMsgByCode(LAS_ERROR_DB_EUPDATE);
        }
        
    } else {
        $result = Ajax::createErrorMsgByCode(LAS_ERROR_OPERATOR_CONNECT_INVALID_CODE);
    }
    
    echo $result;
    exit;
}


/**
 * Program entry point
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

if (!isset($_SERVER['PATH_INFO'])) {
    $result = Ajax::createErrorMsgByCode(LAS_ERROR_APP_EINVAL);
    echo $result;
    exit;
}
$haskey = true; // for not-implement user only
/*
$haskey = false;
if ( !isset($LAS_USER) ) {
    if (($_SERVER['PATH_INFO'] == '/addOauthClient') && isset($_POST['key']) && ($_POST['key'] == $LAS_CFG->key)) {
        // check if use pass a key
        $haskey = true;
    } else {
        $result = Ajax::createErrorMsg(
                'Only CLMS user can access this script!', LAS_ERROR_APP_EPERM
        );
        echo $result;
        exit;
    }
}

// check user role, should be operator
if ((!$haskey) && (($LAS_USER->role != User::OPERATOR) && ($LAS_USER->role != User::OPERATOR_CS) && ($LAS_USER->role != User::OPERATOR_FS) && ($LAS_USER->role != User::OPERATOR_UPG))) {
    $result = Ajax::createErrorMsg(
            'Only operator can access this script!', LAS_ERROR_APP_EPERM
    );
    echo $result;
    exit;
}*/

// update session
//UserSession::updateSession();

// get post data
$result = 0;

$data = isset($_REQUEST['data'])?json_decode(json_encode($_REQUEST['data'])): null;

// Handle information request
$pathInfo = $_SERVER['PATH_INFO'];
try {
    if (isset ( $pathInfo )) {
        
        switch ($pathInfo) {
            case '/addOauthClient' :
                $result = admin_add_oauthclient ($data);
                break;
            case '/getAllOauthClient' :
                $result = admin_get_alloauthclient ();
                break;
            case '/deleteOauthClient' :
                $result = admin_delete_oauthclient ( $data );
                break;
            case '/newOauthClientSecret' :
                $result = admin_new_oauthclientsecret ();
                break;
            case '/updateOauthClient' :
                $result = admin_update_oauthclient ( $data );
                break;
                
            case '/newOauthKeyPair' :
                $result = admin_new_oauthkeypair ( $data );
                break;
                    
            case '/addOauthServer' :
                admin_add_oauthserver ( $json );
                break;
           
            case '/deleteOauthServer' :
                admin_delete_oauthserver ( $json );
                break;
            
            case '/getAllOauthServer' :
                admin_get_alloauthserver ();
                break;
            case '/getOauthClientPKey' :
                admin_get_oauthclientpkey ();
                break;
           
            
            
            case '/updateOauthServer' :
                admin_update_oauthserver ( $json );
                break;
            default :
                throw new OauthException(OauthException::LAS_ERROR_APP_EINVAL);
        }
    }
    echo Ajax::createDataMsg($result);
} catch ( DbException $e ) {
    $result = Ajax::createErrorMsgByCode ( $e->getCode () );
    echo $result;
} catch(OauthException $ex){
    $result = Ajax::createErrorMsgByCode($ex->getCode());
    echo $result;
} catch(\Exception $common){
    //To do
}

/* ===============================================================
   End of op.php
   =============================================================== */
?>
