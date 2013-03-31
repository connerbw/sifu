{*
// -----------------------------------------------------------------------------
// HEADER
// -----------------------------------------------------------------------------
*}

{capture name=header}

  <script type="text/javascript">
  // <![CDATA[

  // ]]>
  </script>

{/capture}

{*
// -----------------------------------------------------------------------------
// HTML
// -----------------------------------------------------------------------------
*}

{include file=$r->html_header}
{include file=$r->letterhead}

<h1>{$r->gtext('user')}</h1>

<table style="white-space:nowrap;"><tr>

<!-- Nickname -->
<td width="1%">{$r->gtext('nickname')}:</td>
<td>{$nickname}</td>
</tr><tr>

<!-- Email -->
<td>{$r->gtext('email')}:</td>
<td>{if $email}{mailto address=$email encode=javascript}{/if}</td>
</tr><tr>

<!-- Name -->
<td>{$r->gtext('name')}:</td>
<td>{$given_name} {$family_name}</td>
</tr><tr>

<!-- Address -->
<td>{$r->gtext('address')}:</td>
<td>
  {if $street_address}{$street_address}<br />{/if}
  {if $locality}{$locality}, {/if}{if $region}{$region}{/if}{if $locality || $region}<br />{/if}
  {if $postcode}{$postcode}<br />{/if}
  {if $country}
    {foreach $r->getCountriesOptions() as $key => $val}
      {if $key == $country}{$val}{/if}
    {/foreach}
  {/if}
</td>
</tr><tr>

<!-- Telephone -->
<td>{$r->gtext('tel')}:</td>
<td>{$tel}</td>
</tr><tr>

<!-- Url -->
<td>{$r->gtext('url')}:</td>
<td><a href="{$url}">{$url}</a></td>
</tr><tr>

<!-- Birthday -->
<td>{$r->gtext('dob')}:</td>
<td>{if $dob && $dob != '0000-00-00'}{$dob}{/if}</td>
</tr><tr>

<!-- Language -->
<td>{$r->gtext('language')}:</td>
<td>
  {if $language}
    {foreach $r->getLanguagesOptions() as $key => $val}
      {if $key == $language}{$val}{/if}
    {/foreach}
  {/if}
</td>
</tr><tr>

<!-- Timezone -->
<td>{$r->gtext('timezone')}:</td>
<td>{$timezone}</td>
</tr><tr>

<!-- Group -->
<td>{$r->gtext('group')}:</td>
<td>
  {if $access_groups_id}
    {foreach $r->getGroupsOptions() as $key => $val}
      {if $key == $access_groups_id}{$val}{/if}
    {/foreach}
  {/if}
</td>

</tr></table>

{include file=$r->html_footer}