{extends file='page_layout.tpl'}
{block name=rolehead}
<link href="theme/default/style/login.css" rel="stylesheet">
{/block}

{block name="topbar"}
<header class="navbar navbar-inverse navbar-static-top las-nav" id="top" role="banner">
<div class="container">
    <div class="navbar-header">
      <a href="../" class="navbar-brand">LAS</a>
    </div>
</div>
</header>
{/block}
{block name=body}
{if  $error ne ''}
<div id="error" class="alert alert-warning">{$error}</div>
{/if}
<div class="card card-container">
            <img id="profile-img" class="profile-img-card" src="//ssl.gstatic.com/accounts/ui/avatar_2x.png" />
            <p id="profile-name" class="profile-name-card"></p>
            <form class="form-signin" method="post" action="las/portal/lib/login.php">
                <span id="reauth-email" class="reauth-email"></span>
                <input name="loginname" id="loginname" class="form-control" placeholder="User Name" required autofocus>
                <input type="password" id="password"  name="password" class="form-control" placeholder="Password" required>
                <div id="remember" class="checkbox">
                    <label>
                        <input type="checkbox" name="staylogin" value="remember-me"> Remember me
                    </label>
                </div>
                <button class="btn btn-large btn-primary btn-block btn-signin" type="submit">Sign in</button>
            </form><!-- /form -->
        </div><!-- /card-container -->
{/block}	  
{block name=rolejs}
<script type="text/javascript" src="las/portal/js/login.js"></script>
{/block}