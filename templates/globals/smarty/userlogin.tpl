<div class="userinfo">
    <a href='{$r->makeUrl('/user/authenticate/login')}'>{$r->gtext('login')}</a>
    {if $r->bool.registrations}| <a href='{$r->makeUrl('/admin/user/new')}'>{$r->gtext('register')}</a>{/if}
</div>
