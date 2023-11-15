<?php
/*
# Plugin name: Next SEO 2.0
# Author: MiraZ Mac
# Author URI: http://mirazmac.info
# Description: The must have SEO plugin for MAI. Next SEO optimizes you site's SEO & overall page loading speed. It includes automated sitemap, twitter cards, facebook og, alexa-google-bing verification. And more..
# License: GPL v3

******************************** 
========== !Warning! =============
You are not allowed to modify this
plugin. If you do so you may not
receive future plugin updates.
******************************** 
*/


/*== Bismillah! ==*/


//Adding hooks
$plugins->add_hook("header","next_seo_other");
$plugins->add_hook("header","next_seo_custom_meta");
$plugins->add_hook("header","next_seo_header_clean");
$plugins->add_hook("header","next_seo_tags");
$plugins->add_hook("footer","next_seo_footer");


//The information of the plugin
function next_seo_info(){

	return	array(	
	"name" => "Next SEO 2.0",
	"author" => "MiraZ Mac",
	"author_site" => "http://mirazmac.info",
	"description" => "The must have SEO plugin for Next Autoindex. Next SEO optimizes you site's SEO & overall page loading speed. It includes twitter cards, facebook og, google analytics, alexa-google-bing verification. And more..<br/><span style='color:blue'>Automated sitemap has been discontinued cause <b>Next AutoIndex 3.0</b> supports automatic sitemaps.</span><br/><a href='http://mirazmac.info'>Visit Plugin Homepage</a> - <a href='http://facebook.com/miraz.mac'><span style='color:red'>Report Bug</span></a>",
	);
	
}

//Tasks to do when plugin installs
function next_seo_install(){
//The contents of htaccess file
$list="\n#Next SEO 2.0 \nRewriteEngine On \nRewriteRule ^sitemap.xml$ sitemap.php [L] \n# compress text, html, javascript, css, xml\nAddOutputFilterByType DEFLATE text/plain\nAddOutputFilterByType DEFLATE text/html\nAddOutputFilterByType DEFLATE text/xml\nAddOutputFilterByType DEFLATE text/css\nAddOutputFilterByType DEFLATE application/xml\nAddOutputFilterByType DEFLATE application/xhtml+xml\nAddOutputFilterByType DEFLATE application/rss+xml\nAddOutputFilterByType DEFLATE application/javascript\nAddOutputFilterByType DEFLATE application/x-javascript\n#Next SEO 2.0 Ends\n";
//Defining file paths
$file=''.MAI_ROOT.'/.htaccess';

//Getting htaccess file contents
$fileContents = file_get_contents($file);
//This line is useful to avoid multi lines
if(preg_match('|Next SEO 2.0|is',$fileContents)){echo'';}
else{file_put_contents($file, $fileContents . $list);}

//Global Database
	global $db;
	// settings menu
	$settings_data = array(
	"name" => "next_seo_google", 
	"value" => "fgf545ff5675g", 
	"title" => "Google Webmaster Verification", 
	"description" => "Insert your Google Webmaster Verification id", 
	"type" => "textarea",
	"plugin" => "next_seo", 
	);
	$settings_data2 = array(
	"name" => "next_seo_meta_description", 
	"value" => "We offer 100% free downloads of everything", 
	"title" => "Meta Description", 
	"description" => "Insert any meta description.", 
	"type" => "textarea",
	"plugin" => "next_seo", 
	);
	$settings_data3 = array(
	"name" => "next_seo_meta_keywords", 
	"value" => "symbian,nokia,software,apps,jar,java,games,wallpaper,download, mobile, videos,mobile, movies,bollywood,hollywood", 
	"title" => "Meta Keywords", 
	"description" => "Insert any meta keywords.", 
	"type" => "textarea",
	"plugin" => "next_seo", 
	);

	$settings_data4 = array(
	"name" => "next_seo_google_analytics", 
	"value" => "<script>...</script>", 
	"title" => "Google Analytics HTML Code", 
	"description" => "Insert your Google analytics html tag", 
	"type" => "textarea",
	"plugin" => "next_seo", 
	);
	$settings_data5 = array(
	"name" => "next_seo_alexa", 
	"value" => "C8t2W8hvVsR9yaG206KeiJPtWqs", 
	"title" => "Alexa Verification", 
	"description" => "Insert your alexa verification ID", 
	"type" => "textarea",
	"plugin" => "next_seo", 
	);
	$settings_data6 = array(
	"name" => "next_seo_bing", 
	"value" => "A136E8E6140DBA6D0449E00610279892", 
	"title" => "Bing Webmaster Verification", 
	"description" => "Insert your Bing Webmaster Verification id", 
	"type" => "textarea",
	"plugin" => "next_seo", 
	);
	$db->insert_array(MAI_PREFIX."plugins_settings",$settings_data);
	$db->insert_array(MAI_PREFIX."plugins_settings",$settings_data2);
	$db->insert_array(MAI_PREFIX."plugins_settings",$settings_data3);
	$db->insert_array(MAI_PREFIX."plugins_settings",$settings_data4);
	$db->insert_array(MAI_PREFIX."plugins_settings",$settings_data5);
	$db->insert_array(MAI_PREFIX."plugins_settings",$settings_data6);

}

