<?php

include "inc/init.php";
include "lib/pagination.class.php";

$plugins->run_hook("index_top");

$dir  = (int)$_GET['dir'];

if ($dir) {
	$downloads_menu = $db->get_row("SELECT `name`,`path`,`description` FROM `" . MAI_PREFIX . "files` WHERE `id` = '$dir'");

	if ($downloads_menu->name != '')
		$lang->downloads_menu = $downloads_menu->name;
	if (!$downloads_menu->description) {
		$dds = "";
	} else {
		$dds = "<div class='description'>$downloads_menu->description</div>";
	}

	foreach (explode('/', substr($downloads_menu->path, 7)) as $dr) {
		$_dr .= "/" . $dr;
		$id = $db->get_row("SELECT `id`,`name` FROM `" . MAI_PREFIX . "files` WHERE `path` = '/files" . $db->escape($_dr) . "'");
		$links[] = " » " . "&nbsp;<a href='$set->url/data/" . $id->id . "/" . mai_converturl($id->name) . "/'>" . htmlentities($id->name) . "</a>";
	}
	$title = $id->name;
} else {
	$add = "<style>.pagination{display:none}</style>";
	$title = $lang->Welcome;

	// updates
	$updates = "<div class='list-group-item fs-5 fw-bold active'>$lang->updates</div>";
	$up_data = $db->select("SELECT * FROM `" . MAI_PREFIX . "updates` ORDER BY `id` DESC LIMIT 0,8");

	$plugins->run_hook("index_updates");
	if ($up_data) {
		foreach ($up_data as $udata) {

			$t = tsince($udata->time, $lang->time_v);
			$updates .= sprintf("<div class='list-group-item'>$udata->text</div>");
		}
		$updates .= "<div class='list-group-item bg-secondary-subtle text-end'><a href='$set->url/updates/2/'>[More Updates..]</a></div>";
	} else
		$updates .= "<div class='list-group-item bg-danger-subtle'>" . $lang->no_data . "</div>";
}

if (is_admin()) {
	$_admin = " <a href='$set->url/admincp/actions.php?act=edit&id=%1\$s'>$lang->edit</a> |
	<a href='$set->url/admincp/actions.php?act=delete&id=%1\$s'>$lang->delete</a>";
	$_admin2 = "<div class='list-group-item text-end'><a class='btn btn-dark text-white fw-normal' href='$set->url/admincp/actions.php?act=add&id=$dir'>$lang->add_folder</a></div>";

	$plugins->run_hook("index_admin");
}


$where_text = "`indir` = '$dir'";

if (!empty($_GET["search"])) {
	$s_f = filter_var($_GET["search"], FILTER_SANITIZE_STRING); //sanitize to avoid SQL error(Serious vulnerability)
	$search_words = explode(" ", $s_f);
	foreach ($search_words as $search_word) {
		$where[] = "`name` LIKE '%$search_word%'";
		$where2[] = "`description` LIKE '%$search_word%'";
	}
	$where_text = "(" . implode(" AND ", $where) . ") OR (" . implode(" AND ", $where2) . ") AND `size` > 0";
	$search_text = htmlentities($_GET["search"], ENT_QUOTES);
	$links[] = " » " . $lang->search;
} elseif (!$dir)
	$links = ' ';

$plugins->run_hook("index_search");

