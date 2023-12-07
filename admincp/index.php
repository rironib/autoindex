<?php

include "../inc/init.php";
$plugins->run_hook("admin_top");

$links[] = "<li class='breadcrumb-item active' aria-current='page'><a href='$set->url/admincp/'>$lang->admincp</a></li>";

if ((hash('sha256', $_POST['pass']) == $set->sinfo->admin_pass) && ($_POST['token'] == $_SESSION['token']) or is_admin()) {

	$_SESSION['token'] = '';

	if ($_POST['r'] == 1) {
		$path_info = parse_url($set->url);
		$hashed_password = hash('sha256', $_POST['pass']);
		setcookie("pass", $hashed_password, time() + 3600 * 24 * 7, $path_info['path']);
	}

	if (!$_SESSION['adminpass']) {
		$_SESSION['adminpass'] = hash('sha256', $_POST['pass']);
	}

	$request_new = $db->count("SELECT `id` FROM `" . MAI_PREFIX . "request` WHERE `reply`=''");

	include "../header.php";
	$version = $set->version;
	$chk_v = get_version("https://stockwalls.pw/version.txt", "0");
	if ($chk_v > $version) {
		$update_av = "<div class='alert alert-warning text-center mt-2'><a href='https://stockwalls.pw/'>Next AutoIndex $chk_v is available! Please Update Now!</a></div>";
	}

	$tpl->grab('admin_options.tpl', 'admin_options');
	$tpl->assign('password', $lang->password);
	$tpl->assign('url', $set->url);
	$tpl->assign('import_files', $lang->import_files);
	$tpl->assign('settings', $lang->settings);
	$tpl->assign('update_av', $update_av);
	$tpl->assign('login', $lang->login);
	$tpl->assign('request', $lang->request);
	$tpl->assign('request_new', $request_new);
	$tpl->assign('file_manager', $lang->file_manager);
	$tpl->assign('plugin_manager', $lang->plugin_manager);
	//New addition
	$tpl->assign('update_av', $update_av);
	$tpl->assign('web_scanner', $lang->web_scanner);
	$tpl->assign('mass_frn', $lang->mass_frn);
	$tpl->assign('config_editor', $lang->config_editor);
	$tpl->assign('tpl_editor', $lang->tpl_editor);
	$tpl->assign('robots_editor', $lang->robots_editor);
	$tpl->assign('sitemap_editor', $lang->sitemap_editor);
	$tpl->assign('plugins_market', $lang->plugins_market);
	$tpl->assign('upload_files', $lang->upload_files);
	$tpl->assign('mark', mai_img('arr.gif'));
	$tpl->assign('version', $set->version);
} else {
	$token = $_SESSION['token'] = md5(rand());

	include "../header.php";
	$tpl->grab('admin_pass.tpl', 'admin_pass');
	$tpl->assign('username', $lang->username);
	$tpl->assign('password', $lang->password);
	$tpl->assign('remember', $lang->remember);
	$tpl->assign('login', $lang->login);
	$tpl->assign('token', $token);
}
$tpl->display();
$plugins->run_hook("admin_end");
include "../footer.php";
