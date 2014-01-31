{*
// -----------------------------------------------------------------------------
// HEADER
// -----------------------------------------------------------------------------
*}

{capture name=header}

  {$r->jQueryInit(true)}
  <script type="text/javascript" src="{$r->url}/includes/symbionts/jquery-ui/development-bundle/external/jquery.cookie.js"></script>

  <script type="text/javascript">
  // <![CDATA[
  $(function(){
    $( "#tabs" ).tabs({ cookie: { expires: 1 } });
  });
  // ]]>
  </script>


  <!-- CSS Overrides -->
  <style type="text/css">
  #tabs { font: inherit; }
  #tabs a:hover { color: white; }
  ul.admin-menu { float: left; margin-right: 2em; font-size: 110%; color: gray; }
  ul.admin-menu li { padding-bottom: 0.25em; }
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

<h2>{$r->gtext('admin')}</h2>

<div id="tabs">
  <ul>
    <li><a href="#tabs-1">{$r->gtext('users')}</a></li>
    <li><a href="#tabs-2">{$r->gtext('marketing')}</a></li>
  </ul>
  <div id="tabs-1">
    <!-- Users -->
    <div class="tip">
    <p>{$r->gtext('tip_acl')}</p>
    </div>
    <ul class="admin-menu">
      <li><a href="{$r->makeUrl('/admin/user/list')}">{$r->gtext('list_users')}</a></li>
      {if $r->acl('w')}<li><a href="{$r->makeUrl('/admin/user/new')}">{$r->gtext('new_user')}</a> [+]</li>{/if}
    </ul>
    <ul class="admin-menu">
      <li><a href="{$r->makeUrl('/admin/group/list')}">{$r->gtext('list_groups')}</a></li>
      {if $r->acl('w')}<li><a href="{$r->makeUrl('/admin/group/new')}">{$r->gtext('new_group')}</a> [+]</li>{/if}
    </ul>
    <ul class="admin-menu">
      <li><a href="{$r->makeUrl('/admin/permissions/list')}">{$r->gtext('list_permissions')}</a></li>
      {if $r->acl('w')}<li><a href="{$r->makeUrl('/admin/permissions/new')}">{$r->gtext('new_permissions')}</a> [+]</li>{/if}
    </ul>
    <div class="clearboth"></div>

  </div>
  <div id="tabs-2">
    <!-- Marketing -->

    <ul class="admin-menu">
      <li><a href="{$r->makeUrl('/admin/marketing/dump')}">{$r->gtext('dump_marketing_table')}</a></li>
    </ul>
    <div class="clearboth"></div>

  </div>

</div>


{include file=$r->html_footer}