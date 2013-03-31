{include file=$r->html_header}

<div id="header">
<h1><a href="{$r->makeUrl('/home')}" class="noBg"><img id='logo' src='{$r->asset('images/logo.png')}' alt='logo' /></a></h1>
{insert name="userInfo"}
{insert name="navlist"}
</div>

<h2>Hello World!</h2>

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean vel semper
tellus. Suspendisse potenti. Etiam lobortis turpis sed sapien tempor blandit. Ut
convallis sapien ullamcorper est aliquet posuere. Donec auctor diam eu neque
fermentum vestibulum. Suspendisse consequat nisi vitae turpis elementum suscipit
quis sed libero. Proin faucibus dignissim dignissim. Curabitur et lacus nisl,
nec bibendum elit. Proin euismod viverra sem non eleifend. Pellentesque lobortis
risus ac lacus placerat sed viverra magna malesuada. Cum sociis natoque
penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis rhoncus
vehicula ultrices. Quisque non mauris neque, eu molestie erat. Morbi a urna sed
nisi eleifend fringilla sed eu massa. Fusce volutpat sem vel velit viverra vel
tempus ipsum pretium. Maecenas semper aliquet est, eget pretium felis congue et.
Donec nisl nunc, tempus sed egestas a, elementum nec orci.</p>

{include file=$r->html_footer}