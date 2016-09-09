/* --------------------------------------------------------------- */
/**
 * FILE NAME   : admin.js
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Aug 20, 2015
 * LASTUPDATES : $Author: michellehong $ on $Date: 5:19:06 PM Aug 20, 2015 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) admin.js            1.0  Aug 20, 2015
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
   Begin of op_all.js
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



/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */

$(document).ready(function() {
    Las.Oauth = {
        getClientList: function (successFn){
           Las.Util.sendRequest(Las.Constant.ADMIN_AJAX+'/getAllOauthClient', null, successFn);
        },
        addClient: function(form, successFn){
            Las.Util.submitForm(Las.Constant.ADMIN_AJAX+'/addOauthClient', form, successFn);
        },
        removeClient: function(client_id, successFn){
            var params = {
                client_id: client_id  
            };
            Las.Util.sendRequest(Las.Constant.ADMIN_AJAX+'/deleteOauthClient', params, successFn);
        },
        updateClient: function(params, successFn){
            Las.Util.sendRequest(Las.Constant.ADMIN_AJAX+'/updateOauthClient', params, successFn);
        },
        getNewSecret: function(successFn){
            Las.Util.sendRequest(Las.Constant.ADMIN_AJAX+'/newOauthClientSecret', null, successFn);
        },
        getNewKeyPair: function(client_id, successFn){
            var params = {
                client_id: client_id  
            };
            Las.Util.sendRequest(Las.Constant.ADMIN_AJAX+'/newOauthKeyPair', params, successFn);
        },
        updateClientStatus: function(client_id, status, successFn){
            var data = {
                client_id : client_id,
                status : status
            }
            Las.Oauth.updateClient(data, successFn);
        }
    }
    
    var oauth_client_list = $('#las_oauth_management table').DataTable({
        "ajax": Las.Constant.ADMIN_AJAX+'/getAllOauthClient',
        "columns": [
              { "data": "client_name" },
              { "data": "client_type", render: function(data){
                  if(data == 'CREDENTIAL'){
                      return 'LAS API'
                  } else {
                      return 'LAS USER';
                  }
              }},
              { "data": "client_id" },
              { "data": "timecreated", render: function(data){
                  return Las.Util.timeStampToDateString(data, 'M d, Y h:i:s a')
              } },
              { "data": "" }
       ],
       "order": [[ 3, "desc" ]],
       "createdRow": function ( row, data, index ) {
           if(data.status == 1){
               $(row).addClass('danger');
           }
       },
       "columnDefs": [ {
           "targets": -1,
           "data": null,
           "orderable": false,
           "render": function ( data, type, row ,meta ) {
               var status_glyphicon  = row.status == 0? 'glyphicon-ban-circle': 'glyphicon-ok-circle';
               
               return '<div class="btn-group" aria-label="action button for '+meta.row+'" data-index="'+meta.row+'">'+
                      '<button class="btn btn-default" data-fn="status"><span class="glyphicon '+status_glyphicon+'"></span>&nbsp;</button>'+
                      '<button class="btn btn-default" data-fn="edit"><span class="glyphicon glyphicon-pencil"></span>&nbsp;</button>'+
                      '<button class="btn btn-default" data-fn="remove"><span class="glyphicon glyphicon-trash"></span>&nbsp;</button></div>';
           }
       }]
    });
    
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if($(e.target).attr('href') == '#oauth_client_list'){
            oauth_client_list.ajax.reload();
        }
     });
    
    $(document).on('click','#oauth_client_list button',function(e){
        var fn = $(this).attr('data-fn');
        var data = oauth_client_list.row(parseInt($(this).closest('.btn-group').attr('data-index'))).data();
        Las.Oauth.currentData = data;
        if(fn== 'status'){
            var data = {
                client_id: data.client_id,
                status : data.status == 1? 0:1
            }
            Las.Oauth.updateClient(data, function(){
                $('#las_modal_update_client').modal('hide'); 
                Las.Error.showSuccess('Update Client', 'Success', function(){
                    oauth_client_list.ajax.reload();
                });
            });
        } else if (fn== 'edit'){
            $('#oauth_update_client_name').val(data.client_name);
            $('#oauth_update_client_description').val(data.description);
            display_client_type= '';
            if(data.client_type == 'CREDENTIAL'){
                display_client_type = 'LAS API';
                $('#oauth_update_client_redirect_url').closest('.form-group').hide();
                
            } else {
                display_client_type = 'LAS USER';
                $('#oauth_update_client_redirect_url').closest('.form-group').show();
                $('#oauth_update_client_redirect_url').val(data.redirect_uri);
            }
            
            $('#oauth_update_client_type').html(display_client_type);
            $('#oauth_update_client_id').html(data.client_id);
            $('#oauth_update_client_secret').html(data.client_secret);
            if(data.public_key !== ''){
                $('#oauth_update_client_public_key').html('<pre>'+data.public_key+'</pre>');
                $('#oauth_update_client_public_key').closest('.form-group').show()
                $('#oauth_update_client_button_key').closest('.form-group').show();
            } else {
                $('#oauth_update_client_public_key').closest('.form-group').hide();
                $('#oauth_update_client_button_key').closest('.form-group').hide();
            }
            
            
            $('#las_modal_update_client').modal('show'); 
            
            
        } else if (fn == 'remove'){
            Las.Oauth.removeClient(data.client_id, function(){
                Las.Error.showSuccess('Remove Client', 'Success', function(){
                    oauth_client_list.ajax.reload();
                });
                
            })
        }
    });
    
    
      
    $(".dropdown-menu li a").click(function(){
        $('#oauth_new_client_result').empty(); //remove the previous result
        var selText = $(this).text();
        $(this).parents('.btn-group').find('.dropdown-toggle').html(selText+' <span class="caret"></span>');
        if(selText == 'LAS API'){
            $('#oauth_new_client_redirect_url').closest('.form-group').addClass('hide')
        } else if (selText == 'LAS USER'){
            $('#oauth_new_client_redirect_url').closest('.form-group').removeClass('hide')
        }
    });
    
    $("button#oauth_new_client_button").click(function(event){
        event.preventDefault();
        $('#oauth_new_client_result').empty();
        Las.Oauth.addClient('#oauth_new_client_form', function(data){
            $('<div class="form-group"><label class="col-sm-2 control-label">Client ID</label>'+
                    '<div class="col-sm-10 form-display">'+data.client_id+'</div>'+
               '</div>').appendTo('#oauth_new_client_result');
            $('<div class="form-group"><label class="col-sm-2 control-label">Client Secret</label>'+
                    '<div class="col-sm-10 form-display">'+data.client_secret+'</div>'+
               '</div>').appendTo('#oauth_new_client_result');
            
            if(data.public_key){
                $('<div class="form-group"><label class="col-sm-2 control-label">Public Key</label>'+
                                '<div class="col-sm-10"><pre>'+data.public_key+'</pre></div>'+
                           '</div>').appendTo('#oauth_new_client_result');
            }
        });
    });
    
    //Event for re-generate secret button
    $("button#oauth_update_client_button_secret").click(function(event){
        
        
        Las.Oauth.getNewSecret(function(data){
            $('#oauth_update_client_secret').html(data);
        })
    });
    //Event for re-generate key pair button
    $("button#oauth_update_client_button_key").click(function(event){
        event.preventDefault();
        var client_id = $('#oauth_update_client_id').text();
        
        Las.Oauth.getNewKeyPair(client_id, function(data){
            $('#oauth_update_client_public_key pre').html(data.public_key);
        });
    });
    
    $("button#oauth_update_client_btn_save").click(function(event){
        event.preventDefault();
        var formData = Las.Util.geFormData('#oauth_update_client_form');
        Las.Oauth.updateClient(formData, function(){
            $('#las_modal_update_client').modal('hide'); 
            Las.Error.showSuccess('Update Client', 'Success', function(){
                oauth_client_list.ajax.reload();
            });
        });
    });
});
/*
 * =============================================================== 
 * End of op_all.js
 * ===============================================================
 */