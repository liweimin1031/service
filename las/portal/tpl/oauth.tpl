{extends file='page_layout.tpl'}
{block name=body}
  <div class="container">
    <form
      method="post" action="las/portal/lib/auth_login.php"
      class="form-signin"
    >
      <h2 class="form-signin-heading">Please sign in</h2>
      <input type="hidden" name="caller" value="{$caller}" />
      <input type="hidden" name="redirect" value="{$redirect}" />
      <input
        type="text" name="loginname"
        class="input-block-level" placeholder="User ID"
      >
      <input
        type="password" name="password" 
        class="input-block-level" placeholder="Password"
      >
      <label class="checkbox">
        <input type="checkbox" value="remember-me" > Remember me
      </label>
      <button class="btn btn-large btn-primary" type="submit">Sign in</button>
    </form>
  </div> <!-- /container -->
{/block}
