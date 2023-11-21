<?php
/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 1.0
Licence: GPL v3
*/
## This is a modified version of Master Autoindex. So all source rights goes to ionutvmi ##

ob_start();

include "../inc/init.php";


$plugins->run_hook("plugin_manager_top");

if (!is_admin()) {
	ob_end_clean();
	header("Location: $set->url");
	exit;
}
$links[] = mai_img("arr.gif") . " <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif") . " $lang->plugin_manager";

$plugins->load(true); // we don't run plugins here
$act = $_GET['act'];
$page = (int)$_GET['page'] == 0 ? 1 : (int)$_GET['page'];

// activate
if ($act == 'activate') {
	$plug = $_GET['plugin'];
	$active_plugins = unserialize($set->sinfo->active_plugins);
	if (!is_array($active_plugins))
		$active_plugins = array();

	$active_plugins[] = $plug;
	$db->query("UPDATE `" . MAI_PREFIX . "settings` SET `active_plugins` = '" . serialize(array_unique($active_plugins)) . "'");

	if (is_callable($plug . "_activate")) {
		call_user_func($plug . "_activate");
	}
	ob_end_clean();
	header("Location: ?page=$page");
	exit;
}
// deactivate
if ($act == 'deactivate') {
	$plug = $_GET['plugin'];
	$active_plugins = unserialize($set->sinfo->active_plugins);
	if (!is_array($active_plugins))
		$active_plugins = array();

	$active_plugins = array_remove_value($active_plugins, $plug);

	$db->query("UPDATE `" . MAI_PREFIX . "settings` SET `active_plugins` = '" . serialize(array_unique($active_plugins)) . "'");

	if (is_callable($plug . "_deactivate")) {
		call_user_func($plug . "_deactivate");
	}
	ob_end_clean();
	header("Location: ?page=$page");
	exit;
}

// install
if ($act == "install") {
	$plug = $_GET['plugin'];
	if (is_callable($plug . "_install")) {
		call_user_func($plug . "_install");
	}
	ob_end_clean();
	header("Location: ?page=$page");
	exit;
}

// uninstall
if ($act == "uninstall") {
	$plug = $_GET['plugin'];

	if ($_POST['yes']) {
		// if it's active
		if (in_array($plug, unserialize($set->sinfo->active_plugins))) {
			// we deactivate
			$active_plugins = unserialize($set->sinfo->active_plugins);
			if (!is_array($active_plugins))
				$active_plugins = array();

			$active_plugins = array_remove_value($active_plugins, $plug);

			$db->query("UPDATE `" . MAI_PREFIX . "settings` SET `active_plugins` = '" . serialize(array_unique($active_plugins)) . "'");
			if (is_callable($plug . "_deactivate")) {
				call_user_func($plug . "_deactivate");
			}
		}
		// we uninstall
		if (is_callable($plug . "_uninstall")) {
			call_user_func($plug . "_uninstall");
		}
		ob_end_clean();
		header("Location: ?page=$page");
		exit;
	} else {

		$content = "<form action='#' method='post'><div class='card mb-2'><div class='card-body'><h5 class='card-title'>$lang->are_you_sure</h5><div class='text-end'><input type='submit' class='btn btn-primary' name='yes' value='$lang->uninstall'> <a class='btn btn-danger text-white fw-normal' href='?'>$lang->cancel</a></div></div></div></form>";
	}
}

