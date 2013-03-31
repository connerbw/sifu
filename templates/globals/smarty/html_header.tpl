<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=9" />
    <title>{$r->title|capitalize}</title>
    <link rel='icon' type='image/png' href='{$r->asset('images/favicon.png')}' />
    {$r->stylesheets}
    {$r->scripts()}
    {$r->header}
    {if isset($smarty.capture.header)}{$smarty.capture.header}{/if}
</head>
<body>
{$r->jsConsole()}
<noscript><div id="noscript-padding"></div></noscript>
<div id="wrapper">