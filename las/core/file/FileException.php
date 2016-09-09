<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : FileException.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : File exception
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6617 $)
 * CREATED     : 02-Oct-2015
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-02-02 15:57:26 +0800 (Mon, 02 Feb 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
 @(#) FileException.php              1.0 			02-Oct-2015
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
 Begin of FileException.php
 =============================================================== */
namespace Las\Core\File;

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
   Class definition
   --------------------------------------------------------------- */

/**
 *
 * File related exception
 *
 * The class is an extension of Exception
 *
 * @since  Version 0.9
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class FileException extends \Exception
{
	/**
	 * The file name related to this error
	 * @var string
	 * @version 1.0
	 * @since Version 1.0
	 */
	private $_file= '';

	/**
	 * File no error
	 * @var integer
	 * @version 1.0
	 * @since Version 1.0
	 */
	const SUCCESS = 0;

	/**
	 * Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.
	 * @var unknown
	 * @version 1.0
	 * @since Version 1.0
	 */
	const UPLOAD_ERR_INI_SIZE = 1;
	
	/**
	 * Value: 2; The uploaded file exceeds the MAX_FILE_SIZE
	 * directive that was specified in the HTML form.
	 * @var unknown
	 * @version 1.0
	 * @since Version 1.0
	 */
	const UPLOAD_ERR_FORM_SIZE = 2;
	
	/**
	 * Value: 3; The uploaded file was only partially uploaded.
	 * @var unknown
	 * @version 1.0
	 * @since Version 1.0
	 */
	const UPLOAD_ERR_PARTIAL = 3;
	
	/**
	 * Value: 4; No file was uploaded.
	 * @var unknown
	 * @version 1.0
	 * @since Version 1.0
	 */
	const UPLOAD_ERR_NO_FILE = 4;
	
	/**
	 * Value: 6; Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.
	 * @var integer
	 * @version 1.0
	 * @since Version 1.0
	 */
	const UPLOAD_ERR_NO_TMP_DIR = 6;
	
	/**
	 * Value: 7; Failed to write file to disk. Introduced in PHP 5.1.0.
	 * @var unknown
	 * @version 1.0
	 * @since Version 1.0
	 */
	const UPLOAD_ERR_CANT_WRITE = 7;
	
	/**
	 * Value: 8; A PHP extension stopped the file upload.
	 * @var unknown
	 * @version 1.0
	 * @since Version 1.0
	 */
	const UPLOAD_ERR_EXTENSION = 8;
	
	/**
	 * Value: 9; The uploaded file already exist
	 * @var unknown
	 * @version 1.0
	 * @since Version 1.0
	 */
	const FILE_ALREADY_EXISTS = 9;

	/**
	 * Value: 10; The file does not exist
	 * @var unknown
	 * @version 1.0
	 * @since Version 1.0
	 */
	const FILE_NOT_FOUND = 10;

	/**
	 * Value: 11; No permission to access the file / directory
	 * @var unknown
	 * @version 1.0
	 * @since Version 1.0
	 */
	const PERMISSION_DENIED = 11;

	
	/**
	 *
	 * FileException constructor
	 *
	 * @version 1.0
	 * @since  Version 1.0
	 * @param  number  $code  Error code for application
	 * @param  string  $previous  Previous Exception
	 * @param  string  $file  The file name related to this error
	 * @see
	 * @author      Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function __construct($code = 0, $previous = null, $file='')
	{
		$message = $this->getErrorMessage($code);
		$this->_file = $file;
		parent::__construct($message, $code, $previous);
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
		switch ($code) {
			case self::SUCCESS:
				return 'File upload successfully';
			case self::UPLOAD_ERR_INI_SIZE:
				return 'PHP: The uploaded file exceeds the upload_max_filesize directive in php.ini';
			case  self::UPLOAD_ERR_FORM_SIZE:
				return 'PHP: The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
			case self:: UPLOAD_ERR_PARTIAL:
				return 'PHP:The uploaded file was only partially uploaded.';
			case self::UPLOAD_ERR_NO_FILE:
				return 'PHP:No file was uploaded.';
			case self::UPLOAD_ERR_NO_TMP_DIR:
				return 'PHP: Missing a temporary folder.';
			case self::UPLOAD_ERR_CANT_WRITE:
				return 'PHP: Failed to write file to disk';
			case self::UPLOAD_ERR_EXTENSION:
				return 'PHP:A PHP extension stopped the file upload';
			case self::FILE_ALREADY_EXISTS:
				return 'The uploaded file already exist';
			case self::FILE_NOT_FOUND:
				return 'The file does not exist';
			case self::PERMISSION_DENIED:
				return 'No permission to access the file / directory';
		}
		return "File unknown error";
	}

}

/* ===============================================================
 End of FileException.php
 =============================================================== */
?>