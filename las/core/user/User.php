<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : User.php
 * AUTHOR      : Patrick C. K. Wu
 * SYNOPSIS    :
 * DESCRIPTION : CLMS User Object
 * SEE ALSO    :
 * VERSION     : 1.4 ($Revision: 6800 $)
 * CREATED     : 06-AUG-2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-02-27 10:48:00 +0800 (Fri, 27 Feb 2015) $
 * UPDATES     : 02-APR-2014    - Avoid deleted user from login with connect
                                  account (Bug# 6193)
                 15-MAY-2014    - Support school based storage configuration
                                  (Bug# 6276)
                 03-JUL-2014    - Operator for upgrade (Bug# 6306)
                 04-FEB-2015    - Add plaintext password field (Bug# 6549)
                 05-FEB-2015    - Import DAO DbException class (Bug# 6549)
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)User.php                 1.0 06-AUG-2013
                                1.2 16-JAN-2014
                                1.3 03-JUL-2014
                                1.4 05-FEB-2015
   by Patrick C. K. Wu


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */


/* ===============================================================
   Begin of User.php
   =============================================================== */
namespace Las\Core\User;


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */

use Las\Tools\Mongo\Exception\DbException;
use Las\Tools\Mongo\MongoDao;


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
 * Main User class
 *
 * @since       Version 1.0.00
 * @param       nil
 * @return      nil
 * @see
 * @author      Patrick C. K. Wu
 * @testing
 * @warnings
 * @updates     
 */
class User{
    const UNKNOWN       = 0;
    const OPERATOR      = 1;
    
    const DELETED_TAG   = '.deleted.';
    const QRCODE_IV     = 'ASTRI_CLS_INITIALIZATION_VECTOR';
    const QRCODE_KEY    = 'hardcode';
    
    const COLLECTION = 'user';

    private static $OPTIONS = Array(
        'bg_color',             // Portal background color
        'bg_name',              // Portal background theme name
        'bg_path',              // Portal background image path
        'coordinator',
        'change_pswd',          // Force user to change password
        'parent_phones',        // Parents' phone number(s)
        'phones',               // User's phone number(s)
        'imageid'
    );
    
    public $id = null;
    public $auth = "hash";
    public $deleted = 0;
    public $loginname = null;
    public $password = null;
    public $name = null;
    public $email = null;
    public $role = null;
    public $mobile = null;
    public $options = null;
    public $timecreated = null;
    public $lastmodified = null;

    /**
     *
     * This function convert standard user entry in PHP object to internal
     * User entry
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   object $object   The user entry in PHP object
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates 04-FEB-2015     - Add plaintext password field (Bug# 6549)
     */
    private static function ext2int($object) {
        if ( empty($object) ) {
            return;
        }
        $name = new \StdClass;
        $name->cname = $object->cname;
        $name->ename = $object->ename;
        if ( !empty($object->password) ) {
            $object->password = User::hashPassword($object->password);
        }
        $object->name = $name;

    }
    
    

    /**
     *
     * This function hash internal user entry in PHP object to standard
     * User entry
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   int $uid         The user ID
     * @return  string Returns the hash string for the image
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates  
     */
    public static function hashAvatar($uid=0) {
        global $LAS_CFG;

        $key = isset($LAS_CFG->key) ? $LAS_CFG->key : 'AvatAr';
        $hash = md5($uid . $key);
        return ($hash);
    }

    /**
     *
     * This function convert internal user entry in PHP object to standard
     * User entry
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   object $object   The user entry in PHP object
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates  
     */
    private static function int2ext($object) {
        global $LAS_CFG;

        if ( empty($object) ) {
            return;
        }
        if(isset($object->_id)){
            $object->id = $object->_id->{'$id'};
            unset($object->_id);
        }

        // Handle options field
        if ( !empty($object->options) ) {
            $options = json_decode($object->options);
            $fields = self::$OPTIONS;
            foreach ( $fields as $field ) {
                $object->$field = '';
                if ( isset($options->$field) ) {
                    $object->$field = $options->$field;
                }
            }
        }


        $hash = User::hashAvatar($object->id);
        $object->image = $LAS_CFG->wwwroot
                       . '/las/portal/lib/las_avatar.php?'
                       . 'uid=' . $object->id . '&hash=' . $hash;
    }

    /**
     *
     * This function update partial options' fields contains in 
     * <code>object</code> and merge with existing options' fields (if any)
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   object $object   The user entry in PHP object
     * @return  nil
     * @see
     * @author  Patrick C. K. Wu
     * @warnings
     * @updates
     */
    private static function updateOptions($object) {
        if ( empty($object) ) {
            return;
        }

        $options = new \StdClass;
        $fields = self::$OPTIONS;
        if ( isset($object->id) ) {
            $user = User::getById($object->id);
            if ( $user ) {
                $user = $user->getData();
                $fields = self::$OPTIONS;
                foreach ( $fields as $field ) {
                    if ( isset($user->$field) ) {
                        $options->$field = $user->$field;
                    }
                }
            }
        }
        foreach ( $fields as $field ) {
            if ( isset($object->$field) ) {
                $options->$field = $object->$field;
            }
        }
        $strOptions = json_encode($options);
        $object->options = $strOptions;
    }

    /**
     *
     * This function decode and validate the user QRcode string
     * <code>qrcode</code>
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   object $qrcode  The QR Code to be validate
     * @return  object Returns user object on success; otherwise,
                <code>false</code>
     * @see
     * @author  Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates
     */
    public static function decodeQRCode($qrcode) {
        global $LAS_CFG;

        $iv = User::QRCODE_IV;
        $salt = $LAS_CFG->salt;
        $crypttext = base64_decode($qrcode, true);
        $text = mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256, $salt, $crypttext, MCRYPT_MODE_ECB, $iv
        );
        $tokens = explode(',', $text);

        if ( count($tokens) === 4 ) {
            $uid = $tokens[0];
            $username = $tokens[1];

            $user = User::getById($uid);
            if (
                $user && !($user->deleted) && ($user->loginname === $username)
            ) {
                $key = !empty($user->key) ? $user->key : "hardcode";
                $checksum = md5(
                    $uid . ':' . $salt . ':' . $key . ':' . $username
                );
                if ( strcmp($tokens[2], $checksum) === 0 ) {
                    return($user->getData());
                }
            }
        }
        return(false);
    }

    /**
     *
     * Delete user with user ID <code>uid</code>
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   int $uid        The user ID
     * @param   int $defaultTime  A timestamp for deletion default is empty
     * @return  bool
     * @see
     * @author  Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates add a defaultTime stamp from version 1.5
     */
    public static function deleteUser($uid, $defaultTime = null) {
        $result = false;

        $user = User::getById($uid);
        if ( $user ) {
            if ( $user->deleted ) {
                return(true);
            }
            $time = time();
            if(!empty($defaultTime)){
              $time = $defaultTime;  
            } 
            try {
                $user->deleted = 1;
                $user->loginname = $user->loginname 
                                 . User::DELETED_TAG
                                 . $time;
                $user->mobile = $user->mobile 
                                 . User::DELETED_TAG
                                 . $time;
                $user->email = $user->email 
                                 . User::DELETED_TAG
                                 . $time;
                $user->save();
            }
            catch (DbException $e) {
            }
        }
        return($result);
    }

    /**
     *
     * This function encode the <code>user</code> to QR Code string
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   object $user    The User object
     * @return  string The QRCode string
     * @see
     * @author  Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates
     */
    public static function encodeQRCode($user) {
        global $LAS_CFG;

        if ( $user && isset($user->id) && isset($user->loginname) ) {
            $iv = User::QRCODE_IV;
            $salt = $LAS_CFG->salt;
            $uid = $user->id;
            $username = $user->loginname;
            $key = !empty($user->key) ? $user->key : "hardcode";

            $text = $uid . ',' . $username . ','
                  . md5($uid . ':' . $salt . ':' . $key . ':' . $username)
                  . ',';
            $crypttext = mcrypt_encrypt(
                MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, $iv
            );
            $crypttext = base64_encode($crypttext);

            // Add urlencode to ensure QRCode readers get the string correctly
            $crypttext = urlencode($crypttext);
            return($crypttext);
        }
        else {
            return(false);
        }
    }

    /**
     *
     * Auth by loginname and password
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   string $uniquename   The user loginname@scode
     * @param   string $password     The password
     * @return  \StdClass
     * @see
     * @author  Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates
     */
    public static function authenticate($loginname, $password) {
        global $LAS_CFG;

        $user = User::getByloginname($loginname);
        if ($user) {

            $salt = empty($LAS_CFG->salt) ? '' : $LAS_CFG->salt;

            $hash = User::hashPassword($password);
            if ( $hash === $user->password ) {
                
                return $user;
            }
        }
        return(false);
    }

    
    /**
     *
     * Get the standard user entry
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   nil
     * @return  \StdClass
     * @see
     * @author  Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates
     */
    public function getData() {
        $object = $this;
        
        User::int2ext($object);
        return $object;
    }
    
    /**
     *
     * Get role list
     *
     * @version 1.0
     * @since   Version 1.0
     * @return  a role array
     * @see
     * @author  Sandy Wong
     * @testing
     * @warnings
     * @updates 03-JUL-2014     - Operator for upgrade (Bug# 6306)
     */
    public static function getRoles() {
        $roles = array();


        $roles[1]->id = User::OPERATOR;
        $roles[4]->name = 'OPERATOR';


        return $roles;
    }

    public static function hashPassword($password) {
        global $LAS_CFG;

        $salt = empty($LAS_CFG->salt) ? '' : $LAS_CFG->salt;
        $hash = md5($password . $salt);
        return($hash);
    }


    /**
     *
     * Set the value of the object
     *
     * @version 1.0
     * @since   Version 1.0
     * @param   object $object   The standard user entry in PHP object
     * @see
     * @author  Patrick C. K. Wu
     * @testing
     * @warnings
     * @updates
     */
    public function setData($object) {
        User::ext2int($object);
        
        User::updateOptions($object);
        $query = array('_id' => $object->id);
        unset($object->id);
        $update = array('$set' => $object);
        return MongoDao::findAndModify(self::COLLECTION, $query, $update);
    }
    
    public static function createUser($loginname, $password, $cname='', $ename = '', $role = User::OPERATOR,  $email = null){
        $user = new User();
        $user->loginname = $loginname;
        
        $user->name->cname = $cname;
        $user->name->ename = $ename;
        if ( !empty($password) ) {
            $user->password = User::hashPassword($password);
        }
        $user->role = $role;
        $user->timecreated = time();
        $user->lastmodified = time();
        unset($user->id);
        
        MongoDao::save(self::COLLECTION, $user);
        
        return $user->getData();
        
    }
    
    public static function getById($id)
    {
        $keys = array (
                "_id"=>new \MongoId($id)
        );
        $object=  MongoDao::searchOne(self::COLLECTION, $keys);
        
        if($object){
            $object = json_decode(json_encode($object));
            self::int2ext($object);
            return $object;
        } 
        return false;
    }
    
    private static function getByLoginname($loginname)
    {
        $keys = array (
                "loginname"=>$loginname
        );
         $object=  MongoDao::searchOne(self::COLLECTION, $keys);
        if($object){
            $object = json_decode(json_encode($object));
            self::int2ext($object);
            return $object;
        } 
        return false;
    }
}



/* ===============================================================
   End of User.php
   =============================================================== */
?>
