{*
// -----------------------------------------------------------------------------
// HEADER
// -----------------------------------------------------------------------------
*}

{capture name=header}

  {$r->jQueryInit()}

  <script type="text/javascript">
  // <![CDATA[;

  // ]]>
  </script>

  <!-- CSS Overrides -->
  <style type="text/css">
  #wrapper { overflow-x: auto; }
  </style>

{/capture}

{*
// -----------------------------------------------------------------------------
// HTML
// -----------------------------------------------------------------------------
*}

{include file=$r->html_header}

<div id="header">
<h1><a href="{$r->makeUrl('/home')}" class="noBg"><img id='logo' src='{$r->asset('images/logo.png')}' alt='logo' /></a></h1>
{insert name="userInfo"}
{insert name="navlist"}
</div>

<h1>{$r->gtext('marketing')}</h1>

<button type="button" onclick="window.location='{$r->makeUrl("/admin/marketing/export")}'">{$r->gtext('export_csv')}</button>

{if $r->arr.list|count}

  <table id="search_results" border="1">
  <thead>
    <tr>
    {foreach $r->arr.list[0] as $key => $val}
      <th>{$key}</th>
    {/foreach}
    </tr>
  </thead>
  <tbody>
    {foreach $r->arr.list as $val}
      <tr>
        {foreach $val as $key => $val2}
          {if $key == 'email'}
            <td>{mailto address=$val2 encode=javascript}</td>
          {elseif $key == 'url' || $key == 'referrer'}
            <td><a href="{$val2}" target="_blank">{$val2}</a></td>
          {else}
            <td>{$val2}</td>
          {/if}
        {/foreach}
      </tr>
    {/foreach}
  </tbody>
  </table>

{else}

  <p><br />{$r->gtext('no_users')}</p>

{/if}

<div class="clearboth"></div>


{include file=$r->html_footer}