<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : FileManager.php
 * AUTHOR      : Kary Ho
 * SYNOPSIS    :
 * DESCRIPTION : LAS task manager
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 6353 $)
 * CREATED     : 29-SEP-2015
 * LASTUPDATES : $Author: mhshi $ on $Date: 2013-02-27 11:20:37 +0800 (Wed, 27 Feb 2013) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)FileManager.php                  1.0 				29-SEP-2015
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
   Begin of FileManager.php
   =============================================================== */
namespace Las\Core\File;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__FILE__) . '/../../../inc.php');
require_once(dirname(__FILE__) . '/../../../lib/php-webhdfs/vendor/autoload.php');

use org\apache\hadoop\WebHDFS;
use org\apache\hadoop\WebHDFS_Exception;

/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */

/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */
class FileManager
{
	private $hdfs;
	
	private $root;
	
	function __construct()
	{
		global $LAS_CFG;
		
		$host     = $LAS_CFG->hadoop_server['host'];
		$port     = $LAS_CFG->hadoop_server['port'];
		$username = $LAS_CFG->hadoop_server['username'];
		
		$this->root = $LAS_CFG->hadoop_server['root'];
		
		$this->hdfs = new WebHDFS($host, $port, $username, $host, $port, true);
	}
	
	/**
	 *
	 * Create directory to Hadoop
	 *
	 * @version    1.0
	 * @since      Version    1.0
	 * @param      string     $path   directory path
	 * @see
	 * @author     Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function createDir($path){
		try {
			$result = $this->hdfs->mkdirs($this->_addHadoopRoot($path, true));
		}
		catch (WebHDFS_Exception $e){
			$code = $this->_convertWebHDFSException($e->getCode());
			$result = new FileException($code, null, $path);
		}
		return $result;
	}

	/**
	 *
	 * Create file to Hadoop
	 *
	 * @version    1.0
	 * @since      Version    1.0
	 * @param      object     $file        file to upload, please use _FILES
	 * @param      string     $dest_path   destination file path
	 * @see
	 * @author     Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function createFiles($file, $dest_path = null)
	{
		$dest_path = $this->_addHadoopRoot($dest_path, true);

		if ($file["error"] > 0) {
			//please note that the first 0-8 error match PHP error
			$result = new FileException($file['error'], null, $file["name"]);
		}
		else {
			try {
				// create nested directories recursively
				$is_success = $this->hdfs->create($dest_path . $file['name'], $file['tmp_name']);
				if ($is_success){
					$result = $this->_removeHadoopRoot($dest_path . $file['name']);
				}
				else{
					$result = false;
				}
			}
			catch (WebHDFS_Exception $e){
				$code = $this->_convertWebHDFSException($e->getCode());
				if ($code === FileException::FILE_ALREADY_EXISTS){
					$this->deleteFile($dest_path . $file['name']);
					$result = $this->createFiles($file, $dest_path);
				}
				else{
					$result = new FileException($code, null, $file["name"]);
				}
			}
		}
		return $result;
	}
	
	/**
	 *
	 * Create file with data to Hadoop
	 *
	 * @version    1.0
	 * @since      Version    1.0
	 * @param      object     $file_name   name of file to create
	 * @param      string     $data     file content
	 * @param      string     $dest_path   destination file path
	 * @see
	 * @author     Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function createFileWithData($file_name, $data, $dest_path = null)
	{
		$dest_path = $this->_addHadoopRoot($dest_path, true);
		
		try {
			$is_success = $this->hdfs->createWithData($dest_path . $file_name, $data);
			if ($is_success){
				$result = $this->_removeHadoopRoot($dest_path . $file_name);
			}
			else{
				$result = false;
			}
		}
		catch (WebHDFS_Exception $e){
			$code = $this->_convertWebHDFSException($e->getCode());
			if ($code === FileException::FILE_ALREADY_EXISTS){
				$this->deleteFile($dest_path . $file_name);
				$result = $this->createFileWithData($file_name, $data, $dest_path);
			}
			else{
				$result = new FileException($code, null, $file_name);
			}
		}
		return $result;
	}
	
	/**
	 *
	 * Open and Read file from Hadoop
	 *
	 * @version    1.0
	 * @since      Version    1.0
	 * @param      string     $path   file path
	 * @see
	 * @author     Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function readFile($path){
		try {
			$result = $this->hdfs->open($this->_addHadoopRoot($path));
		}
		catch (WebHDFS_Exception $e){
			$code = $this->_convertWebHDFSException($e->getCode());
			$result = new FileException($code, null, $path);
		}
		return $result;
	}
	
	/**
	 *
	 * List directory in Hadoop
	 *
	 * @version    1.0
	 * @since      Version    1.0
	 * @param      string     $path   directory path
	 * @see
	 * @author     Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function listDir($path){
		try {
			$records = $this->hdfs->listFiles($this->_addHadoopRoot($path, true), $recursive = true, $includeFileMetaData = true);
			foreach($records as $record){
				$record->path = $this->_removeHadoopRoot($record->path);
				$results[] = $record;
			}
		}
		catch (WebHDFS_Exception $e){
			$code = $this->_convertWebHDFSException($e->getCode());
			$results = new FileException($code, null, $path);
		}
		return $results;
	}
	
	/**
	 *
	 * List directory in Hadoop
	 *
	 * @version    1.0
	 * @since      Version    1.0
	 * @param      string     $path   file / directory path
	 * @see
	 * @author     Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function getFileStatus($path){
		try {
			$result = $this->hdfs->getFileStatus($this->_addHadoopRoot($path));
		}
		catch (WebHDFS_Exception $e){
			$code = $this->_convertWebHDFSException($e->getCode());
			$result = new FileException($code, null, $path);
		}
		return $result;
	}

    /**
	 *
	 * Delete file from Hadoop
	 *
	 * @version    1.0
	 * @since      Version    1.0
	 * @param      string     $path   file / directory path
	 * @see
	 * @author     Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	public function deleteFile($path){
		try {
			$result = $this->hdfs->delete($this->_addHadoopRoot($path));
		}
		catch (WebHDFS_Exception $e){
			$code = $this->_convertWebHDFSException($e->getCode());
			$result = new FileException($code, null, $path);
		}
		return $result;
	}
	
	/**
	 *
	 * Convert WebHDFS exception to File exception
	 *
	 * @version    1.0
	 * @since      Version    1.0
	 * @param      int        $code   WebHDFS exception code
	 * @see
	 * @author     Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	private function _convertWebHDFSException($code){
		switch ($code) {
			case WebHDFS_Exception::FILE_ALREADY_EXISTS:
				return FileException::FILE_ALREADY_EXISTS;
			case WebHDFS_Exception::FILE_NOT_FOUND:
				return FileException::FILE_NOT_FOUND;
			case WebHDFS_Exception::PERMISSION_DENIED:
				return FileException::PERMISSION_DENIED;
		}
	}

    /**
	 *
	 * Add Hadoop root at the beginning of the path
	 *
	 * @version    1.0
	 * @since      Version    1.0
	 * @param      string     $path   file / directory path
     * @param      boolean    $isDir  tells whether the path is a directory
	 * @see
	 * @author     Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	private function _addHadoopRoot($path, $isDir = false){
		$root_length = strlen($this->root);
		
		if( (substr($path, 0, $root_length)) === $this->root){
			$fullpath = $path;
		}
		else{
			$fullpath = $this->root . $path;
		}
		
		if ($isDir){
			$temp = strlen($fullpath) - strlen("/");
			
			if ( !(($temp >= 0) && (strpos($fullpath, "/", $temp) !== false)) ){
				$fullpath .= "/"; 
			}
		}

		return $fullpath;
	}
	
	/**
	 *
	 * Remove Hadoop root from the path
	 *
	 * @version    1.0
	 * @since      Version    1.0
	 * @param      string     $path   file / directory path
	 * @param      boolean    $isDir  tells whether the path is a directory
	 * @see
	 * @author     Kary Ho
	 * @testing
	 * @warnings
	 * @updates
	 */
	private function _removeHadoopRoot($path){
		$root_length = strlen($this->root);
	
		if( (substr($path, 0, $root_length)) === $this->root){
			$fullpath = str_replace($this->root, "", $path);
		}
		else{
			$fullpath = $path;
		}
	
		return $fullpath;
	}
}
/* ===============================================================
   End of FileManager.php
   =============================================================== */
?>