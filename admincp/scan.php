<?php


@set_time_limit(0);

include "../inc/init.php";

if (!is_admin()) {
    header("Location: $set->url");
    exit;
}

$links[] = "<li class='breadcrumb-item'><a href='$set->url/admincp/'>$lang->admincp</a></li>";
$links[] = "<li class='breadcrumb-item active' aria-current='page'>File Scanner</li>";

$folder_count = 0;
$files_count = 0;


// grab the files


$db_fl = $db->select("SELECT * FROM `" . MAI_PREFIX . "files`");
foreach ($db_fl as $file)
    $db_files[] = $file->path;

$folder_files = scaner("../files");

foreach ($folder_files as $f) {
    $f = rtrim($f, "/");
    if (!in_array($f, $db_files)) {
        $db_files[] = $f;
        if (is_dir(".." . $f)) {
            $dir = $db->get_row("SELECT `id` FROM `" . MAI_PREFIX . "files` WHERE `path`='" . dirname($f) . "'");
            $add = array(
                "path" => $db->escape($f),
                "name" => $db->escape(basename($f)),
                "time" => time(),
                "indir" => (int)$dir->id,
                "size" => 0
            );
            $db->insert_array(MAI_PREFIX . "files", $add);
            $folder_count++;
        } else {
            $dir = $db->get_row("SELECT `id` FROM `" . MAI_PREFIX . "files` WHERE `path`='" . dirname($f) . "'");
            $add = array(
                "path" => $db->escape($f),
                "name" => $db->escape(basename($f)),
                "time" => time(),
                "indir" => (int)$dir->id,
                "size" => filesize(".." . $f)
            );
            $db->insert_array(MAI_PREFIX . "files", $add);
            $files_count++;
        }
    }
}

// check for extra files
$tmp_files = array_map("_rtrim", $folder_files);
$extra = array_diff(array_merge($tmp_files, $db_files), array_intersect($tmp_files, $db_files));

foreach ($extra as $extra)
    $db->query("DELETE FROM `" . MAI_PREFIX . "files` WHERE `path`='" . $db->escape($extra) . "'");


include "../header.php";

echo "<ul class='list-group my-2'>
        <li class='list-group-item fs-5 fw-bold active'>File Scanner</li>
        <li class='list-group-item'>Scan Complete!</li>
        <li class='list-group-item'>New folders: " . $folder_count . "</li>
        <li class='list-group-item'>New files: " . $files_count . "</li>
    </ul>";

include "../footer.php";

// functions
function _rtrim($v)
{
    return rtrim($v, "/");
}

function scaner($path)
{
    static $f_arr;

    @chmod($path, 0777);

    $arr = glob($path . '/*');

    if (is_array($arr)) {
        foreach ($arr as $vv) {

            if (is_dir($vv)) {
                $f_arr[] = substr($vv, 2) . '/';
                scaner($vv);
            } else {
                $f_arr[] = substr($vv, 2);
            }
        }
    }
    return $f_arr;
}
