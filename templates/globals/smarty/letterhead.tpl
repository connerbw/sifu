<div class="hideforprint" style='text-align:right;'>
[ <a href="javascript:window.print();">{$r->gtext('print')}</a>, <a href="{insert name="previousURL"}">{$r->gtext('back')}</a> ] <br />
</div>
<div class="clearboth"></div>

<table class="letterhead"><tr><td style="width:1%;">
<img id='logo' src='{$r->asset('images/logo.png')}' alt='logo' />
</td><td>
{if $letterhead}{$letterhead|trim|nl2br}{else}{#letterhead#|trim|nl2br}{/if}
</td></tr></table>