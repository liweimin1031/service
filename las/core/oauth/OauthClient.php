<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : OauthClient.php
 * AUTHOR      : Sandy Wong
 * SYNOPSIS    :
 * DESCRIPTION : LAS Oauthclient Object
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision$)
 * CREATED     : 23-JUL-2013
 * LASTUPDATES : $Author$ on $Date$
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)OauthClient.php          1.0 23-JUL-2013
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
   Begin of OauthClient.php
   =============================================================== */
namespace Las\Core\Oauth;


use Las\Tools\Mongo\MongoDao;
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

/**
 * Main OauthClient class
 * 
 * Handle the database insert, delete, search function
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
class OauthClient {
    const STATUS_ENABLE       = 0;
    const STATUS_DISABLE      = 1;
    
    const CLIENT_TYPE_CREDENTIAL         = 'CREDENTIAL';
    const CLIENT_TYPE_CONNECT            = 'CONNECT';
    
    
    const COLLECTION = 'oauth_client';
    
    public $client_id = null;
    public $client_secret = null;
    public $redirect_uri = null;
    public $client_name = null;
    public $client_type = null;
    public $public_key = null;
    public $private_key = null;
    public $lastmodified = null;
    public $timecreated = null;
    public $status = null;
    public $description = null;
    
    
    /**
     *
     * This function get oauth client
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  bool Returns client by client id; otherwise,
                <code>false</code> is returned
     * @see
     * @author  Sandy Wong
     * @warnings
     * @updates
     */
    public static function getClient($client_id) {
        $query = array('client_id' => $client_id);
        $obj= MongoDao::searchOne(self::COLLECTION, $query);
        
        $obj = json_decode(json_encode($obj));
        unset ($obj->_id);
        return $obj;
    }
    
    public static function updateClient($client_id, $data){
       $query = array('client_id' => $client_id);
       $data = (array)$data;
       $data['lastmodified'] = time();
       $update = array('$set'=> $data);
       MongoDao::findAndModify(self::COLLECTION, $query, $update);
       
       return self::getClient($client_id);
    }
    
    /**
     * 
     * Delete a oauth client by client id
     *
     * @since  Version 1.0
     * @param string $client_id client id
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function deleteClient($client_id){
        $query = array('client_id' => $client_id);
        return MongoDao::deleteList(self::COLLECTION, $query);
    }
    
    public static function createClient($client_id, $client_secret, $redirect_uri, $client_type, $client_name, $public_key, $private_key, $description='') {
        $client = new  OauthClient();
        $client->client_id = $client_id;
        $client->client_secret = $client_secret;
        $client->redirect_uri = $redirect_uri;
        $client->client_type = $client_type;
        $client->client_name = $client_name;
        $client->public_key = $public_key;
        $client->private_key = $private_key;
        $client->status = self::STATUS_ENABLE;
        $client->description = $description;
        $client->timecreated = time();
        $client->lastmodified = time();
        
        
        $client->save();
        return $client;
                
    }
    
    
    public function save(){
        return MongoDao::save(self::COLLECTION, $this);
    }
    public static function getAll(){
        try {
            $result = array();
            $items   = MongoDao::search(self::COLLECTION, array());
            foreach($items as $item){
                $item = json_decode(json_encode($item));
                unset($item->_id);
                $result[]= $item; 
            }
            return $result;
        } catch (DbException $e) {
            return false;
        }
    }
    
    public static function getValidClientType($client_type){
        if (trim($client_type) == 'LAS API'){
            return constant("\Las\Core\Oauth\OauthClient::CLIENT_TYPE_CREDENTIAL");
        }
        if (trim($client_type) == 'LAS USER'){
            return constant("\Las\Core\Oauth\OauthClient::CLIENT_TYPE_CONNECT");
        }
        return false;
    }
    
    public static function isKeyPairSupprted($client_type){
        return $client_type == self ::CLIENT_TYPE_CONNECT;
    }
    
    public static function isRedirectURIRequired($client_type){
        return $client_type == self ::CLIENT_TYPE_CONNECT;
    }
}

/* ===============================================================
   End of OauthClient.php
   =============================================================== */
?>
