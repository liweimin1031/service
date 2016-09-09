<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : las_excel.php
 * AUTHOR      : Patrick C. K. Wu
 * SYNOPSIS    :
 * DESCRIPTION : LAS Excel library
 * SEE ALSO    :
 * VERSION     : 1.1 ($Revision: 6969 $)
 * CREATED     : 20-JUN-2013
 * LASTUPDATES : $Author: patrickw $ on $Date: 2015-03-18 15:53:09 +0800 (Wed, 18 Mar 2015) $
 * UPDATES     : 
 * NOTES       : It requires PHP Excel 1.7.6 or above
 */
/* ---------------------------------------------------------------
   @(#)las_excel.php           1.0 20-JUN-2013
                                1.1 18-MAR-2015
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
   Begin of las_excel.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
require_once(dirname(__FILE__) . '/../../../inc.php');
require_once($LAS_CFG->lib_root . '/PHPExcel/Classes/PHPExcel/IOFactory.php');


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
 * This function get the row entries from <code>file</code> according to the
 * <code>tokens</code> provided
 *
 * @since       Version 1.0.00
 * @param       path            The path to the file
 * @param       sheetname       The sheet name to be checked.  If it is set
                                to <code>null</code>, the first sheet will be
                                used.
 * @param       tokens          The key-value pair.  The key should be the
                                column name and value is the entry key wanted.
                                For example, if key-value is "Email" => "email"
                                Value under columne "Email" will be mapped to
                                $entry->email.
 * @return      Returns array of entries on success; otherwise, returns
                <code>false</code>
 * @see
 */
/*
 * @author      Patrick C. K. Wu
 * @testing
 * @warnings
 * @updates     
 */
function las_excel_getentries($file, $sheetname, $tokens) {
    $result = false;

    if ( !file_exists($file) ) {
        return(false);
    }
    try {
        $excel = PHPExcel_IOFactory::load($file);
        if ( $excel ) {
            if ( $sheetname ) {
                $sheet = $excel->getSheetByName($sheetname);
            }
            else {
                $excel->setActiveSheetIndex(0);
                $sheet = $excel->getActiveSheet();
            }
            if ( $sheet ) {
                $cols = $sheet->getColumnDimensions();
                $headings = false;
                $colnames = array_keys($tokens);
                if ( $cols ) {
                    foreach ( $cols as $col ) {
                        $cellIdx = $col->getColumnIndex() . "1";
                        $cell = $sheet->getCell($cellIdx);
                        if ( $cell && in_array($cell->getValue(), $colnames) ) {
                            $heading = new stdClass;
                            $heading->index = $col->getColumnIndex();
                            $heading->key = $cell->getValue();
                            $heading->name = $tokens[$heading->key];
                            $headings[] = $heading;
                        }
                    }
                }
                if ( is_array($headings) ) {
                    $iRows = $sheet->getHighestRow();
                    for ( $i=2; $i<=$iRows; $i++ ) {
                        $bValid = false;
                        $entry = new stdClass;
                        foreach ( $headings as $heading ) {
                            $cellIdx = $heading->index . "$i";
                            $cell = $sheet->getCell($cellIdx);
                            if ( $cell ) {
                                $key = $heading->name;
                                $entry->$key = $cell->getValue();
                                if ( !empty($entry->$key) ) {
                                    $bValid = true;
                                }
                            }
                        }
                        if ( $bValid ) {
                            $result[] = $entry;
                        }
                    }
                }
            }
        }
    }
    catch (Exception $e) {
    }

    return($result);
}

/**
 * This function get the row entries from <code>file</code> according to the
 * <code>tokens</code> provided
 *
 * @since       Version 1.0.00
 * @param       path            The path to the file
 * @param       sheetname       The sheet name to be checked.  If it is set
                                to <code>null</code>, the first sheet will be
                                used.
 * @param       tokens          The key-value pair.  The key should be the
                                column name and value is the entry key wanted.
                                For example, if key-value is "Email" => "email"
                                Value under columne "Email" will be mapped to
                                $entry->email.
 * @return      Returns array of entries on success; otherwise, returns
                <code>false</code>
 * @see
 */
/*
 * @author      Patrick C. K. Wu
 * @testing
 * @warnings
 * @updates
 */
function las_excel_getsheets($file, $tokens) {
    $result = false;

    if ( !file_exists($file) ) {
        return(false);
    }
    try {
        $excel = PHPExcel_IOFactory::load($file);
        if ( $excel ) {
            $names = $excel->getSheetNames();
            foreach ( $names as $sheetname ) {
                $sheet = $excel->getSheetByName($sheetname);
                if ( $sheet ) {
                    $timetable = new stdClass;
                    $timetable->name = $sheetname;
                    $timetable->entries = false;

                    $cols = $sheet->getColumnDimensions();
                    $headings = false;
                    $colnames = array_keys($tokens);
                    if ( $cols ) {
                        foreach ( $cols as $col ) {
                            $cellIdx = $col->getColumnIndex() . "1";
                            $cell = $sheet->getCell($cellIdx);
                            if (
                                $cell && in_array($cell->getValue(), $colnames)
                            ) {
                                $heading = new stdClass;
                                $heading->index = $col->getColumnIndex();
                                $heading->key = $cell->getValue();
                                $heading->name = $tokens[$heading->key];
                                $headings[] = $heading;
                            }
                        }
                    }
                    if ( is_array($headings) ) {
                        $iRows = $sheet->getHighestRow();
                        for ( $i=2; $i<=$iRows; $i++ ) {
                            $entry = new stdClass;
                            foreach ( $headings as $heading ) {
                                $cellIdx = $heading->index . "$i";
                                $cell = $sheet->getCell($cellIdx);
                                if ( $cell ) {
                                    $key = $heading->name;
                                    $entry->$key = $cell->getValue();
                                }
                            }
                            $timetable->entries[] = $entry;
                        }
                    }
                    $result[] = $timetable;
                }
            }
        }
    }
    catch (Exception $e) {
    }

    return($result);
}


/*
// Test for clms_excel_getentries
$tokens = array(
    "Username" => "username",
    "Student ID" => "suid",
    "Surname" => "lastname_en",
    "Firstname" => "firstname_en",
    "姓" => "lastname_tw",
    "名" => "firstname_tw",
    "Password" => "password",
    "E-mail Address" => "email"
);

//$result = clms_excel_getentries("students.xls", null, $tokens);
$result = clms_excel_getentries("students.xls", "Students", $tokens);
$strJSON = json_encode($result);
echo "$strJSON<br />\n";
*/


/* ===============================================================
   End of las_excel.php
   =============================================================== */
?>
