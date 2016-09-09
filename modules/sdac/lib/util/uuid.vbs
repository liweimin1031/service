'  ---------------------------------------------------------------
'  FILE NAME   : uuid.vbs
'  AUTHOR      : Patrick C. K. Wu
'  SYNOPSIS    :
'  DESCRIPTION : Script to generatel UUID
'  SEE ALSO    :
'  VERSION     : 1.0 ($Revision: 3 $)
'  CREATED     : 21-MAY-2012
'  LASTUPDATES : $Author: michellehong $ on $Date: 2013-03-15 10:54:21 +0800 (Fri, 15 Mar 2013) $
'  UPDATES     : 
'  NOTES       :
'  ---------------------------------------------------------------
'  @(#)uuid.vbs                 1.0 21-MAY-2012
'  by Patrick C. K. Wu
'
'
'  Copyright by ASTRI, Ltd., (ECE Group)
'  All rights reserved.
'
'  This software is the confidential and proprietary information
'  of ASTRI, Ltd. ("Confidential Information").  You shall not
'  disclose such Confidential Information and shall use it only
'  in accordance with the terms of the license agreement you
'  entered into with ASTRI.
'  ---------------------------------------------------------------
'
'
'  ===============================================================
'  Begin of uuid.vbs
'  ===============================================================
'
'
'  ---------------------------------------------------------------
'  Included Library
'  ---------------------------------------------------------------
'
'
'  ---------------------------------------------------------------
'  Global Variables
'  ---------------------------------------------------------------
'
'
'  ---------------------------------------------------------------
'  Constant definition
'  ---------------------------------------------------------------
'
'
'  ---------------------------------------------------------------
'  Function definition
'  ---------------------------------------------------------------
'--
'  Program entry point
'
'  @since       Version 1.0.00
'  @param       nil
'  @return      nil
'  @see
'--
'  @author      Patrick C. K. Wu
'  @testing
'  @warnings
'  @updates
'--
set obj = CreateObject("Scriptlet.TypeLib")
WScript.StdOut.WriteLine obj.GUID


'  ===============================================================
'  End of uuid.vbs
'  ===============================================================
