<?php

if (preg_match("#sitemap.php#s", $_SERVER['REQUEST_URI'])) {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: sitemap.xml');
	exit;
}

header('Content-type: text/xml');

include "inc/init.php";

$file_data = $db->select("SELECT * FROM `" . MAI_PREFIX . "files` ORDER BY `id` DESC");

echo "<?xml version='1.0' encoding='UTF-8'?>\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$baseUrl = $set->url;

$staticUrls = [
	"/",
	"/disclaimer",
	"/tos",
	"/usr_set",
	"/request",
	"/top"
];

foreach ($staticUrls as $url) {
	outputUrl($baseUrl . $url, 1.0, 'monthly');
}

if ($file_data) {
	foreach ($file_data as $mydata) {
		$url = $mydata->isdir == '1'
			? "$baseUrl/data/$mydata->id/" . mai_converturl($mydata->name) . "/"
			: "$baseUrl/data/file/$mydata->id/" . mai_converturl($mydata->name);

		outputUrl($url, 0.80, 'daily');
	}
}

echo '</urlset>';

function outputUrl($loc, $priority, $changefreq)
{
	echo sprintf("
<url>
    <loc>%s</loc>
    <priority>%s</priority>
    <changefreq>%s</changefreq>
</url>", htmlspecialchars($loc), $priority, $changefreq);
}
