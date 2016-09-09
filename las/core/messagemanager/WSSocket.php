<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : WSSocket.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : WebSocket Socket library
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision$)
 * CREATED     : 04-JUN-2012
 * LASTUPDATES : $Author$ on $Date$
 * UPDATES     : 
 * NOTES       : RFC6455 - The WebSocket Protocol
 */
/* ---------------------------------------------------------------
   @(#)WSSocket.php             1.0 04-JUN-2012
   by Kary Ho


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */


/* ===============================================================
   Begin of WSSocket.php
   =============================================================== */
namespace Las\Core\MessageManager;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
use Las\Core\Util\Time;

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */
define("LAS_MSG_CHECKSUM",    0XFFFF);

/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */
/**
 * WSSocket
 *
 * @since       Version 1.0.00
 * @param       nil
 * @return      nil
 * @see
 */
/*
 * @author      Kary Ho
 * @testing
 * @warnings
 * @updates
 */
class WSSocket {
    protected   $_socket;     // socket handle

    /**
     * WSSocket constructor
     *
     * @since   Version 1.0.00
     * @param   hsocket         socket handle
     * @param   handler         WebSocket event handle
     * @return  nil
     * @see
     */
    /*
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($socket)
    {
        $this->_socket = $socket;
    }

    /**
     * Get the socket handle
     *
     * @since   Version 1.0.00
     * @param   nil
     * @return  Returns the socket handle
     * @see
     */
    /*
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function getSocket()
    {
        return ($this->_socket);
    }

    /**
     * Close socket
     *
     * @since   Version 1.0.00
     * @param   nil
     * @return  nil
     * @see
     */
    /*
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function close()
    {
        $result = fclose($this->_socket);
        
        $log = "[".Time::getCurrentDateTime(true)."][LAS][WSSocket][close]";
        $log .= ($result) ? "[OK]" : "[Error: Cannot close socket connection]";
        error_log($log);
    }

    /**
     * Read <code>n</code> bytes from socket
     *
     * @since   Version 1.0.00
     * @param   n               The number of bytes must be read
     * @return  Returns <code>n</code> bytes of data read from scoket
     * @see
     */
    /*
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    protected function readBytes($n)
    {
        $socket = $this->_socket;
        $result = '';
        $iRemain = $n;

        do {
            $block = fread($socket, $iRemain);
//             if ( $block === false || feof($socket) ) {
            if ( $block === false ) {
                return (false);
            }

            $iRemain -= strlen($block);
            $result .= $block;
        }
        while ( $iRemain > 0 );

        return ($result);
    }

    /**
     * Read WebSocket data frame 
     *
     * @since   Version 1.0.00
     * @param   nil
     * @return  Returns data frame read from scoket (in object format)
     * @see
     */
    /*
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function readFrame()
    {
    	error_log("[".Time::getCurrentDateTime(true)."][LAS][WSSocket][readFrame][Start]");
    	
        $buffer = $this->readBytes(12);

        if ( $buffer ) {
        	$frame = unpack('Nmsg_type/Nmsg_length/Nchecksum', $buffer);
        	$frame = (object) $frame;
        	
        	$hex_total = dechex((float)$frame->msg_type) + dechex((float) $frame->msg_length);
        	
        	if ( $hex_total + $frame->checksum === LAS_MSG_CHECKSUM ) {
        		$buffer = $this->readBytes($frame->msg_length);
        		
        		if ( $buffer ){
	        		$result = new \stdClass;
	        		$result->type = $frame->msg_type;
	        		$result->data = json_decode($buffer);
	        		
	        		error_log("[".Time::getCurrentDateTime(true)."][LAS][WSSocket][readFrame][OK]");
	        		return $result;
        		}
        	}
        }
        
        error_log("[".Time::getCurrentDateTime(true)."][LAS][WSSocket][readFrame][Error: Read message failed]");
        return false;
    }

    /**
     * Write socket
     *
     * @since   Version 1.0.00
     * @param   buffer          The buffer to be send
     * @return  Returns <code>true</code> on success; otherwise, returns
     *          <code>false</code>
     * @see
     */
    /*
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function write($buffer)
    {
        $result = false;
        $socket = $this->_socket;
        $iWrite = 0;

        if ( $buffer && !empty($buffer)  ) {
            for ( $iTotal = 0; $iTotal < strlen($buffer); $iTotal += $iWrite ) {
                $iWrite = fwrite($socket, substr($buffer, $iTotal));
                if ( $iWrite === false ) {
                    return ($iTotal);
                }
            }
            $result = $iTotal;
        }

        return ($result);
    }

    /**
     * Write WebSocket data <code>frame</code> to socket
     *
     * @since   Version 1.0.00
     * @param   frame           The WebSocket data frame
     * @return  Returns <code>true</code> on success; otherwise, returns
     *          <code>false</code>
     * @see
     */
    /*
     * @author      Kary Ho
     * @testing
     * @warnings
     * @updates
     */
    public function writeFrame($message, $msg_type)
    {
    	error_log("[".Time::getCurrentDateTime(true)."][LAS][WSSocket][writeFrame][Start]");
    	
    	$result = false;

        if ( (!empty($message)) && (isset($msg_type)) ) {
            $msg_length = strlen($message);
			
            $hex_total = dechex((float) $msg_length) + dechex((float) $msg_type);
            $checksum = LAS_MSG_CHECKSUM - $hex_total;
            
            $header = pack('NNN', $msg_type, $msg_length, $checksum);
            $strFrame = $header . $message;

            $result = $this->write($strFrame);
        }
        
        if ($result === false){
        	error_log("[".Time::getCurrentDateTime(true)."][LAS][WSSocket][writeFrame][Error: Write message failed]");
        }
        else{
        	error_log("[".Time::getCurrentDateTime(true)."][LAS][WSSocket][writeFrame][OK]");
        }
        
        return ($result);
    }
}

/* ===============================================================
   End of WSSocket.php
   =============================================================== */
?>
