{*
// -----------------------------------------------------------------------------
// HEADER
// -----------------------------------------------------------------------------
*}

{capture name=header}

  {$r->jQueryInit()}
  {$r->jQueryValidatorInit()}

  <script type="text/javascript">
  // <![CDATA[

  // --------------------------------------------------------------------------
  // Functions
  // --------------------------------------------------------------------------

  function calc_chmod(no_update)
  {
    var users = new Array("_user", "_group", "_other");
    var totals = new Array("", "", ""); // Stringify

    for (var i = 0; i < users.length; i++)
    {
      var number = 0;
      var field4 = "#" + users[i] + "4";
      var field2 = "#" + users[i] + "2";
      var field1 = "#" + users[i] + "1";

      if ($(field4).is(':checked')) { number += 4; }
      if ($(field2).is(':checked')) { number += 2; }
      if ($(field1).is(':checked')) { number += 1; }

      totals[i] = totals[i] + number;
    }

     if (!no_update) $('#chmod').val(totals[0] + totals[1] + totals[2]);
  }


  function octal_change()
  {
    var val = $('#chmod').val();

    var ownerbin = parseInt(val.charAt(0)).toString(2);
    while (ownerbin.length < 3) { ownerbin = "0" + ownerbin; };  // Stringify

    var groupbin = parseInt(val.charAt(1)).toString(2);
    while (groupbin.length < 3) { groupbin = "0" + groupbin; };

    var otherbin = parseInt(val.charAt(2)).toString(2);
    while (otherbin.length < 3) { otherbin = "0" + otherbin; };

    $("#_user4").prop('checked', parseInt(ownerbin.charAt(0)) == 1 ? true : false);
    $("#_user2").prop('checked', parseInt(ownerbin.charAt(1)) == 1 ? true : false);
    $("#_user1").prop('checked', parseInt(ownerbin.charAt(2)) == 1 ? true : false);

    $("#_group4").prop('checked', parseInt(groupbin.charAt(0)) == 1 ? true : false);
    $("#_group2").prop('checked', parseInt(groupbin.charAt(1)) == 1 ? true : false);
    $("#_group1").prop('checked', parseInt(groupbin.charAt(2)) == 1 ? true : false);

    $("#_other4").prop('checked', parseInt(otherbin.charAt(0)) == 1 ? true : false);
    $("#_other2").prop('checked', parseInt(otherbin.charAt(1)) == 1 ? true : false);
    $("#_other1").prop('checked', parseInt(otherbin.charAt(2)) == 1 ? true : false);

    calc_chmod(true);
  }


  // --------------------------------------------------------------------------
  // Initializers
  // --------------------------------------------------------------------------

  $(function(){
    $("#permissionsForm").validate();
    octal_change();
  });
  // ]]>
  </script>


  <!-- CSS Overrides -->
  <style type="text/css">
  #chmod_calculator { margin-left:225px; margin-bottom:2em; width:auto;}
  #chmod_calculator th, #chmod_calculator td { text-align:center; padding-left: 2em; padding-right: 2em; }
  </style>

{/capture}

{*
// -----------------------------------------------------------------------------
// HTML
// -----------------------------------------------------------------------------
*}

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

{if $id}
<button style="float:right;" type="button" onclick="window.location='{$r->makeUrl("/admin/permissions/duplicate")}/{$id}'">{$r->gtext('clone')}</button>
{/if}

<h1>{if $r->bool.edit_mode}{$r->gtext('permissions')}{else}{$r->gtext('new_permissions')}{/if}</h1>


<form action="{$r->text.form_url}" name="permissionsForm" id="permissionsForm" method="post" accept-charset="utf-8" >

  {if $id}
  <input type="hidden" name="id" value="{$id}" />
  {/if}

  <p>
  <label class="desc" for="name">{$r->gtext('module')}:</label>
  {html_options name='module' id='module' class="required" options=$r->getAccessOptions() selected=$module}
  </p>

  <p>
  <label class="desc" for="users_id">{$r->gtext('owner')} <em>({$r->gtext('chown')|lower})</em>:</label>
  {html_options name='users_id' id='users_id' class='required' options=$r->getUsersOptions() selected=$users_id}
  </p>


  <p>
  <label class="desc" for="access_groups_id">{$r->gtext('group')} <em>({$r->gtext('chgrp')|lower})</em>:</label>
  {html_options name='access_groups_id' id='access_groups_id' class='required digits' options=$r->getGroupsOptions() selected=$access_groups_id}
  </p>

  <p>
  <label class="desc" for="chmod">{$r->gtext('permissions')} <em>({$r->gtext('chmod')|lower})</em>:</label>
  <input type="text" name="chmod" id="chmod" value="{$chmod}" class="required digits" maxlength="3" max="777" onkeyup="octal_change();" />
  </p>

  <table id="chmod_calculator" border="1">
  <thead>
    <tr>
      <th colspan="4">{$r->gtext('chmod_calculator')}</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td></td>
      <td><em>{$r->gtext('owner')}</em></td>
      <td><em>{$r->gtext('group')}</em></td>
      <td><em>{$r->gtext('other')}</em></td>
    </tr>
    <tr>
      <td style="text-align:left;"><em>{$r->gtext('read')}</em></td>
      <td><input type="checkbox" name="_user4" id="_user4" value="4" onclick="calc_chmod();" /></td>
      <td><input type="checkbox" name="_group4" id="_group4" value="4" onclick="calc_chmod();" /></td>
      <td><input type="checkbox" name="_other4" id="_other4" value="4" onclick="calc_chmod();" /></td>
    </tr>
    <tr>
      <td style="text-align:left;"><em>{$r->gtext('write')}</em></td>
      <td><input type="checkbox" name="_user2" id="_user2" value="2" onclick="calc_chmod();" /></td>
      <td><input type="checkbox" name="_group2" id="_group2" value="2" onclick="calc_chmod();" /></td>
      <td><input type="checkbox" name="_other2" id="_other2" value="2" onclick="calc_chmod();" /></td>
    </tr>
    <tr>
      <td style="text-align:left;"><em>{$r->gtext('execute')}</em></td>
      <td><input type="checkbox" name="_user1" id="_user1" value="1" onclick="calc_chmod();" /></td>
      <td><input type="checkbox" name="_group1" id="_group1" value="1" onclick="calc_chmod();" /></td>
      <td><input type="checkbox" name="_other1" id="_other1" value="1" onclick="calc_chmod();" /></td>
    </tr>
  </tbody>
  </table>

  <p>
  <label class="desc">&nbsp;</label>
  <input type="button" id="cancel" class="button" value="{$r->gtext('cancel')}" onclick="document.location='{insert name="previousURL"}';" />
  <input type="submit" id="submit" class="button" value="{$r->gtext('submit')}" />
  </p>

</form>

{include file=$r->html_footer}