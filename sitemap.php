<?php

if (preg_match("#sitemap.php#s", $_SERVER['REQUEST_URI'])) {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: sitemap.xml');
	exit;
}

header('Content-type: text/xml');

include "inc/init.php";

$file_data = $db->select("SELECT * FROM `" . MAI_PREFIX . "files` ORDER BY `id` DESC");
$file_data2 = $db->select("SELECT * FROM `" . MAI_PREFIX . "files` ORDER BY `id` DESC LIMIT 0,1");

echo "<?xml version='1.0' encoding='UTF-8'?>\n";
echo '<?xml-stylesheet type="text/xsl" href="' . $set->url . '/sitemap.xsl"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

$baseUrl = $set->url;

$staticUrls = [
	"/",
	"/disclaimer",
	"/request",
	"/top",
	"/tos",
	"/usr_set",
];

foreach ($staticUrls as $url) {
	outputUrl($baseUrl . $url, 1.0, date("D, d M Y H:i:s T"));
}

if ($file_data) {
	foreach ($file_data as $mydata) {
		$url = $mydata->isdir == '1'
			? "$baseUrl/data/$mydata->id/" . mai_converturl($mydata->name) . "/"
			: "$baseUrl/data/file/$mydata->id/" . mai_converturl($mydata->name);

		$lastmod = ($file_data2) ? date("D, d M Y H:i:s T", $file_data2[0]->time) : date("D, d M Y H:i:s T");
		outputUrl($url, 0.80, $lastmod);
	}
}

echo '</urlset>';

function outputUrl($loc, $lastmod, $priority)
{
	echo sprintf("
<url>
    <loc>%s</loc>
	<lastmod>%s</lastmod>
    <priority>%s</priority>
</url>", htmlspecialchars($loc), $lastmod, $priority);
}
