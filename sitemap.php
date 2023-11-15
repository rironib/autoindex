<?php
// Next AutoIndex Sitemap
if (preg_match("#sitemap.php#s", '' . $_SERVER['REQUEST_URI'] . '')) {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: sitemap.xml');
} else {
	header('Content-type: text/xml');
}


include "inc/init.php";

$file_data = $db->select("SELECT * FROM `" . MAI_PREFIX . "files` ORDER BY `id` DESC");


echo "<?xml version='1.0' encoding='UTF-8'?>\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
echo '<url>
<loc>' . $set->url . '</loc>
<priority>1.0</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>' . $set->url . '/disclaimer</loc>
<priority>1.0</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>' . $set->url . '/tos</loc>
<priority>1.0</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>' . $set->url . '/usr_set</loc>
<priority>0.90</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>' . $set->url . '/request</loc>
<priority>0.90</priority>
<changefreq>daily</changefreq>
</url>
<url>
<loc>' . $set->url . '/top</loc>
<priority>0.90</priority>
<changefreq>daily</changefreq>
</url>';
if ($file_data) {
	foreach ($file_data as $mydata) {
		if ($mydata->isdir == '1') {
			echo sprintf("
<url>
<loc>$set->url/data/$mydata->id/" . mai_converturl($mydata->name) . "/</loc>
<priority>0.80</priority>
<changefreq>daily</changefreq>
</url>");
		} else {
			echo sprintf("
<url>
<loc>$set->url/data/file/$mydata->id/" . mai_converturl($mydata->name) . "</loc>
<priority>0.80</priority>
<changefreq>daily</changefreq>
</url>");
		}
	}
} else {
}
echo '</urlset>';
