/* --------------------------------------------------------------- */
/**
 * FILE NAME   : las_global.js
 * AUTHOR      : Patrick C. K. Wu
 * SYNOPSIS    :
 * DESCRIPTION : CLMS global variable Javascript library
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision$)
 * CREATED     : 22-OCT-2013
 * LASTUPDATES : $Author$ on $Date$
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#)las_global.js           1.0 22-OCT-2013
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
   Begin of las_global.js
   =============================================================== */


/* ---------------------------------------------------------------
   Included header
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
 * Global Variables
 * --------------------------------------------------------------- */
var las_global = {
    version:            '1.0.0',
    attrs:              {}
};


/* ---------------------------------------------------------------
   Class definition
   --------------------------------------------------------------- */

/**
 * This function get the value of the global variable.  If
 * <code>value</code> is entered, the value will be assigned to the global
 * variable.
 *
 * @since       Version 1.0.00
 * @param       name            The name of the global variable
 * @param       value           The value of the global variable [optional]
 * @return      Returns the value of the global variable if
                <code>value</code> is not set; otherwise, zero is returned
                on success.
 * @see         
 * @author      Patrick C. K. Wu
 * @testing
 * @warnings
 * @updates
 */
las_global.attr = function (name, value) {
    var         result;

    if ( value == undefined ) {
        // get global variable value
        result = las_global.attrs[name];
    }
    else {
        // set global variable value
        las_global.attrs[name] = value;
        result = 0;
    }

    return(result);
}

/**
 * This function dump all available global variable(s) to <code>div_id</code>.
 * If <code>div</code> is undefined, this function dump variable(s) using
 * function <code>alert()</code>
 *
 * @since       Version 1.0.00
 * @param       div_id          The ID of div to dump the variable(s)
 * @param       name            The name of the global variable
 * @return      nil
 * @see         
 * @author      Patrick C. K. Wu
 * @testing
 * @warnings
 * @updates
 */
las_global.dump = function (div_id) {
    var         divObj;

    if ( div_id == undefined ) {
    }
    else {
        divObj = document.getElementById(div_id);
    }

    if ( divObj == undefined ) {
        for ( attr in las_global.attrs ) {
            alert(attr + ": " + las_global.attrs[attr]);
        }
    }
    else  {
        var     html = "";

        for ( attr in las_global.attrs ) {
            html += attr + ": " + las_global.attrs[attr] + "<br />";
        }
        divObj.innerHTML = html;
    }
    return;
};


/* ===============================================================
   End of las_global.js
   =============================================================== */


