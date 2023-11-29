<?php
include('inc/init.php');
include('lib/pagination.class.php');

$total_results = $db->count("SELECT `id` FROM `" . MAI_PREFIX . "updates`");
if ($total_results > 0) {
	$updates = "<div class='list-group-item fs-5 fw-bold active'>$lang->more_updates</div>";
	// pagination
	$perpage = 10;
	$page = (int)$_GET['page'] == 0 ? 1 : (int)$_GET['page'];
	if ($page > ceil($total_results / $perpage)) $page = ceil($total_results / $perpage);
	$start = ($page - 1) * $perpage;

	$links[] = " » " . " $lang->more_updates / Page: $page";
	$s_pages = new pag($total_results, $page, $perpage);

	$show_pages = "<span class='page-link text-dark' href='#'>$lang->pages : </span>" . $s_pages->pages;
	$show_pages =  str_replace('?page=', '' . $set->url . '/updates/', $show_pages);
	$show_pages = str_replace("'>", "/'>", $show_pages);
	$data = $db->select("SELECT * FROM `" . MAI_PREFIX . "updates` ORDER BY time DESC LIMIT $start,$perpage");

	foreach ($data as $d) {
		$updates .= "<div class='list-group-item'>$d->text</div>";
	}
}
include "header.php";
$tpl->grab('updates.tpl', 'updates');
$tpl->assign('MAI_TPL', $set->url . "/" . MAI_TPL);
$tpl->assign('url', $set->url);
$tpl->assign('updates', $updates);
$tpl->assign('more_updates', $more_updates);
$tpl->assign('show_pages', $show_pages);
$tpl->display();

include "footer.php";
