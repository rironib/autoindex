<?php
/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 1.0
Licence: GPL v3
*/

## header.php ##

$plugins->run_hook("header_top");

if (!$title)
	$title = strip_tags(end($links));

// $links should be defined in inc/init.php as an array
if (is_array($links))
	foreach ($links as $link)
		$_links .= $link . " ";
if (empty($link)) {
	$hide_links = '<style>#breadcrumb{display:none}</style>';
}

$title = $title . " - " . $set->name;
$logo = empty($set->logo) ? $set->name : "<img src='$set->logo' alt='logo'>";

$tpl->grab('header.tpl', 'header');
$tpl->assign('title', $title);
$tpl->assign('links', $_links);
$tpl->assign('hide_links', $hide_links);
$tpl->assign('logo', $logo);
$tpl->assign('url', $set->url);
$tpl->display();

$plugins->run_hook("header_end");
