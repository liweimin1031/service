/* --------------------------------------------------------------- */
/**
 * FILE NAME   : las.js
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Aug 28, 2015
 * LASTUPDATES : $Author: michellehong $ on $Date: 2:38:48 PM Aug 28, 2015 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) las.js            1.0  Aug 28, 2015
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
   Begin of las.js
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */



/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */
$(document).ready(function() {
    Las = {};
    Las.Operator= {};
    Las.Constant = {
        ADMIN_AJAX: 'las/portal/lib/admin.php',
        LANG: 'lang/',
        INFORMATION: 'info',
        WARINING: 'warning',
        ERROR: 'error',
        ERROR_NETWORK: 'network',
        ERROR_SERVER: 'server'
    };

    Las.Error = {
        generateErrorMsg : function(error_type, error, title) {
            var titleMsg = '';
            switch (title) {
            case Las.Constant.INFORMATION:
                titleMsg = 'info';
                break;
            case Las.Constant.WARINING:
                titleMsg = 'warning';
                break;
            default:
                titleMsg = 'error';
            break;
            }

            switch (error_type) {
            case Las.Constant.ERROR_NETWORK:
                /*objErr = {
                        errcode : ORPLMS.Error.LAS_ERROR_ENETWORK,
                        arg : error.code
                    };
                    ORPLMS.Error.showError(titleMsg, objErr);
                    */
                break;
            case Las.Constant.ERROR_SERVER:

                var errorMsg = Las.Error.getErrorString(error.code);
                Las.Error.showError(titleMsg, errorMsg);
                break;

            default:

            }
        },
        showError: function(titleMsg, message){
            $('#las_message_box h4').html (titleMsg);
            $('#las_message_box .modal-body').html (message);
            $('#las_message_box').off().modal('show');
        },
        showSuccess: function(titleMsg, message, closeCallBack){
            $('#las_message_box h4').html (titleMsg);
            $('#las_message_box .modal-body').html (message);
            $('#las_message_box').off().modal('show');
            $('#las_message_box').on('hidden.bs.modal', function (e) {
                closeCallBack();
            });
        },
        getErrorString: function(key, arg){
            return Las.Lang.getString(key, 'error', arg);
        }
    };
    
    Las.Lang = {
        load : function(module_name, callback){
            if(typeof (Las.Lang[module_name])== 'undefined'){
                var response = $.ajax({
                    url: Las.Constant.LANG+module_name+'.json.en_utf8',
                    cache: true,
                    async: false
                }).responseText;
                Las.Lang[module_name] = $.parseJSON(response);
               
            }
        },
        getStringByLocal: function(key, module_name, arg){
            var identifier = Las.Lang[module_name][key],
            result;

            
            if (typeof identifier.argtype === 'undefined') {
                return (identifier.value);
            }
            
            else {
                if (typeof arg === 'undefined') {
                    return (identifier.value);
                    //return (undefined);
                }
                else {
                    result = identifier.value;

                    switch (identifier.argtype) {

                    case "string":
                        result = identifier.value;
                        result = result.replace("%s", arg);
                        break;
                    /*case "date":
                        result = pjs_getdate(locale, module, result, arg);
                        break;*/
                    default:
                        break;
                    }
                    return (result);
                }
            }
        },
        getString: function(key, module_name, arg){
            if(typeof (Las.Lang[module_name])== 'undefined'){
                Las.Lang.load(module_name);
            }
            
            var value = Las.Lang.getStringByLocal(key, module_name, arg);
            return value;
        }
    };
    
    


    Las.Util = {
        isFunction : function(obj) {
            return !!(obj && obj.constructor && obj.call && obj.apply);
        },
        sendRequest : function(url, params, successFn, failureFn, async, timeout) {
            var setting = {
                type : 'POST',
                url : url,
                dataType: 'json',
                cache: false,
                data : {data: params},
                success : function(data, textStatus, jqXHR) {
                    if(data && data.success){
                        if (Las.Util.isFunction(successFn)) {
                            successFn(data.data);
                        }
                    } else {
                        Las.Error.generateErrorMsg(Las.Constant.ERROR_SERVER,
                                        data.error);
                        if (Las.Util.isFunction(failureFn)) {
                            failureFn(data.data);
                        }
                    }
                },
                error : function(jqXHR, textStatus, errorThrown) {
                    var error = {
                        code : jqXHR.status
                    };
                    Las.Error.generateErrorMsg(Las.Constant.ERROR_NETWORK, error);

                }
            };
            if(typeof(async) !==undefined){
                setting.async = async;
            }

            if(typeof(timeout) !==undefined){
                setting.timeout = timeout*1000;
            }
            $.ajax(setting);
        },

        submitForm: function(url, form, successFn, failureFn, async, timeout){
            var formData = Las.Util.geFormData(form);
            Las.Util.sendRequest(url, formData, successFn, failureFn, async, timeout);
        },
        geFormData: function(form){
            if( !(form instanceof jQuery)){
                form = $(form);
            }
            var array = form.serializeArray();
            var data = {};

            jQuery.each(array, function() {
                data[this.name] = this.value || '';
            });

            $.each(form.find(".btn-group .dropdown-menu"), function(index, element){
                var root= element.closest('.btn-group');
                var name = $(root).attr('name');
                var content = $(root).find('.dropdown-toggle').text()
                data[name] = content || '';
            });
            
            $.each(form.find(".form-display"), function(index, element){
                var name = $(element).attr('name');
                var content = $(element).text()
                data[name] = content || '';
            });

            return data;
        },
        timeStampToDateString: function(timestamp, dateFormat){
            
            dateFormat = dateFormat.split("");
            
            var dt = new Date(timestamp * 1000);

            var date = dt.getDate(),
            month = dt.getMonth(),
            hours = dt.getHours(),
            minutes = dt.getMinutes(),
            seconds = dt.getSeconds();
                        
            var getMonthAbbr = function(month){
                var month_abbrs = [
                                   'Jan',
                                   'Feb',
                                   'Mar',
                                   'Apr',
                                   'May',
                                   'Jun',
                                   'Jul',
                                   'Aug',
                                   'Sep',
                                   'Oct',
                                   'Nov',
                                   'Dec'
                               ];

               return month_abbrs[month];
            };
            
            var getDayAbbr = function(day){
                var days_abbr = [
                                    'Sun',
                                    'Mon',
                                    'Tue',
                                    'Wed',
                                    'Thur',
                                    'Fri',
                                    'Sat'
                                ];
                return days_abbr[day];
            };
            
            var getMonthName = function(month){
                var month_names = [
                                    'January',
                                    'February',
                                    'March',
                                    'April',
                                    'May',
                                    'June',
                                    'July',
                                    'August',
                                    'September',
                                    'October',
                                    'November',
                                    'December'
                                ];

                return month_names[month];
            }
            var date_props = {
                d: date < 10 ? '0'+date : date,
                D: getDayAbbr(dt.getDay()),
                j: dt.getDate(),
                w: dt.getDay(),
                F: getMonthName(month),
                m: month < 10 ? '0'+(month+1) : month+1,
                M: getMonthAbbr(month),
                n: month+1,
                Y: dt.getFullYear(),
                y: dt.getFullYear()+''.substring(2,4),
                a: hours > 12 ? 'pm' : 'am',
                A: hours > 12 ? 'PM' : 'AM',
                g: hours % 12 > 0 ? hours % 12 : 12,
                G: hours > 0 ? hours : "12",
                h: hours % 12 > 0 ? hours % 12 : 12,
                H: hours,
                i: minutes < 10 ? '0' + minutes : minutes,
                s: seconds < 10 ? '0' + seconds : seconds           
            };

            // loop through format array of characters and add matching data else add the format character (:,/, etc.)
            var date_string = "";
            for(var i=0;i<dateFormat.length;i++){
                var f = dateFormat[i];
                if(f.match(/[a-zA-Z]/g)){
                    date_string += date_props[f] ? date_props[f] : '';
                } else {
                    date_string += f;
                }
            }

            return date_string;
        }
    };
});


/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */


/*
 * =============================================================== 
 * End of las.js
 * ===============================================================
 */