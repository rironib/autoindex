<?php
include'inc/init.php';
$links[] = mai_img("arr.gif")." Ping SiteMap to Search Engines";

include "header.php";
// SiteMap URL
$sitemap_url = "$set->url/sitemap.xml";
// Search Engine URLs
$google="http://www.google.com/webmasters/sitemaps/ping?sitemap=";
$bing="http://www.bing.com/webmaster/ping.aspx?siteMap=";

 // Lets Ping Them!
$data=miraz_get_contents("$google$sitemap_url");
echo '<div class="title">Response From Google</div><div class="content">';
if($data){
echo '
<b>Sitemap Notification Received</b><br/>
Your Sitemap has been successfully added to our list of Sitemaps to crawl. If this is the first time you are notifying Google about this Sitemap, please add it via <a href="http://www.google.com/webmasters/tools/">http://www.google.com/webmasters/tools/</a> so you can track its status. Please note that we do not add all submitted URLs to our index, and we cannot make any predictions or guarantees about when or if they will appear.</div>';
}
else{
echo'Failed to Ping Google!';
}
echo'</div>';
$data2=miraz_get_contents("$bing$sitemap_url");
echo'<div class="title">Response From Bing</div><div class="content2">';
if($data2){
echo '
Thanks for submitting your Sitemap. Join the <a href="http://bing.com/webmaster">Bing Webmaster Tools</a> to see your Sitemaps status and more reports on how you are doing on Bing.</div>';
}
else{
echo'Failed to Ping Bing!';
}
echo'</div>';
include "footer.php";
?>