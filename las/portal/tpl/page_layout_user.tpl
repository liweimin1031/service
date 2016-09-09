{extends file='page_layout.tpl'}
{block name=topbar}

<header class="navbar navbar-inverse navbar-static-top las-nav" id="top" role="banner">
<div class="container">
    <div class="navbar-header">
      <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-navbar" aria-controls="bs-navbar" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="../" class="navbar-brand">LAS</a>
    </div>
    <nav id="bs-navbar" class="collapse navbar-collapse">
	      <ul class="nav navbar-nav" id="las_top_user_menu">
	        {block name=rolemenu}{/block}
	      </ul>
	      <ul class="nav navbar-nav navbar-right">
	         <li class="dropdown">
                 <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="name">
                      {$user_cname}<b class="caret"></b>
                 </a>
                 <ul class="dropdown-menu">
                     <li>
                         <a href="{$logout_path}">Logout</a>
                     </li>
                 </ul>
              </li>
	      </ul>
	</nav>
</div>
</header>	  
 
{/block}
{block name=userjs}
  <script type="text/javascript" src="las/portal/js/las_global.js"></script>
  {include file='las_global.tpl'}
  <script type="text/javascript" src="las/portal/js/las.js"></script>
{/block}