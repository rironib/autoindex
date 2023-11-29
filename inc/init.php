<?php

session_start();
error_reporting(E_ALL ^ E_NOTICE); // hide the notice
define("MAI_ROOT", dirname(dirname(__FILE__)) . "/");
define("MAI_TPL", "tpl/");
//Redirect to Setup Page
$chk = '' . MAI_ROOT . 'inc/settings.php';
if (!file_exists($chk)) {
	header('Location: install.php');
}

$set = new stdClass(); // php 5.4 fix


include MAI_ROOT . "inc/settings.php";
// remove the last slash if any in $set->url
$set->url = rtrim($set->url, "/");

include MAI_ROOT . "lib/mysql.class.php";
include MAI_ROOT . "lib/plugin.class.php";
include MAI_ROOT . "lib/template.class.php";
include MAI_ROOT . "lang/index.php";
include MAI_ROOT . "lib/functions.php";


// make $lang an object
$lang = (object)$lang;
// template object
$tpl = new Tpl();

// version
$set->version = '3.2';

// db connection
$db = new dbConn($set->db_host, $set->db_user, $set->db_pass, $set->db_name);


$set->sinfo = $db->get_row("SELECT * FROM `" . MAI_PREFIX . "settings`");

if (!$set->sinfo) {
	header("Location: install.php");
	exit;
}


// check if we have any cookie saved
if ($_COOKIE['pass'] == $set->sinfo->admin_pass)
	$_SESSION['adminpass'] = $set->sinfo->admin_pass;


// get the settings for plugins
if (!is_array(unserialize($set->sinfo->active_plugins)))
	$set->sinfo->active_plugins = serialize(array());

$_PS = $db->select("SELECT `name`,`value` FROM `" . MAI_PREFIX . "plugins_settings`");
if ($_PS) {
	foreach ($_PS as $__PS) {
		$set->plugins[$__PS->name] = $__PS->value;
	}
}

// plugins object
$plugins = new Plugins();
$plugins->load();

// $links[] = " » " . "&nbsp;<a href='$set->url'>$lang->Home</a>";

$links[] = "<li class='breadcrumb-item'><a href='$set->url'>$lang->Home</a></li>";

$plugins->run_hook("init");

remove_magic_quotes();
