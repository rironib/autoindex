<?php
$plugins->add_hook("index", "hide_pag_add");

function hide_pag_info()
{
	return array(
		"name" => "Hide Page",
		"author" => "ionutvmi",
		"author_site" => "http://master-land.net",
		"description" => "This will hide the pagination if there is only 1 page",
	);
}

function hide_pag_add($value)
{
	global $total_results, $perpage;
	if ($total_results > 0 && ceil($total_results / $perpage) < 2)
		$value = preg_replace("~<div class=\"pages\">([^<]+)</div>~s", "", $value);
	return $value;
}
