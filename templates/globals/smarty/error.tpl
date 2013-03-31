{* ### HEADER ### *}

{capture name=header}
<meta http-equiv="refresh" content="5;url={insert name="previousURL"}">
{/capture}

{* ### HTML ### *}

{include file=$r->html_header}

<p><strong>{$r->gtext('error')}</strong>
<p>{$r->text.error_message}</p>
<p style="text-align:center;">[ <a href="{insert name="previousURL"}">{$r->gtext('back')}</a> ]</p>

{include file=$r->html_footer}