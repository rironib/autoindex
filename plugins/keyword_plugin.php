<?php
# Plugin name: Keyword Cloud Plugin
# Author: MiraZ Mac
# Author URI: http://mirazmac.info
# Description: This plugin will show keyword cloud on file download page. You can use it with Next SEO Plugin for better optmization.
# License: GPL v3

/******************************** 
========== !Warning! =============
You are not allowed to modify this
plugin. If you do so you may not
receive future plugin updates.
 ******************************** 
 */


/*== Bismillah! ==*/

//Hooks
$plugins->add_hook("file", "keyword_add");
$plugins->add_hook("footer", "keyword_stylesheet");


function keyword_info()
{
	return	array(
		"name" => "Keyword Cloud Plugin",
		"author" => "Miraz Mac",
		"author_site" => "http://mirazmac.info",
		"description" => "This plugin will show keyword cloud on file download page.<br/><a href='http://mirazmac.info'>Visit Plugin Homepage</a> - <a href='http://facebook.com/miraz.mac'><span style='color:red'>Report Bug</span></a>",
	);
}
function keyword_install()
{
	//Global Database
	global $db;
	// settings menu
	$settings_data = array(
		"name" => "keyword_style",
		"value" => "background: none repeat scroll 0% 0% #FFF;
padding: 5px;
border: 1px solid rgba(224, 224, 224, 1);
text-align: center;
margin: 2px 0px;
font-size: 13px;",
		"title" => "Keyword cloud stylesheet",
		"description" => "Customize the div style of keyword",
		"type" => "textarea",
		"plugin" => "keyword",
	);
	$settings_data2 = array(
		"name" => "keyword_text",
		"value" => "Download New %filename% mp3 Song Free download | Download %filename% Bangla full Album mp3 song | %filename% Songs Album | %filename% song download, mp3, Amr, Sound Track | %filename% bangla movie download,free | %filename% 192 kbps 64 kbps | %filename% Songs Album 320 kbps download, | %filename% master print download, | %filename% full movie official print download,| %filename% clean download, | %filename% Songs Album mp3 album download , | %filename% mp3 song full album | %filename% Songs Album zip file download, | %filename% mp4 download, | %filename% PC HD Download, | %filename% new high quality download,low quality | %filename% 2015,2016, 2017, 2018 full download, | %filename% Music video download now, | %filename% Music mp3 full mp3 download now, | %filename% CD rip download, | %ext% DVDrip Vcdscam webrip Dvdscam download now, | %filename% 3gp,mp4,avi,mkv download now,full HD | %filename% Download,new movie | %filename% 3gp mp4 avi mkv download,full HD 3gp PC Mp4 3gp download, | %filename% Non retail download game,software,ringtone, | %filename% Grameenphone Welcome Tune Code, | %filename% Caller tune,Teletune,Ichche Tune , Gp wt Code %filename% Movie All Mp3 Songs Album | %filename% full Lyrics | %filename% Bangla Unreleased Mp3 Songs Download | Bollywood %filename% Mp3 Download %filename% Kolkata Bangla Mp3 Download Now",
		"title" => "Keyword Text",
		"description" => "The main text of the keyword. No HTML allowed. Use <b>[b]text[/b]</b> to bold text. Variable list:<br/> <b>%filename%</b> will output the File name and <b>%ext%</b> will output the file extension.",
		"type" => "textarea",
		"plugin" => "keyword",
	);
	$db->insert_array(MAI_PREFIX . "plugins_settings", $settings_data);
	$db->insert_array(MAI_PREFIX . "plugins_settings", $settings_data2);
}
// After plugin is installed
function keyword_is_installed()
{
	global $db;
	if ($db->count("SELECT `name` FROM `" . MAI_PREFIX . "plugins_settings` WHERE `plugin`='keyword'") > 0)
		return true;

	return false;
}

// After plugin is uninstalled
function keyword_uninstall()
{
	global $db;
	$db->query("DELETE FROM `" . MAI_PREFIX . "plugins_settings` WHERE `plugin`='keyword'");
}



//The main part - here we set the keywords	
function keyword_add($mac = '')
{
	global $download, $set, $file, $ext;
	$ext = $ext->extension;
	$showext = strtoupper($ext);
	$filename = $file->name;
	$txt = htmlentities($set->plugins['keyword_text']);
	//Defining Variables value
	$txt = str_replace('%filename%', "$filename", $txt);
	$txt = str_replace('%ext%', "$showext", $txt);
	$txt = preg_replace('#\[b\](.*?)\[/b\]#si', '<b>\1</b>', $txt);
	//Lets show
	$download = $download . "<div class='list-group mb-2'><div class='list-group-item fs-6 text-center'><b style='color:red'>Tags Cloud:</b> $txt</div></div>";

	return $mac;
}


//Now lets embed the stylesheet
function keyword_stylesheet($value)
{
	global $db, $set;

	$value = str_replace("</body>", '<style>.tags_cloud{' . $set->plugins["keyword_style"] . '}</style></body>', $value);

	return $value;
}