$total_results = $db->count("SELECT `id` FROM `" . MAI_PREFIX . "files` WHERE $where_text");
if ($total_results > 0) {

	// pagination
	$perpage = $_SESSION['perp'] ? (int)$_SESSION['perp'] : $set->perpage;
	$page = (int)$_GET['page'] == 0 ? 1 : (int)$_GET['page'];
	if ($page > ceil($total_results / $perpage)) $page = ceil($total_results / $perpage);
	$start = ($page - 1) * $perpage;

	$s_pages = new pag($total_results, $page, $perpage);

	$show_pages = "<span class='page-link text-dark' href='#'>$lang->pages : </span>" . $s_pages->pages;

	// order by
	if ($_GET['sort'])
		$_SESSION['sort'] = (int)$_GET['sort'];
	if ($_SESSION['sort'] === null) $_SESSION['sort'] = 6;

	switch ($_SESSION['sort']) {
		case 1:
			$order = "`time` ASC";
			$dateasc = " selected='1'";
			break;
		case 2:
			$order = "`name` DESC";
			$namedesc = " selected='1'";
			break;
		case 3:
			$order = "`name` ASC";
			$nameasc = " selected='1'";
			break;
		case 4:
			$order = "`size` DESC";
			$sizedesc = " selected='1'";
			break;
		case 5:
			$order = "`size` ASC";
			$sizeasc = " selected='1'";
			break;
		default:
			$order = "`time` DESC";
			$datedesc = " selected='1'";
	}

	$show_order = "$lang->sort: 
		<a href='?sort=6'>$lang->datedesc</a> | 
		<a href='?sort=1'>$lang->dateasc</a> |
		<a href='?sort=2'>$lang->namedesc</a> |
		<a href='?sort=3'>$lang->nameasc</a>
		";

	$plugins->run_hook("index_order");

	$data = $db->select("SELECT * FROM `" . MAI_PREFIX . "files` WHERE $where_text ORDER BY `isdir` DESC, $order LIMIT $start,$perpage");

	foreach ($data as $d) {
		if ($d->time > (time() - 60 * 60 * 24))
			$new_text = "<span class='badge bg-danger'>NEW</span>";
		else
			$new_text = '';

		// Folder
		if (is_dir("." . $d->path)) {

			if ($d->isdir == 0) {
				$db->query("UPDATE `" . MAI_PREFIX . "files` SET `isdir` = '1' WHERE `id` = '$d->id'");
			}

			$count = $db->count("SELECT `id` FROM `" . MAI_PREFIX . "files` WHERE `path` LIKE '" . $d->path . "%' AND `isdir` = '0'");

			$plugins->run_hook("index_folders");
			if (!$d->icon) {
				$f_icon = "$set->url/tpl/style/png/folder.png";
			} else {
				$f_icon = $d->icon;
			}
			$folders .= "<div class='list-group-item'><img class='f_icon' src='$f_icon' alt='&#187;'/>
			<a href='$set->url/data/$d->id/" . mai_converturl($d->name) . "/'>$d->name [$count]</a>" . sprintf($_admin, $d->id) . " </div>";

			$plugins->run_hook("index_folders_end");
		} else {
			$plugins->run_hook("index_files_top");

			$files .= "<div class='list-group-item'>";

			// icon
			if ($d->icon == '') {
				$ext = (object)pathinfo($d->path);
				$ext->extension = strtolower($ext->extension);

				if (in_array($ext->extension, array('png', 'jpg', 'jpeg', 'gif', 'jar'))) {
					if ($ext->extension == 'jar')
						$icon = "/icon.php?s=" . base64_encode($d->path);
					else
						$icon = "/thumb.php?w=45&src=" . base64_encode($d->path);
				} else {
					$all_icons = str_replace(".png", "", array_map("basename", glob(MAI_TPL . "style/png/*.png")));
					if (!in_array($ext->extension, $all_icons))
						$icon = "/" . MAI_TPL . "style/png/file.png";
					else
						$icon = "/" . MAI_TPL . "style/png/$ext->extension.png";
				}
			} else {
				$icon = "/thumb.php?ext&w=45&src=" . urlencode($d->icon);
			}

			// Watermak
			$plugins->run_hook("index_files");
			if (is_admin()) {
				if ($ext->extension == 'mp4' || $ext->extension == '3gp' || $ext->extension == 'avi' || $ext->extension == 'mpg' || $ext->extension == 'flv') {
					if ($d->watermark == "0") {
						$w_mark = "| <a href='$set->url/admincp/watermark.php?video=$d->id'>Watermark</a> | ";
					} else {
						$w_mark = " | <b style='color:green'>Watermarked</b> | ";
					}
				} else {
					$w_mark = "";
				}
			}

			// FIles
			$files .= "<div class='d-flex align-items-start'>";
			$files .= "<img src='$set->url" . $icon . "' width='45' height='45'>";
			$files .= "<div class='ms-3'><a href='$set->url/data/file/$d->id/" . mai_converturl($d->name) . "'>" . $d->name . "</a> $new_text<br/><small class='text-dark'>" . convert($d->size) . " | $d->views Hits $w_mark" . sprintf($_admin, $d->id) . "</small></div></div></div>";

			$plugins->run_hook("index_files_end");
		}
	}
} else {
	$files .= "<div class='list-group-item bg-danger-subtle'>" . $lang->no_data . "</div>";
}


// if the admin message is blank don't display the admin name
if (trim($set->sinfo->main_msg) == "")
	$lang->admin = null;
else {
	$lang->admin .= ":";
	$set->sinfo->main_msg .= "<br/><br/>";
}
include "header.php";
$tpl->grab('index.tpl', 'index');
$tpl->assign('MAI_TPL', $set->url . "/" . MAI_TPL);
$tpl->assign('url', $set->url);
$tpl->assign('admin', $lang->admin);
$tpl->assign('downloads_menu', $lang->downloads_menu);
$tpl->assign('description', $dds);
$tpl->assign('main_msg', $set->sinfo->main_msg);
$tpl->assign('updates', $updates);
$tpl->assign('files', $files);
$tpl->assign('folders', $folders);
$tpl->assign('extra', $lang->extra);
$tpl->assign('terms_of_service', $lang->terms_of_service);
$tpl->assign('settings', $lang->settings);
$tpl->assign('show_pages', $show_pages);
$tpl->assign('search', $lang->search);
$tpl->assign('search_placeholder', $lang->search_placeholder);
$tpl->assign('search_text', $search_text);
$tpl->assign('request', $lang->request);
$tpl->assign('sort_remove', $add);
$tpl->assign('show_order', $show_order);
$tpl->assign('_admin2', $_admin2);
$tpl->display();

$plugins->run_hook("index_end");

include "footer.php";
