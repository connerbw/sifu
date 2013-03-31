{* ### HEADER ### *}

{capture name=header}

  {$r->jQueryInit()}
  {$r->jQueryValidatorInit()}

  <script type="text/javascript">
  // <![CDATA[
  $(function(){
      $("#loginForm").validate();
  });
  // ]]>
  </script>


  <!-- CSS Overrides -->
  <style type="text/css">
  table, td { border: none; }
  </style>

{/capture}

{* ### HTML ### *}

{include file=$r->html_header}

{if $r->bool.form_error}
  <div id="form_error">
    <h2>{$r->gtext('form_error')}</h2>
    <ul>
    {foreach from=$r->arr.errors item=foo}
      <li>{$foo}</li>
    {foreachelse}
      <li>{$r->gtext('unknown_error')}</li>
    {/foreach}
    <ul>
  </div>
{/if}


<table width="100%">
<tr><td style="vertical-align:top;">

<form action="{$r->text.form_url}" name="loginForm" id="loginForm" method="post" accept-charset="utf-8" >

  <p>
  <label class="desc" for="nickname">{$r->gtext('nickname')}:</label>
  <input type="text" name="nickname" id="nickname" value="{$nickname}" class="required" /><br />
  </p>

  <p>
  <label class="desc" for="nickname">{$r->gtext('password')}:</label>
  <input type="password" name="password" id="password" class="required" /><br />
  </p>

  <p>
  <label class="desc">&nbsp;</label>
  <input type="button" class="button" value="{$r->gtext('cancel')}" onclick="document.location='{insert name="previousURL"}';" />
  <input type="submit" class="button" value="{$r->gtext('submit')}" />
  </p>

</form>

</td><td style="vertical-align:top;text-align:right;">
<img id='logo' src='{$r->asset('images/logo.png')}' alt='logo' />
</td></tr>
</table>

{include file=$r->html_footer}