<?php
/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 3.2
Licence: GPL v3
*/

$plugins->run_hook("footer_top");

$footer = "<a href='$set->url'> $lang->Home </a> | <a href='$set->url/tos.php'>$lang->TOS</a> | <a href='$set->url/admincp'>$lang->admin_panel</a>";
if ($_SESSION['adminpass'])
	$footer .= " | <a href='$set->url/logout.php'>$lang->logout </a>";

$tpl->grab('footer.tpl', 'footer');
$tpl->assign('footer', $footer);
$tpl->display();


$plugins->run_hook("footer_end");