// After plugin is installed
function next_seo_is_installed(){
	global $db;
	if($db->count("SELECT `name` FROM `".MAI_PREFIX."plugins_settings` WHERE `plugin`='next_seo'") > 0)
		return true;
	
	return false;
	
}

// After plugin is uninstalled
function next_seo_uninstall(){
	
	$path=''.MAI_ROOT.'/.htaccess';
	$xfile = file_get_contents($path);
$newfile = preg_replace('|#Next SEO 2.0(.*?)#Next SEO 2.0 Ends|is','',$xfile); //deleting .htaccess contents created by next seo
file_put_contents($path, $newfile);
global $db;
	$db->query("DELETE FROM `".MAI_PREFIX."plugins_settings` WHERE `plugin`='next_seo'");
}



//Custom Meta Tags Function
function next_seo_custom_meta($value){
	global $db,$set;
	
		$value = preg_replace('|<meta name="description" content="(.*?)">|is','<meta name="description" content="'.$set->plugins["next_seo_meta_description"].'">',$value);
		$value = preg_replace('|<meta name="keywords" content="(.*?)">|is','<meta name="keywords" content="'.$set->plugins["next_seo_meta_keywords"].'">',$value);
	
	return $value;
}



//Other Function
function next_seo_other($value){
	global $db,$set;
	
	
		$value = str_replace("</head>",'<meta name="google-site-verification" content="'.$set->plugins["next_seo_google"].'">
	<meta name="msvalidate.01" content="'.$set->plugins["next_seo_bing"].'">
	<meta name="alexaVerifyID" content="'.$set->plugins["next_seo_alexa"].'"></head>',$value);
		$value = str_replace("</head>",'
<meta name="viewport" content="width=device-width">
<meta property="og:site_name" content="{$title}">
<meta property="og:type" content="website">
<meta property="og:locale" content="en_US">
<meta property="og:title" content="{$title}">
<meta property="og:description" content="'.$set->plugins["next_seo_meta_description"].'">
<meta property="og:url" content="{$url}">
<meta name="twitter:card" content="summary">
<meta name="twitter:description" content="'.$set->plugins["next_seo_meta_description"].'">
<meta name="twitter:title" content="{$title}">
<meta name="twitter:domain" content="{$url}">
<meta name="distribution" content="global">
<meta name="Identifier-URL" content="{$url}">
<meta content="chrome=1" http-equiv="X-UA-Compatible">
<meta name="revisit-after" content="1 days">
<meta name="robots" content="index,follow">
<meta content="General" name="Rating">
<meta content="never" name="Expires">
<meta content="all" name="audience">
</head>',$value);
	
	return $value;
}

//Footer
function next_seo_footer($value){
	global $db,$set;
	
	
		$value = str_replace("</body>",''.$set->plugins["next_seo_google_analytics"].'</body>',$value);
	
	return $value;
}

//Function to clean unused head tags
function next_seo_header_clean($value){
	global $db,$set;
	
	
		$value = str_replace('<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">','<!DOCTYPE HTML>',$value);
	$value= str_replace('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">','<html hreflang="en">',$value);
	return $value;
}

function next_seo_tags($value){
	global $db,$set;
	
	
		$value = preg_replace('|<head>(.*?)</head>|is','<head>
<!--This site is optimized with Next SEO 2.0-->
$1
<!--Next SEO 2.0 Ends-->
</head>',$value);
	
	return $value;
}

/* What the fu*k? I said close the file */
/* Generated in Mac Incorporated Lab 13/07/2015 - 12:44 PM (GMT+6) */