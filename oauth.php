<?php

/* --------------------------------------------------------------- */
/**
 * FILE NAME   : oauth.php
 * AUTHOR      : Sandy Wong
 * SYNOPSIS    :
 * DESCRIPTION : Oauth API
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision$)
 * CREATED     : 06-MAY-2013
 * LASTUPDATES : $Author$ on $Date$
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)oauth.php                1.0 06-MAY-2013
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
  Begin of oauth.php
  =============================================================== */

require_once(dirname(__FILE__) .DIRECTORY_SEPARATOR. 'inc.php');
/* ---------------------------------------------------------------
  Included Library
  --------------------------------------------------------------- */
// Common LMS PHP libraries
use Las\Core\Oauth\OauthSession;
use Las\Core\Oauth\LasOauthServer;


$lasOauthServer = new LasOauthServer();

// parse input path
if ( isset($_SERVER['PATH_INFO']) ) {
    switch ($_SERVER['PATH_INFO']) {
        case '/authorize':
            // check if user is authrized, otherwise show login page
            $is_authorized = false;
            if ( !isset($LAS_USER) ) {
                // save parameter to session first
               
                $response_type = isset($_REQUEST['response_type'])?$_REQUEST['response_type']: null;
                $client_id = isset($_REQUEST['client_id'])?$_REQUEST['client_id']:null;
                $redirect_uri = isset($_REQUEST['redirect_uri'])? $_REQUEST['redirect_uri']: null;
                $state = isset($_REQUEST['state'])?$_REQUEST['state']: null;
                $scope = isset($_REQUEST['scope'])?$_REQUEST['scope']:null;
                $session = OauthSession::createSession($response_type,$client_id ,$redirect_uri, $state, $scope);
                
                // redirect to login page
                header('Location: ../index.php?caller=oauth&redirect_url=oauth.php/authorize');
                break;
            } else {
                $result = OauthSession::checkSession();
                
                if ($result) {
                    $_GET = array_merge($_SESSION['oauth'], $_GET);
                    
                    // unset session
                    OauthSession::deleteSession();
                }
                $is_authorized = true;
                $lasOauthServer->sendAuthorizeCode($is_authorized, $LAS_USER->id);
                
            }
           
            break;
        case '/access':
            // send out access token
            $lasOauthServer->sendAccessToken();
            break;
        default:
	        header('HTTP/1.0 404 Not Found');
	        exit();
    }
} else {
    header('HTTP/1.0 404 Not Found');
    exit();
}

/* ===============================================================
  End of oauth.php
  =============================================================== */
?>