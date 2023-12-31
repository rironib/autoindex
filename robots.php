<?php

if (preg_match("#robots.php#s", $_SERVER['REQUEST_URI'])) {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: robots.txt');
    exit;
} else {
    header('Content-Type: text/plain; charset=utf-8');
}

include "inc/init.php";

$bots = [
    'Googlebot',
    'Googlebot-Image',
    'Googlebot-Mobile',
    'MSNBot',
    'PSBot',
    'Slurp',
    'Yahoo-MMCrawler',
    'yahoo-blogs/v3_9',
    'teoma',
    'Scrubby',
    'ia_archiver',
    'Gigabot',
    'Robozilla',
    'Nutch',
    'baiduspider',
    'naverbot',
    'yeti',
    '*'
];

echo "#Generated by NextAutoIndex Reborn\n\n";

foreach ($bots as $bot) {
    echo "User-agent: $bot\n";
    echo "Disallow:\n";
    echo "Disallow: /cgi-bin/\n";
    echo "Disallow: /admincp/\n";
    echo "Disallow: /?sort=1\n";
    echo "Disallow: /?sort=2\n";
    echo "Disallow: /?sort=3\n";
    echo "Disallow: /?sort=6\n\n\n";
}

echo "Sitemap: {$set->url}/sitemap.xml\n";
