{*
// -----------------------------------------------------------------------------
// HEADER
// -----------------------------------------------------------------------------
*}

{capture name=header}

  {$r->jQueryInit(true)}

  <script type="text/javascript">
  // <![CDATA[;

  function deleteWarning(name, id) {
    var res = confirm("{$r->gtext('warning')}!\n\n{$r->gtext('alert_delete_1')|escape:'javascript'} \n\n{$r->gtext('alert_delete_2')|escape:'javascript'} " + name + "? \n");
    if (res) {
      location.href = "{$r->makeUrl('/admin/user/delete')}/" + id;
    }
    else {
      return;
    }
  }

  $(function(){
    {$r->jQueryHoverCode()}
  });

  // ]]>
  </script>

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

<h1>{$r->gtext('users')}</h1>

{if $r->acl('w')}<button type="button" onclick="window.location='{$r->makeUrl("/admin/user/new")}'">{$r->gtext('new_user')}</button> {/if}

{if $r->arr.list|count}

  <table id="user_table" border="1">
  <thead>
    <tr>
    <th>{$r->gtext('nickname')}</th>
    <th>{$r->gtext('group')} <em>({$r->gtext('chgrp')|lower})</em></th>
    <th>{$r->gtext('email')}</th>
    <th style="width:1%;"><span class="ui-icon ui-icon-print" title="{$r->gtext('print')}"></span></th>
    {if $r->acl('w')}<th style="width:1%;"><span class="ui-icon ui-icon-pencil" title="{$r->gtext('edit')}"></span></th>{/if}
    {if $r->acl('x')}<th style="width:1%;"><span class="ui-icon ui-icon-trash" title="{$r->gtext('delete')}"></span></th>{/if}
    </tr>
   </thead>
   <tbody>
    {foreach $r->arr.list as $val}
      <tr>
        <td>{$val.nickname}</td>
        <td>{$val.access_groups_name}</td>
        <td>{mailto address=$val.email encode=javascript}</td>
        <td><div class="ui-state-default ui-corner-all" title="{$r->gtext('print')}"><a href="{$r->makeUrl('/admin/user/imprint')}/{$val.id}"><span class="ui-icon ui-icon-print"></span></a></div></td>
        {if $r->acl('w')}<td><div class="ui-state-default ui-corner-all" title="{$r->gtext('edit')}"><a href="{$r->makeUrl('/admin/user/edit')}/{$val.id}"><span class="ui-icon ui-icon-pencil"></span></a></div></td>{/if}
        {if $r->acl('x')}{if $val.access_groups_name|lower == 'root'} {* Don't delete root users *}
          <td></td>
        {else}
          <td><div class="ui-state-default ui-corner-all" title="{$r->gtext('delete')}" onclick="deleteWarning('{$val.nickname|replace:'"':'`'|escape:'javascript'}', {$val.id});" ><span class="ui-icon ui-icon-trash"></span></div></td>
        {/if}{/if}
      </tr>
    {/foreach}
  </tbody>
  </table>
  {$r->text.pager}

{else}

  <p><br />{$r->gtext('no_users')}</p>

{/if}


{include file=$r->html_footer}