////// settings
elseif ($act == "settings") {

	$plug = $_GET['plugin'];

	if ($_POST) {
		$on_save = true;
		$fail_message = 'fail'; // use this var to store any error message in _on_save()
		if (is_callable($plug . "_on_save")) {
			$on_save = (bool)call_user_func($plug . "_on_save");
		}

		if ($on_save) {
			foreach ($_POST as $k => $v) {
				if (is_array($v))
					$value = $db->escape(serialize($v));
				else
					$value = $db->escape($v);

				$db->query("UPDATE `" . MAI_PREFIX . "plugins_settings` SET `value`='$value' WHERE `name` = '" . $db->escape($k) . "'");
			}

			$content .= "<div class='alert alert-success'>$lang->saved</div>";
		} else
			$content .= "<div class='alert alert-danger'>$fail_message</div>";
	}

	$data = $db->select("SELECT * FROM `" . MAI_PREFIX . "plugins_settings` WHERE `plugin` = '" . $db->escape($plug) . "'");
	// is it active ??
	if (!in_array($plug, unserialize($set->sinfo->active_plugins)) or !$data) {
		ob_end_clean();
		header("Location: ?page=$page");
		exit;
	}
	$content .= "<form action='#' method='post'>";

	$content .= "<div class='list-group mb-2'><div class='list-group-item fs-5 fw-bold active'>" . htmlentities($_GET['plugin']) . " - " . $lang->settings . "</div>";

	foreach ($data as $p) {
		$content .= "<div class='list-group-item fw-bold bg-primary-subtle'>$p->title</div>";
		$content .= "<div class='list-group-item'>";
		if ($p->type == 'yesno') {
			$content .= "<select class='form-control' name='$p->name'>
			<option value='0'" . ($p->value == 0 ? " selected='vmi'" : "") . ">$lang->no</option>
			<option value='1'" . ($p->value == 1 ? " selected='vmi'" : "") . ">$lang->yes</option>
			</select>";
		}
		if ($p->type == 'onoff') {
			$content .= "<select class='form-control' name='$p->name'>
			<option value='0'" . ($p->value == 0 ? " selected='vmi'" : "") . ">$lang->off</option>
			<option value='1'" . ($p->value == 1 ? " selected='vmi'" : "") . ">$lang->on</option>
			</select>";
		}
		if ($p->type == 'textarea') {
			$content .= "<textarea class='form-control' rows='5' name='$p->name'>" . htmlentities($p->value) . "</textarea>";
		}
		if ($p->type == 'text') {
			$content .= "<input type='text' class='form-control' name='$p->name' value='" . htmlentities($p->value, ENT_QUOTES) . "'>";
		}
		if (preg_match("~select(.+)~i", $p->type)) {
			$type = explode("\n", $p->type);
			$content .= "<select class='form-select' name='$p->name'>";
			for ($i = 1; $i <= count($type); $i++) {
				$val = explode("=", $type[$i]);
				if (trim($val[0]) != '')
					$content .= "<option value='" . $val[0] . "'" . ($p->value == $val[0] ? " selected='vmi'" : "") . ">" . htmlentities($val[1]) . "</option>";
			}
			$content .= "</select>";
		}
		if (preg_match("~radio(.+)~i", $p->type)) {
			$type = explode("\n", $p->type);
			for ($i = 1; $i <= count($type); $i++) {
				$val = explode("=", $type[$i]);
				if (trim($val[0]) != '')
					$content .= "<input class='form-check-input' type='radio' name='$p->name' value='" . $val[0] . "'" . ($p->value == $val[0] ? " checked='vmi'" : "") . ">" . htmlentities($val[1]) . "<br/>";
			}
		}
		if (preg_match("~checkbox(.+)~i", $p->type)) {
			$type = explode("\n", $p->type);
			for ($i = 1; $i <= count($type); $i++) {
				$val = explode("=", $type[$i]);
				if (trim($val[0]) != '')
					$content .= "<input class='btn-check' type='checkbox' name='$p->name[]' value='" . $val[0] . "'" . (in_array($val[0], unserialize($p->value)) ? " checked='vmi'" : "") . ">" . htmlentities($val[1]) . "<br/>";
			}
		}
		$content .= "</div>";
		$content .= "<small class='list-group-item bg-secondary-subtle'><span class='badge bg-secondary'>Note</span> $p->description </small>";
	}

	$content .= "</div>";
	$content .= "<div class='text-center'><input type='submit' class='btn btn-dark px-4' value='$lang->save'></div></form>";
} else {
	// plugin list
	include "../lib/array_pagination.class.php";
	include "../lib/pagination.class.php";

	$plugs_list = glob("../plugins/*_plugin.php");

	$total_results = count($plugs_list);
	// pagination
	$perpage = $_SESSION['perp'] ? (int)$_SESSION['perp'] : $set->perpage;
	// $page is defined in the first lines
	if ($page > ceil($total_results / $perpage)) $page = ceil($total_results / $perpage);
	$start = ($page - 1) * $perpage;

	$s_pages = new pag($total_results, $page, $perpage);

	$show_pages = "<span class='page-link text-dark' href='#'>$lang->pages : </span>" . $s_pages->pages;

	$a_pag = new array_pagination;
	$plugs = $a_pag->generate($plugs_list, $perpage);

	$content .= "<div class='list-group mb-2'>";
	$content .= "<div class='list-group-item fs-4 fw-bold active'>$lang->plugin_manager</div>";

	foreach ($plugs as $plug) {
		// grab the plugin name this is why the name of the plugin must be
		// <name>_plugin.php
		$plug = substr(basename($plug), 0, -11);

		$content .= "<div class='list-group-item bg-primary-subtle fs-5'>";

		if (is_callable($plug . "_info")) {
			$info = (object)call_user_func($plug . "_info");
		}

		$content .=  $info->name != '' ? $info->name : $plug;

		if (is_callable($plug . "_uninstall"))
			$uninstall = " <a class='btn btn-danger text-light fw-normal fs-6 py-0 px-2' href='?act=uninstall&plugin=$plug&page=$page'>$lang->uninstall</a>";
		else
			$uninstall = '';
		if (is_callable($plug . "_is_installed")) {
			$is_installed = (bool)call_user_func($plug . "_is_installed");
		} else
			$is_installed = true;

		if ($is_installed) {
			$is_active = in_array($plug, unserialize($set->sinfo->active_plugins));
			if ($is_active) {
				// check for settings
				if ($db->count("SELECT `name` FROM `" . MAI_PREFIX . "plugins_settings` WHERE `plugin` = '" . $db->escape($plug) . "'") > 0)
					$content .= " <a class='btn btn-secondary text-light fw-normal fs-6 py-0 px-2' href='?act=settings&plugin=$plug'>$lang->settings</a> ";
				$content .= " <a class='btn btn-dark text-light fw-normal fs-6 py-0 px-2' href='?act=deactivate&plugin=$plug&page=$page'>$lang->deactivate</a> ";
			} else
				$content .= " <a class='btn btn-success text-light fw-normal fs-6 py-0 px-2' href='?act=activate&plugin=$plug&page=$page'>$lang->activate</a> ";

			// show uninstall
			$content .= $uninstall;
		} else {
			$content .= " <a class='btn btn-primary text-light fw-normal fs-6 py-0 px-2' href='?act=install&plugin=$plug&page=$page'>$lang->install</a> ";
		}

		if ($db->count("SELECT `name` FROM `" . MAI_PREFIX . "plugins_settings` WHERE `plugin` = '" . $db->escape($plug) . "'") > 0)
			$have_set = true;

		$content .= "</div>";
		$content .= "<div class='list-group-item'>";

		$content .= "<small>" . ($info->author != '' ? "$lang->author : " . ($info->author_site != '' ? "<a href='$info->author_site'>$info->author</a>" : $info->author) . " <br/>" : "") . "$info->description </small>";
		$content .= "</div>";
	}

	$content .= "</div>";
	$content .= "<div class='pagination justify-content-center m-2'>{$show_pages}</div>";
}

include "../header.php";
$tpl->grab("plugin_manager.tpl", "plugin_manager");
$tpl->assign('plugin_manager', $lang->plugin_manager);
$tpl->assign("content", $content);
$tpl->display();
$plugins->run_hook("plugin_manager_end");
include "../footer.php";
