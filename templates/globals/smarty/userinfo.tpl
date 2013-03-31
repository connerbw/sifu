<div class="userinfo">
    {if $r->bool.acl}<span id='adminLink'>[ <a href='{$r->makeUrl('/admin')}'>{$r->gtext('admin')}</a> ]</span>{/if}
    <strong>{$r->gtext('welcome')}:</strong> <a href='{$r->makeUrl('/admin/user/edit')}/{$r->text.users_id}'>{$r->text.nickname}</a> |
    <a href='{$r->makeUrl('/user/authenticate/logout')}'>{$r->gtext('logout')}</a>
</div>
