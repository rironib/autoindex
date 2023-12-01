<?php

/**
 * Adverts Plugin
 * Displays HTML ads in the header and/or footer.
 * Author: ionutvmi@gmail.com
 * Date: 21-Sep-2012
 * Website: http://master-land.net
 */

// Hooks
$plugins->add_hook("header", "ads_show_top");
$plugins->add_hook("footer", "ads_show_foot");

// Plugin Information
function ads_info()
{
	return array(
		'name' => 'Adverts Plugin',
		'author' => 'ionutvmi',
		'author_site' => 'http://master-land.net',
		'description' => 'Displays HTML ads in the header and/or footer',
	);
}

// Installation
function ads_install()
{
	global $db;

	// Settings
	$settings = array(
		array(
			'name' => 'ads_show',
			'value' => '2',
			'title' => 'Place ads on:',
			'description' => 'The place where the ads will be displayed',
			'type' => 'select',
			'options' => '0=Top|1=Bottom|2=Both',
			'plugin' => 'ads',
		),
		array(
			'name' => 'ads_show_text_top',
			'value' => '<div class="ad"><a href="http://mirazmac.info">Sample Text Link Ad on Top</a></div>',
			'title' => 'Top Ad',
			'description' => 'The ad content that will be placed in the header',
			'type' => 'textarea',
			'plugin' => 'ads',
		),
		array(
			'name' => 'ads_show_text_foot',
			'value' => '<div class="ad"><a href="http://mirazmac.info">Sample Text Link Ad on Footer</a></div>',
			'title' => 'Footer Ad',
			'description' => 'The ad content that will be placed in the footer',
			'type' => 'textarea',
			'plugin' => 'ads',
		),
	);


	$db->insert_array(MAI_PREFIX . 'plugins_settings', $setting);
}

// Advertisement Display
function ads_show_top($value)
{
	global $set;

	// Check if ads should be displayed at the top
	if ($set->plugins['ads_show'] == '0' || $set->plugins['ads_show'] == '2') {
		$value = str_replace('<!--header end-->', '<!--header end-->' . $set->plugins['ads_show_text_top'], $value);
	}
	return $value;
}

function ads_show_foot($value)
{
	global $set;
	// Check if ads should be displayed at the bottom
	if ($set->plugins['ads_show'] == '1' || $set->plugins['ads_show'] == '2') {
		$value = str_replace('<!--footer start-->', '<!--footer start-->' . $set->plugins['ads_show_text_foot'], $value);
	}
	return $value;
}


// Uninstallation
function ads_uninstall()
{
	global $db;
	$db->query("DELETE FROM `" . MAI_PREFIX . "plugins_settings` WHERE `plugin`='ads'");
}
