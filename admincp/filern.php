<?php
/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 1.0
Licence: GPL v3
*/
## This is a modified version of Master Autoindex. So all source rights goes to ionutvmi ##
@set_time_limit(0);

include "../inc/init.php";

if (!is_admin()) {
	header("Location: $set->url");
	exit;
}

$links[] = " » " . " <a href='index.php'>$lang->admincp </a>";
$links[] = " » " . " Mass File Renamer ";


if ($_POST) {

	$content .= "<div class='alert alert-success'>" . frename($_POST['path']) . "</div>";
}

$content .= "<form action='#' method='post'><div class='list-group mb-2'>
<div class='list-group-item fs-5 fw-bold active'>Mass File Renamer</div>
<div class='list-group-item bg-secondary-subtle fw-bold'>Folder :</div>
<div class='list-group-item'><select class='form-control' name='path'><option value=''>./</option>";
$all_folders = $db->select("SELECT `path` FROM `" . MAI_PREFIX . "files` WHERE `size` = '0'");

foreach ($all_folders as $folder) {
	$folder = substr($folder->path, 6); // remove /files

	$content .= "<option value='$folder'>$folder</option>";
}
$content .= "</select></div>
<div class='list-group-item bg-secondary-subtle fw-bold'>Select Rule :</div>
<div class='list-group-item'><input class='form-control' type='text' name='rule' placeholder='*.png'></div>
<div class='list-group-item bg-secondary-subtle fw-bold'>Replace :</div>
<div class='list-group-item'><input class='form-control' type='text' name='r' value=''><span class='p-2'>=></span><input class='form-control' type='text' name='w' value=''></div>
<div class='list-group-item bg-secondary-subtle fw-bold'>Prefix :</div>
<div class='list-group-item'><input class='form-control' type='text' name='prefix' value=''></div>
<div class='list-group-item bg-secondary-subtle fw-bold'>Sufix : (It will be added before the file extension)</div>
<div class='list-group-item'><input class='form-control' type='text' name='sname' value='$set->name'>
</div>
</div>

<div class='text-center mb-2'><input type='submit' class='btn btn-dark px-4' value='Rename'></div>
</form>

<div class='alert alert-info' role='alert'>Use this tool very carefully!</div>";


include "../header.php";

echo $content;

include "../footer.php";

function frename($path)
{
	global $db;
	$files = glob("../files" . $path . "/" . $_POST['rule']);
	foreach ($files as $file) {
		if (is_file($file)) {
			$info = (object)pathinfo($file);
			$new_name = $_POST['prefix'] . str_replace($_POST['r'], $_POST['w'], basename($file, "." . $info->extension)) . $_POST['sname'] . "." . $info->extension;
			$new_path = dirname($file) . "/" . $new_name;
			rename($file, $new_path);
			$db->query("UPDATE `" . MAI_PREFIX . "files` SET `path`='" . substr($new_path, 2) . "', `name`= '$new_name' WHERE `path`='" . substr($file, 2) . "'");
			$zzz .= $new_name . " SAVED ! <br/>";
		}
	}
	return $zzz;
}
