<?php
// Ping Sitemap to Search Engines (Google & Bing).

include 'inc/init.php';
$links[] = " » " . " Ping Sitemap";

// Function to ping a search engine using cURL
function pingSearchEngine($engineUrl, $sitemapUrl, $engineName)
{
    $encodedSitemapUrl = urlencode($sitemapUrl);
    $pingUrl = "$engineUrl$encodedSitemapUrl";

    // Initialize cURL session
    $ch = curl_init($pingUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Perform the cURL request
    $data = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo "<div class='list-group mb-2'><div class='list-group-item bg-danger text-white fs-5 fw-bold'>Error</div><div class='list-group-item'>cURL Error for $engineName: " . curl_error($ch) . "</div></div>";
    } else {
        echo "<div class='list-group mb-2'><div class='list-group-item fs-5 fw-bold active'>Response From $engineName</div><div class='list-group-item'>";

        if ($data !== false) {
            echo "<b>Sitemap Notification Received</b>
                <hr class='my-2'>
                Your Sitemap has been successfully added to the list of Sitemaps to crawl.";
        } else {
            echo "Failed to Ping $engineName. Check your network and try again.";
        }

        echo "</div></div>";
    }

    // Close cURL session
    curl_close($ch);
}

include "header.php";

// Sitemap URL
$sitemap_url = "$set->url/sitemap.xml";

// Search Engine URLs
$google = "http://www.google.com/webmasters/sitemaps/ping?sitemap=";
$bing = "http://www.bing.com/webmaster/ping.aspx?siteMap=";

// Ping Google using cURL
pingSearchEngine($google, $sitemap_url, "Google");

// Ping Bing using cURL
pingSearchEngine($bing, $sitemap_url, "Bing");

include "footer.php";
