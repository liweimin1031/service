{extends file='page_layout_user.tpl'}
{block name=rolehead}
<link rel="stylesheet" type="text/css" href="jslib/datatable/datatables.css"/>
<link rel="stylesheet" type="text/css" href="theme/default/style/admin.css"/>
{/block}
{block name=rolemenu}
   <li>
       <a href="#oauth_management">Oauth</a>
   </li>
{/block}
{block name=body}

<div class="panel panel-primary las_panel_tabs" id="las_oauth_management">
  <div class="panel-heading">
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#oauth_client_list" data-toggle="tab">Client List</a></li>
    <li role="presentation"><a href="#oauth_new_client" data-toggle="tab">New Cient</a></li>
  </ul>
  </div>

  <!-- Tab panes -->
  <div class="panel-body">
  <div class="tab-content">
    <div class="tab-pane active fade in " id="oauth_client_list">
        <table class="display table table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Client Type</th>
                <th>Client ID</th>
                <th>Created Time</th>
                <th>Action</th>
            </tr>
        </thead>
 
        <tbody>
            
        </tbody>
    </table>
    </div> <!--  End Of Client List -->
    <div class="tab-pane fade" id="oauth_new_client">
         <form class="form-horizontal" id="oauth_new_client_form" name= "oauth_new_client_form">
			  <div class="form-group">
			    <label for="oauth_new_client_type" class="col-sm-2 control-label">Client Type</label>
			    <div class="col-sm-10">
			           <div class="btn-group" id="oauth_new_client_type" name = "client_type"> <a class="btn btn-default dropdown-toggle btn-select" data-toggle="dropdown" href="#">Choose a type <span class="caret"></span></a>
				            <ul class="dropdown-menu">
				                <li><a href="#">LAS API</a></li>
				                <li><a href="#">LAS USER</a></li>
				            </ul>
				        </div>
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="oauth_new_client_name" class="col-sm-2 control-label">Client Name</label>
			    <div class="col-sm-10">
			      <input type="text" required class="form-control" id="oauth_new_client_name" name="client_name" placeholder="Please enter a name">
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="oauth_new_client_desc" class="col-sm-2 control-label">Client Description</label>
			    <div class="col-sm-10">
			      <textarea class="form-control" id="oauth_new_client_desc" name="description" rows= 5></textarea>
			    </div>
			  </div>
			  <div class="form-group hide">
			    <label for="oauth_new_client_redirect_url" class="col-sm-2 control-label">Redirect URL</label>
			    <div class="col-sm-10">
			      <input type="url" required class="form-control"  id="oauth_new_client_redirect_url" name="redirect_uri" placeholder="http://abc.com"></input>
			    </div>
			  </div>
			  <div class="form-group">
			    <div class="col-sm-offset-2 col-sm-10">
			      <button type="submit" class="btn btn-primary" id="oauth_new_client_button">Create</button>
			    </div>
			  </div>
			</form>
            <form id="oauth_new_client_result" class="form-horizontal"> </form>
    </div><!--  End Of Oath New Client -->
  </div>
  </div>
</div>

<div class="modal fade" id="las_modal_update_client" role="dialog" aria-labelledby="las_modal_update_client">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	        <h4 class="modal-title">Update Client</h4>
	      </div>
	      <div class="modal-body">
	          <form class="form-horizontal" id="oauth_update_client_form" name= "oauth_update_client_form">
			  <div class="form-group">
			    <label for="oauth_update_client_type" class="col-sm-3 control-label">Client Type</label>
			    <div class="col-sm-9">
			       <div id= "oauth_update_client_type" class="form-display" name="client_type"></div>
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="oauth_update_client_id" class="col-sm-3 control-label">Client ID</label>
			    <div class="col-sm-9">
			           <div id= "oauth_update_client_id" class="form-display" name="client_id"></div>
			    </div>
			  </div>
			  
			  <div class="form-group">
			    <label for="oauth_update_client_name" class="col-sm-3 control-label">Client Name</label>
			    <div class="col-sm-9">
			      <input type="text" required class="form-control" id="oauth_update_client_name" name="client_name" placeholder="Please enter a name">
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="oauth_update_client_desc" class="col-sm-3 control-label">Client Description</label>
			    <div class="col-sm-9">
			      <textarea class="form-control" id="oauth_update_client_desc" name="description" rows= 5></textarea>
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="oauth_update_client_redirect_url" class="col-sm-3 control-label">Redirect URL</label>
			    <div class="col-sm-9">
			      <input type="url" required class="form-control"  id="oauth_update_client_redirect_url" name="redirect_uri" placeholder="http://abc.com"></input>
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="oauth_update_client_secret" class="col-sm-3 control-label" >Client Secret</label>
			    <div class="col-sm-9">
			      <div id= "oauth_update_client_secret" class="form-display" name="client_secret"></div>
			    </div>
			  </div>
			  <div class="form-group" style="text-align: right;">
			    <div class="col-sm-12" >
			      <button type="submit" class="btn btn-primary" id="oauth_update_client_button_secret">Re-Generate Secret</button>
			    </div>
			  </div>
			  <div class="form-group">
			    <label for="oauth_update_client_public_key" class="col-sm-3 control-label" >Public Key</label>
			    <div class="col-sm-9">
			      <div id= "oauth_update_client_public_key" class="form-display" name="public_key"></div>
			    </div>
			  </div>
			  <div class="form-group" style="text-align: right;">
			    <div class="col-sm-12" >
			      <button type="submit" class="btn btn-primary" id="oauth_update_client_button_key">Re-Generate Key</button>
			    </div>
			  </div>
			</form>
	      </div>
	      <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	          <button type="button" class="btn btn-primary" id="oauth_update_client_btn_save">Save Changes</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  
{/block}	  
{block name=rolejs}
    <script type="text/javascript" src="jslib/datatable/datatables.js"></script>
    <script type="text/javascript" src="las/portal/js/admin.js"></script>
{/block}