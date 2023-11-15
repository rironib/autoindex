<?php
$plugins->add_hook("index", "top_add");

function top_info()
{
	return array(
		"name" => "Top Files Plugin",
		"author" => "ionutvmi",
		"author_site" => "http://master-land.net",
		"description" => "This will display the top files on your site",
	);
}

function top_install()
{
	global $db;
	// settings 
	$settings_data = array(
		"name" => "top_sort", // name of the setting must be unique so adding the plugin name is a good practice
		"value" => "views", // default value
		"title" => "Sort Top Files By", // title will be displayed on settings page
		"description" => "The top files will be sorted by this criteria", // description
		"type" => "select \nviews=Views Number \ndcount=Downloads Number", // type check master-land.net for more info
		"plugin" => "top", // your plugin <name>
	);
	$settings_data2 = array(
		"name" => "top_number",
		"value" => "20",
		"title" => "Top Files No",
		"description" => "The number of files to be displayed, keep in mind the there in so pagination",
		"type" => "text",
		"plugin" => "top",
	);
	$settings_data3 = array(
		"name" => "top_sort_type",
		"value" => "DESC",
		"title" => "Sort files",
		"description" => "Order the files should be sorted <b>ASC</b> or <b>DESC</b>",
		"type" => "select \nASC=ASC \nDESC=DESC",
		"plugin" => "top",
	);

	$db->insert_array(MAI_PREFIX . "plugins_settings", $settings_data);
	$db->insert_array(MAI_PREFIX . "plugins_settings", $settings_data2);
	$db->insert_array(MAI_PREFIX . "plugins_settings", $settings_data3);
}

function top_is_installed()
{
	global $db;
	if ($db->count("SELECT `name` FROM `" . MAI_PREFIX . "plugins_settings` WHERE `plugin`='top'") > 0)
		return true;

	return false;
}

function top_uninstall()
{
	global $db;
	$db->query("DELETE FROM `" . MAI_PREFIX . "plugins_settings` WHERE `plugin`='top'");
}

function top_add($value)
{
	global $dir, $set;
	// here you can edit the html code

	if (!$dir)
		// $value .= "<div class='content2'><a href='$set->url/top.php'><img src='$set->url/" . MAI_TPL . "style/images/gdir.gif' alt='.'/>&nbsp;Top Files</a></div>";
		$value .= "";
	return $value;
}
