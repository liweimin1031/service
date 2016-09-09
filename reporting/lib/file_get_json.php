<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : file_get_json.php
 * AUTHOR      : Yunzhao Lu
 * SYNOPSIS    :
 * DESCRIPTION : LAS get json for reporting
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6048 $)
 * CREATED     : 18-NOV-2015
 * LASTUPDATES : $Author: patrickw $ on $Date: 2014-12-08 17:58:16 +0800 (Mon, 08 Dec 2014) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)api.php        1.0               18-NOV-2015
   by Yunzhao Lu


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */


/* ===============================================================
   Begin of api.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__FILE__) . '/../../inc.php');

use Las\Core\Util\Ajax;
use Las\Core\Util\AccessToken;
use Las\Core\Task\TaskResultManager;

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */

if($_REQUEST['token']) {
    $data_token = $_REQUEST['token'];
    error_log("token:$data_token");
    $atk = new AccessToken();
    if($vtk = $atk->validateToken($data_token)) {
        error_log('access');
        $tr = (object)(new TaskResultManager($vtk->taskid));
        $atk->deleteToken($data_token);
        $json_data = json_encode($tr->output);
    } else {
        $json_data = '';
        error_log("[".date('Y-m-d H:i:s')."][LAS][Reporting][ValidateToken Fail]");
    }

} else {
    error_log("[".date('Y-m-d H:i:s')."][LAS][Reporting][No Token]");
    $json_data = '';
    exit;
}

/* ===============================================================
   End of file_get_json.php
   =============================================================== */

//$path = '/tmp/json_hkeaa.json';
//$json_hkeaa = file_get_contents($path);

?>
var jsonData = <?php echo $json_data?>;
//console.log(jsonData);
