<?php
// Bismillah!
//The information of the plugin
//Adding hooks
$plugins->add_hook("header","wapking_stylesheet_embed");
$plugins->add_hook("index","wapking_stylesheet_search_hide");

function wapking_stylesheet_info(){

	return	array(	
	"name" => "Wapking Stylesheet Plugin",
	"author" => "MiraZ Mac",
	"author_site" => "http://mirazmac.info",
	"description" => "This plugin will change your site's css to wapking wapking style css.<br/><a href='http://mirazmac.info'>Visit Plugin Homepage</a> - <a href='http://facebook.com/miraz.mac'><span style='color:red'>Report Bug</span></a>",
	);
	
}


//Function to clean unused head tags
function wapking_stylesheet_embed($value){
	global $db,$set;
$value = preg_replace('|<link rel="stylesheet"(.*?)"/>|is','<link rel="stylesheet" type="text/css" href="'.$set->url.'/tpl/style/wapking.css"/>',$value);
	return $value;
}
function wapking_stylesheet_search_hide($value=''){
global $tpl;
$value=str_replace('<div class="title">Search</div>','',$value);
return $value;}