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
  $(function(){
    {if $r->bool.edit_mode  || $r->bool.dupe_mode}
    $.ajaxSetup({ async: false }); // @see: http://stackoverflow.com/questions/4764124
    {/if}
    $("#groupForm").validate();
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

<h1>{if $r->bool.edit_mode}{$r->gtext('group')}{else}{$r->gtext('new_group')}{/if}</h1>


<form action="{$r->text.form_url}" name="groupForm" id="groupForm" method="post" accept-charset="utf-8" >

  {if $id}
  <input type="hidden" name="id" value="{$id}" />
  {/if}

  <p>
  <label class="desc" for="name">{$r->gtext('name')}:</label>
  <input type="text" name="name" id="name" value="{$name}" remote="{$r->url}/modules/admin/ajax.dupeCheckGroup.php?id={$id}" class="required" maxlength="20"  {if $name|lower == 'root' || $name|lower == 'banned'}readonly="readonly"{/if} />
  </p>

  <p>
  <label class="desc">&nbsp;</label>
  <input type="button" id="cancel" class="button" value="{$r->gtext('cancel')}" onclick="document.location='{insert name="previousURL"}';" />
  <input type="submit" id="submit" class="button" value="{$r->gtext('submit')}" />
  </p>

</form>

{include file=$r->html_footer}