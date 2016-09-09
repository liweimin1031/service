<!DOCTYPE html>
<html>
<head>
  <title>{$school_cname} - {$school_ename}</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" /> 
  <link rel="shortcut icon" href="las/portal/images/favicon.ico" />
  <link rel="icon" href="las/portal/images/favicon.ico" />
  <link href="jslib/bootstrap/css/bootstrap.css" rel="stylesheet">
  <link href="theme/default/style/las.css" rel="stylesheet">
  {block name=rolehead}{/block}
</head>
<body {if $showBackground} id="clms_bg" style="background-image:url({$bg_path});background-color:{$bg_color}" {/if} >
      <!-- Docs master nav -->
	  
	    {block name= topbar}{/block}
	   
	  
	
	<div class="container">
		{block name=body}
  	    {/block}
	</div>
  
  <footer id="las_footer">
      <div class="container">
        <p class="text-muted">All Rights Reserved</p>
      </div>
  </footer>
  
  <div class="modal fade" id="las_message_box" role="dialog" aria-labelledby="las_message_box">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	        <h4 class="modal-title"></h4>
	      </div>
	      <div class="modal-body">
	          
	      </div>
	      <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <script type="text/javascript" src="jslib/jquery-1.11.3.js"></script>
  <script type="text/javascript" src="jslib/bootstrap/js/bootstrap.min.js"></script>
  {block name=userjs}{/block}
  {block name=rolejs}{/block}
  
</body>
</html>