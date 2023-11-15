<?php
/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 1.0
Licence: GPL v3
*/
## This is a modified version of Master Autoindex. So all source rights goes to ionutvmi ##

function stripslashes_recursive($value)
{
    if (is_array($value)) {
        foreach ($value as $index => $val) {
            $value[$index] = stripslashes_recursive($val);
        }
        return $value;
    } else {
        return stripslashes($value);
    }
}

function remove_magic_quotes()
{
    if (version_compare(PHP_VERSION, '5.4.0', '<') && get_magic_quotes_gpc()) {
        $_GET = stripslashes_recursive($_GET);
        $_POST = stripslashes_recursive($_POST);
    }
}



function mai_img($src, $alt = '')
{
    global $plugins, $set;

    return $plugins->run_hook("mai_img", "<img src='$set->url/" . MAI_TPL . "style/images/$src' alt='$alt'>");
}
function mai_converturl($string)
{
    $string = str_replace(" ", "-", $string);
    $string = str_replace(".", "-", $string);
    $string = str_replace("@", "-", $string);
    $string = str_replace("/", "-", $string);
    $string = str_replace("\\", "-", $string);
    $string = preg_replace("/[^a-zA-Z0-9\-]/", "", $string);
    return $string;
}
function is_admin()
{
    global $set;
    if ($_SESSION['adminpass'] == $set->sinfo->admin_pass)
        return true;
    return false;
}
function get_max_upl()
{
    $max_upload = (int)(ini_get('upload_max_filesize'));
    $max_post = (int)(ini_get('post_max_size'));
    $memory_limit = (int)(ini_get('memory_limit'));
    return min($max_upload, $max_post, $memory_limit);
}
function convert($size)
{
    $unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}
function tsince($t, $arr)
{
    $tt = time() - $t;
    $tp = $arr[0];
    if ($tt >= 60 && $tt < 3600) {
        $tt = floor($tt / 60);
        $tp = $arr[1];
    }
    if ($tt >= 3600 && $tt < 86400) {
        $tt = floor($tt / 3600);
        $tp = $arr[2];
    }
    if ($tt >= 86400 && $tt < 2592000) {
        $tt = floor($tt / 86400);
        if ($tt == '1') {
            $tp = $arr[3];
        } else {
            $tp = $arr[4];
        }
    }
    if ($tt >= 2592000) {
        $tt = floor($tt / 2592000);
        if ($tt == '1') {
            $tp = $arr[5];
        } else {
            $tp = $arr[6];
        }
    }

    return "$tt $tp ";
}
function deleteAll($directory, $empty = false)
{
    if (substr($directory, -1) == "/") {
        $directory = substr($directory, 0, -1);
    }

    if (!file_exists($directory) || !is_dir($directory)) {
        return false;
    } elseif (!is_readable($directory)) {
        return false;
    } else {
        $directoryHandle = opendir($directory);

        while ($contents = readdir($directoryHandle)) {
            if ($contents != '.' && $contents != '..') {
                $path = $directory . "/" . $contents;

                if (is_dir($path)) {
                    deleteAll($path);
                } else {
                    unlink($path);
                }
            }
        }

        closedir($directoryHandle);

        if ($empty == false) {
            if (!rmdir($directory)) {
                return false;
            }
        }

        return true;
    }
}

// remove by value:
function array_remove_value()
{
    $args = func_get_args();
    return array_diff($args[0], array_slice($args, 1));
}

function dirmv($source, $destination)
{
    if (is_dir($source)) {
        @mkdir($destination);
        $directory = dir($source);
        while (FALSE !== ($readdirectory = $directory->read())) {
            if ($readdirectory == '.' || $readdirectory == '..') {
                continue;
            }
            $PathDir = $source . '/' . $readdirectory;
            if (is_dir($PathDir)) {
                dirmv($PathDir, $destination . '/' . $readdirectory);
                continue;
            }
            rename($PathDir, $destination . '/' . $readdirectory);
        }

        $directory->close();
    } else {
        rename($source, $destination);
    }
}
//Function to retrieve content from remote URL
// sample usage: get_version('URL','UA'); 
// Use 0 to Use Desktop User Agent & 1 for Mobile. Leaving it blank will be default to Desktop 
function get_version($url, $ua = '')
{
    if ($ua == '0') {
        $user_agent = "Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0";
    } else {
        $user_agent = "Opera/9.80 (J2ME/MIDP; Opera Mini/4.2.13337/34.818; U; en) Presto/2.8.119 Version/11.10";
    }
    $btext = rand(600, 600000); // ;)
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, '' . $url . '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // access the ssl sites
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.google.com/search?sclient=psy-ab&site=&source=hp&btnG=Search&q=' . $url . ''); // i came from google :p
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, '' . $user_agent . ' ' . $btext . '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: en-us,en;q=0.7,bn-BD,bn;q=0.3', 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5'));
        $content = curl_exec($ch);
        curl_close($ch);
        if (!empty($content)) {
            return $content;
        } else {
            return false;
        }
    } elseif (ini_get('allow_url_fopen')) {
        $header = array('http' => array('user_agent' => '' . $user_agent . ' ' . $btext . ''));
        $context = stream_context_create($header);
        $content = file_get_contents('' . $url . '', false, $context);
        if (!empty($content)) {
            return $content;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
// Time AGo
function ago($ptime)
{
    $estimate_time = time() - $ptime;

    if ($estimate_time < 1) {
        return 'Just Now';
    }

    $condition = array(
        12 * 30 * 24 * 60 * 60  =>  'year',
        30 * 24 * 60 * 60       =>  'month',
        24 * 60 * 60            =>  'day',
        60 * 60                 =>  'hour',
        60                      =>  'minute',
        1                       =>  'second'
    );

    foreach ($condition as $secs => $str) {
        $d = $estimate_time / $secs;

        if ($d >= 1) {
            $r = round($d);
            return '' . $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
        }
    }
}
