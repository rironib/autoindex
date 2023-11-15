<?php
// Check for sitemap.php in the URI and redirect
if (preg_match("#sitemap.php#s", $_SERVER['REQUEST_URI'])) {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: sitemap.xml');
	exit; // Terminate script after redirection
}

// Set content type to XML
header('Content-type: text/xml');

// Include initialization file
include "inc/init.php";

// Fetch file data from the database
$file_data = $db->select("SELECT * FROM `" . MAI_PREFIX . "files` ORDER BY `id` DESC");

// Output XML header
echo "<?xml version='1.0' encoding='UTF-8'?>\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

// Base URL for the website
$baseUrl = $set->url;

// Static URLs
$staticUrls = [
	"/",
	"/disclaimer",
	"/tos",
	"/usr_set",
	"/request",
	"/top"
];

// Output static URLs with the base URL
foreach ($staticUrls as $url) {
	outputUrl($baseUrl . $url, 1.0, 'daily');
}

// Output dynamic URLs from the database
if ($file_data) {
	foreach ($file_data as $mydata) {
		$url = $mydata->isdir == '1'
			? "$baseUrl/data/$mydata->id/" . mai_converturl($mydata->name) . "/"
			: "$baseUrl/data/file/$mydata->id/" . mai_converturl($mydata->name);

		outputUrl($url, 0.80, 'daily');
	}
}

// Close the XML document
echo '</urlset>';

// Function to output URL with priority and change frequency
function outputUrl($loc, $priority, $changefreq)
{
	echo sprintf("
<url>
    <loc>%s</loc>
    <priority>%s</priority>
    <changefreq>%s</changefreq>
</url>", htmlspecialchars($loc), $priority, $changefreq);
}
