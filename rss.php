<?php

// RSS Feed for Next AutoIndex
// Added in version 3.0

// Check for the "rss.php" in the URL and redirect to "feed"
if (preg_match("#rss.php#s", $_SERVER['REQUEST_URI'])) {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: feed.rss');
    exit;
} else {
    header('Content-Type: application/xml; charset=utf-8');
}

// Include initialization file
include "inc/init.php";

// Fetch file data from the database
$file_data = $db->select("SELECT * FROM `" . MAI_PREFIX . "files` ORDER BY `id` DESC LIMIT 0,10");
$file_data2 = $db->select("SELECT * FROM `" . MAI_PREFIX . "files` ORDER BY `id` DESC LIMIT 0,1");

// Output XML header
echo '<?xml version="1.0" encoding="utf-8"?>';
echo "\n<!-- Generated by NextAutoIndex Reborn-->\n";
?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:feedburner="http://rssnamespace.org/feedburner/ext/1.0">
    <channel>
        <title><?php echo htmlspecialchars($set->name); ?></title>
        <link><?php echo htmlspecialchars($set->url); ?></link>
        <description>
            <![CDATA[This is the official content feed of <?php echo htmlspecialchars($set->name); ?>. Subscribe to get regular updates.]]>
        </description>
        <lastBuildDate><?php
                        $lastBuildDate = ($file_data2) ? date("D, d M Y H:i:s T", $file_data2[0]->time) : date("D, d M Y H:i:s T");
                        echo htmlspecialchars($lastBuildDate);
                        ?></lastBuildDate>
        <language>en-US</language>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>1</sy:updateFrequency>
        <generator>NextAutoIndex Pro</generator>
        <?php
        if ($file_data) {
            foreach ($file_data as $mydata) {
                if ($mydata->isdir == '1') {
                    continue; // Skip directories
                }
        ?>
                <item>
                    <title><?php echo htmlspecialchars($mydata->name); ?></title>
                    <link><?php echo htmlspecialchars("$set->url/data/file/$mydata->id/" . mai_converturl($mydata->name)); ?></link>
                    <pubDate><?php echo htmlspecialchars(date("D, d M Y H:i:s T", $mydata->time)); ?></pubDate>
                    <description>
                        <![CDATA[<?php
                                    echo (!empty($mydata->description)) ? htmlspecialchars($mydata->description) : htmlspecialchars("$set->name has just uploaded this new file $mydata->name.");
                                    ?>]]>
                    </description>
                </item>
        <?php
            }
        } else {
            echo '<!--No Content Added Yet!-->';
        }
        ?>
    </channel>
</rss>