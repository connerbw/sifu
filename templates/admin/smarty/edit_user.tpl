{*
// -----------------------------------------------------------------------------
// HEADER
// -----------------------------------------------------------------------------
*}

{capture name=header}

  {$r->jQueryInit(true)}
  {$r->jQueryValidatorInit()}

  <script type="text/javascript">
  // <![CDATA[
  $(function(){
    {if $r->bool.edit_mode  || $r->bool.dupe_mode}
    $.ajaxSetup({ async: false }); // @see: http://stackoverflow.com/questions/4764124
    {/if}
    $("#dob").datepicker({ dateFormat: 'yy-mm-dd' });
    $("#userForm").validate();

    {if $r->isMe($id)}
    $('#access_groups_id').prop('disabled', true);
    {/if}
  });
  // ]]>
  </script>

  {$r->jQueryDatepickerLocale()}

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

{if $id && $r->acl('w')}
<button style="float:right;" type="button" onclick="window.location='{$r->makeUrl("/admin/user/duplicate")}/{$id}'">{$r->gtext('clone')}</button>
{/if}

<h1>{if $r->bool.edit_mode}{$r->gtext('user')}{else}{$r->gtext('new_user')}{/if}</h1>


<form action="{$r->text.form_url}" name="userForm" id="userForm" method="post" accept-charset="utf-8" >

  {if $id}
  <input type="hidden" name="id" value="{$id}" />
  {/if}

  <p>
  <label class="desc" for="nickname">{$r->gtext('nickname')}:</label>
  <input type="text" name="nickname" id="nickname" value="{$nickname}" remote="{$r->url}/modules/admin/ajax.dupeCheckNickname.php?id={$id}" class="required" maxlength="20" {if $r->bool.edit_mode}onchange='$("#password").rules("add", { required: true });'{/if} />
  </p>

  <p>
  <label class="desc" for="email">{$r->gtext('email')}:</label>
  <input type="text" name="email" id="email" value="{$email}" remote="{$r->url}/modules/admin/ajax.dupeCheckEmail.php?id={$id}" class="required email" />
  </p>

  <p>
  <label class="desc" for="password">{$r->gtext('password')}:</label>
  <input type="password" name="password" id="password" minlength="4" {if !$r->bool.edit_mode}class="required"{/if} />
  </p>

  <p>
  <label class="desc" for="_verify_password">{$r->gtext('verify_password')}:</label>
  <input type="password" name="_verify_password" id="_verify_password" equalTo="#password" />
  </p>

  <p>
  <label class="desc" for="given_name">{$r->gtext('given_name')}:</label>
  <input type="text" name="given_name" id="given_name" value="{$given_name}" />
  </p>

  <p>
  <label class="desc" for="family_name">{$r->gtext('family_name')}:</label>
  <input type="text" name="family_name" id="family_name" value="{$family_name}" />
  </p>

  <p>
  <label class="desc" for="street_address">{$r->gtext('street_address')}:</label>
  <input type="text" name="street_address" id="street_address" value="{$street_address}" />
  </p>

  <p>
  <label class="desc" for="locality">{$r->gtext('locality')}:</label>
  <input type="text" name="locality" id="locality" value="{$locality}" />
  </p>

  <p>
  <label class="desc" for="region">{$r->gtext('region')}:</label>
  <input type="text" name="region" id="region" value="{$region}" />
  </p>

  <p>
  <label class="desc" for="region">{$r->gtext('postcode')}:</label>
  <input type="text" name="postcode" id="postcode" value="{$postcode}" />
  </p>

  <p>
  <label class="desc" for="country">{$r->gtext('country')}:</label>
  {html_options name='country' id='country' options=$r->getActiveCountriesOptions() selected=$country}
  </p>

  <p>
  <label class="desc" for="tel">{$r->gtext('tel')}:</label>
  <input type="text" name="tel" id="tel" value="{$tel}" />
  </p>

  <p>
  <label class="desc" for="url">{$r->gtext('url')}:</label>
  <input type="text" name="url" id="url" value="{$url}" class="url" />
  </p>

  <p>
  <label class="desc" for="dob">{$r->gtext('dob')}:</label>
  <input type="text" name="dob" id="dob" value="{$dob}" class="dateISO" />
  </p>

  <p>
  <label class="desc" for="language">{$r->gtext('language')}:</label>
  {html_options name='language' id='language' options=$r->getActiveLanguagesOptions() selected=$language}
  </p>

  <p>
  <label class="desc" for="timezone">{$r->gtext('timezone')}:</label>
  {html_options name='timezone' id='timezone' options=$r->getTimezonesOptions() selected=$timezone}
  </p>

  {if $r->bool.edit_mode || $r->acl('w')}
    <p>
    <label class="desc" for="access_groups_id">{$r->gtext('group')} <em>({$r->gtext('chgrp')|lower})</em>:</label>
    {html_options name='access_groups_id' id='access_groups_id' class='digits' options=$r->getGroupsOptions() selected=$access_groups_id }
    </p>
  {/if}

  {if not $r->isLoggedIn()}
    <p>
    <label class="desc" for="how_they_heard_about_us">{$r->gtext('how_they_heard_about_us')|wordwrap:30:"<br />\n"}</label>
    <input type="text" name="how_they_heard_about_us" id="how_they_heard_about_us" value="{$how_they_heard_about_us}" />
    </p>
  {/if}

  <p>
  <label class="desc">&nbsp;</label>
  <input type="button" id="cancel" class="button" value="{$r->gtext('cancel')}" onclick="document.location='{insert name="previousURL"}';" />
  <input type="submit" id="submit" class="button" value="{$r->gtext('submit')}" />
  </p>

</form>

{include file=$r->html_footer}