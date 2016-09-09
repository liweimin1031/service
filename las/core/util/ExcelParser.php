<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : ExcelParser.php
 * AUTHOR      : Yunzhao Lu
 * SYNOPSIS    :
 * DESCRIPTION : LAS Excel Library
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 1 $)
 * CREATED     : 20-Nov-2015
 * LASTUPDATES : $Author: yzlu $ on $Date: 2015-06-10 11:59:56 +0800 (Wed, 10 Jun 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)ExcelParser.php           1.0 20-Nov-2015
   by Yunzhao Lu


   Copyright by ASTRI, Ltd., (SNDS Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */


/* ===============================================================
   Begin of ExcelParser.php
   =============================================================== */
namespace Las\Core\Util;

/* ---------------------------------------------------------------
   Included header
   --------------------------------------------------------------- */
//require_once('astriquiz_util.php');
global $LAS_CFG;
require_once($LAS_CFG->lib_root . '/PHPExcel/Classes/PHPExcel/IOFactory.php');
require_once($LAS_CFG->lib_root . '/PHPExcel/Classes/PHPExcel.php');
require_once($LAS_CFG->lib_root . '/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php');
require_once($LAS_CFG->portal_root . '/lib/las_excel.php');

/**
 * Main class ExcelParser
 *
 * @since       Version 1.0.00
 * @param       nil
 * @return      nil
 * @see
 * @author      Yunzhao Lu
 * @testing
 * @warnings
 * @updates
 */
class ExcelParser {
    /**
    * This function get plain text from PHPExcel_RichText
    *
    * @since       Version 1.1.00
    * @param       $richText object
    * @return      plain text
     * @author      Yunzhao Lu
    * @testing
    * @warnings
    * @updates
    */
    public static function getPlainText($richText) {
        if(is_object($richText)) {
            return $richText->getPlainText();
        } else {
            return $richText;
        }
    }

    /**
    * This function to read xls file to object
    *
    * @since       Version 1.4.00
    * @param       $filename      xls filename
    * @return
     * @author      Yunzhao Lu
    * @testing
    * @warnings
    * @updates
    */
    public static function xls2obj($filename, $sheetname, $tokens) {
      $obj = new \stdClass;
      if (!file_exists($filename) or !is_readable ($filename)) {
        $obj->success=false;
        return $obj;
      }
      $xls = \PHPExcel_IOFactory::load($filename);
      if($sheetname) {
          $sheet = $xls->getSheetByName($sheetname);
      } else {
          $xls->setActiveSheetIndex(0);
          $sheet = $xls->getActiveSheet();
      }
      $rows = $sheet->getHighestRow();
      $cols = self::columnNumber($sheet->getHighestColumn());
      //echo $rows. ' '. $cols;
      $heading = array();
      $validCols = array();
      for ($col = 0; $col < $cols; $col++)
      {
        $cellname = $sheet->getCellByColumnAndRow($col, 1)->getValue();
        if(empty($tokens) || in_array($cellname, $tokens)) {
            $heading[]=$cellname;
            $validCols[$cellname] = $col;
        }
      }
      
      $data[] = array();
      
      for($row = 2; $row <= $rows; $row ++)
      {
        foreach($validCols as $ck=>$col)
        {
          $data[$row-2][$ck] = self::getPlainText($sheet->getCellByColumnAndRow($col,$row)->getValue());
        }
      }
      $obj->heading = $heading;
      $obj->data= $data;
      $obj->success = true;
      //print_r($obj);
      return $obj;
    }
    
    /**
    * This function returns the column number of col
    *
    * Column AB for example is really position 29
    *
    * @since       Version 1.4.00
    * @param       $col           col name
    * @return
     * @author      Yunzhao Lu
    * @testing
    * @warnings
    * @updates
    */
    public static function columnNumber($col) {
      $col = str_pad($col,2,'0',STR_PAD_LEFT);
      $i = ($col{0} == '0') ? 0 : (ord($col{0}) - 64) * 26;
      $i += ord($col{1}) - 64;
      return $i;
    }
    
}
