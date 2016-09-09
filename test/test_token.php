<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : test_token.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Aug 28, 2015
 * LASTUPDATES : $Author: csdhong $ on $Date: 4:36:11 PM Aug 28, 2015 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) test_token.php              1.0 Aug 28, 2015
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
   Begin of test_token.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */

require_once(dirname(__DIR__).DIRECTORY_SEPARATOR. 'inc.php');

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */
use Las\Core\Oauth\LasOauthServer;

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */
$oauthUtil = new LasOauthServer();
$token_data = $oauthUtil->getAccessTokenData();

if (!$token_data) {
    header('HTTP/1.0 401 Unauthorized');
    exit();
}

$data = new \stdClass();
$data->success = true;
$data->token = $token_data;

echo json_encode($data);


/* ===============================================================
   End of test_token.php
   =============================================================== */
?>