{* ### HEADER ### *}

{capture name=header}
<meta http-equiv="refresh" content="5;url={insert name="previousURL"}">
{/capture}

{* ### HTML ### *}

{include file=$r->html_header}

<p><strong>{$r->gtext('success')}</strong>
<p>{$r->gtext('success_2')}</p>
<p style="text-align:center;">[ <a href="{insert name="previousURL"}">{$r->gtext('back')}</a> ]</p>

{include file=$r->html_footer}