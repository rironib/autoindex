<?php
/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 1.0
Licence: GPL v3
*/

include "inc/init.php";
include "lib/pagination.class.php";

$plugins->run_hook("request_top");

$links[] = mai_img("arr.gif") . " $lang->request ";


if ($_POST['rq']) {
	$request_text = $_POST['rq'];
	if ($request_text[10] && !$_COOKIE['ss']) {
		$db->query("INSERT INTO `" . MAI_PREFIX . "request` SET `text`='" . $db->escape($request_text) . "'");
		$add = "<div class='alert alert-success'> $lang->req_added </div>";
		setcookie('ss', md5(1), time() + 3600 * 12);
		$plugins->run_hook("request_ins");
	} else {
		$add = "<div class='alert alert-danger'> $lang->req_limit </div>";
	}
}
if ($_POST['reply'] && is_admin()) {
	$plugins->run_hook("request_rpl");
	$db->query("UPDATE `" . MAI_PREFIX . "request` SET `reply` = '" . $db->escape($_POST['reply']) . "' WHERE `id`='" . (int)$_POST['req'] . "'");
	header("Location: ?page=$page");
}
if ($_GET['delete'] && is_admin()) {
	$plugins->run_hook("request_del");
	$db->query("DELETE FROM `" . MAI_PREFIX . "request` WHERE `id`='" . (int)$_GET['req'] . "'");
	header("Location: ?page=$page");
}

if (!is_admin())
	$where_text = "WHERE `reply` != ''";
// pagination
$total_results = $db->count("SELECT * FROM `" . MAI_PREFIX . "request` $where_text");
if ($total_results > 0) {
	$perpage = $_SESSION['perp'] ? (int)$_SESSION['perp'] : $set->perpage;
	$page = (int)$_GET['page'] == 0 ? 1 : (int)$_GET['page'];
	if ($page > ceil($total_results / $perpage)) $page = ceil($total_results / $perpage);
	$start = ($page - 1) * $perpage;
	$s_pages = new pag($total_results, $page, $perpage);

	$show_pages = "<span class='page-link text-dark' href='#'>$lang->pages : </span>" . $s_pages->pages;

	$data = $db->select("SELECT * FROM `" . MAI_PREFIX . "request` $where_text ORDER BY `id` DESC LIMIT $start,$perpage");

	if ($data) {
		$requests = "<div class='list-group mb-2'>
		<div class='list-group-item fs-5 fw-bold active'>$lang->req_last</div>";

		foreach ($data as $d) {
			$requests .= "<div class='list-group-item'>" . nl2br(htmlentities($d->text)) . "</div><div class='list-group-item bg-secondary-subtle'>";
			if ((int)$_GET['req'] == $d->id && is_admin())
				$requests .= "<form action='?page=$page' method='post'><input type='hidden' name='req' value='$d->id'><div class='input-group'><input type='text'  name='reply' class='form-control' value='" . htmlentities($d->reply, ENT_QUOTES) . "'><input class='btn btn-primary' type='submit' value='$lang->submit'></input><a href='?' class='btn btn-danger text-light' type='button'>$lang->cancel</a></div></form>";
			else
				$requests .= ($d->reply == '' ? "*" : "") . "<b> $lang->admin </b> : " . nl2br(htmlentities($d->reply)) . "" . (is_admin() ? " <a href='?" . $_SERVER['QUERY_STRING'] . "&req=$d->id'><span class='badge bg-primary fw-normal'>$lang->reply</span></a> <a href='?" . $_SERVER['QUERY_STRING'] . "&delete=1&req=$d->id'><span class='badge bg-danger fw-normal'>$lang->delete</span></a>" : "");

			$requests .= "</div></div>";
		}
	}
}

include "header.php";
$tpl->grab("request.tpl", "request");
$tpl->assign("requests", $requests);
$tpl->assign("add", $add);
$tpl->assign("show_pages", $show_pages);
$tpl->assign("lgrequest", $lang->request);
$tpl->display();

$plugins->run_hook("request_end");
include "footer.php";
