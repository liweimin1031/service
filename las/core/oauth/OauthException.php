<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : OauthException.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Aug 24, 2015
 * LASTUPDATES : $Author: csdhong $ on $Date: 4:13:08 PM Aug 24, 2015 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) OauthException.php              1.0 Aug 24, 2015
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
   Begin of OauthException.php
   =============================================================== */
namespace Las\Core\Oauth;

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
class OauthException extends \Exception{
    
    const LAS_ERROR_UNKNOWN = -1;
    
    const ERROR_BASE = 196608;
    
    const LAS_ERROR_APP_EINVAL = 131094;
    
    const LAS_ERROR_OAUTH_ECLIENT_NAME = 196609;
    
    const LAS_ERROR_OAUTH_ECLIENT_TYPE = 196610;
    
    const LAS_ERROR_OAUTH_ERETURN_URI = 196611;
    
    const LAS_ERROR_OAUTH_EKEYPAIR = 196612;
    
    const LAS_ERROR_OAUTH_EINVAL  = 196613;
    
    
    /**
     *
     * DbExcpetion constructor
     *
     * @version 1.0
     * @since  Version 1.0
     * @param number $code Error code for application
     * @param string $previous Previous Exception
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($code = 0, $previous = null)
    {
        $message = $this->getErrorMessage($code);
        parent::__construct($message, $code, $previous);
    }
    
    /**
     *
     * Convert the Exception to string
     *
     * @version 1.0
     * @since Version 1.0
     * (non-PHPdoc)
     * @see Exception::__toString()
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __toString()
    {
        return __CLASS__
        . ": [{$this->code}]:{$this->message}: [{$this->getDevCode()}]: [{$this
        ->getDevMessage()}]\n";
    }
    
    /**
     *
     * Get the error message based on error code
     *
     * @version 1.0
     * @since  Version 1.0
     * @param integer $code Error code
     * @return string Error message
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private function getErrorMessage($code)
    {
        global $LAS_CFG;
        switch ($code) {
            case self::ERROR_BASE:
                return 'Oauth unknown error';
            default:
                return $LAS_CFG->ErrorMsg[$code];
        }
        return "Oauth unknown error";
    }
    
    /**
     *
     * Get previous error
     *
     * Used by tracking the old previous error trigger the exception if any
     * @version 1.0
     * @since  Version 1.0
     * @return boolean
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function hasPrevious()
    {
        $previous = $this->getPrevious();
        if (!empty($previous)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     *
     * Get the previous error code
     *
     * @version 1.0
     * @since  Version 1.0
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getDevCode()
    {
        if ($this->hasPrevious()) {
            return $this->getPrevious()->getCode();
        }
        return '';
    }
    
    /**
     *
     * Get previous error message
     *
     * @version 1.0
     * @since  Version 1.0
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getDevMessage()
    {
        if ($this->hasPrevious()) {
            return $this->getPrevious()->getMessage();
        }
        return '';
    }
}


/* ===============================================================
   End of OauthException.php
   =============================================================== */
?>