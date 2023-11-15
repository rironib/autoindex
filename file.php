<?php
/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 3.2
Licence: GPL v3
*/
## This is a modified version of Master Autoindex. So all source rights goes to ionutvmi ##

include "inc/init.php";

$plugins->run_hook("file_top");


$fid = (int)$_GET['id'];

$file = $db->get_row("SELECT * FROM `" . MAI_PREFIX . "files` WHERE `id`='$fid'");

if (!$file) {
	header("Location: $set->url");
	exit;
}
$in_dir = $file->indir;
$r_file = $db->select("SELECT * FROM `" . MAI_PREFIX . "files` WHERE `indir`='$in_dir'");
$rcount = count($r_file);
$related = '';
if ($rcount > 2) {
	$i = 0; // temp variable
	foreach ($r_file as $r) {
		if (!$r->size == "0") {
			if ($r->id == "$fid") {
				//skip this file
			} else {
				$related .= "<div class='list-group-item'><a href='$set->url/data/file/$r->id/" . mai_converturl($r->name) . "'>$r->name</a>
				<br/>
				[" . convert($r->size) . "] | $r->views Hits
				</div>";
			}
		}
		if (++$i == 5) break;
	}
} else {
	$related .= '<div class="list-group-item">No Related Files!</div>';
}

$title = $file->name;

if (isset($_GET["download"])) {
	$plugins->run_hook("download", null);

	$db->query("UPDATE `" . MAI_PREFIX . "files` SET `dcount` = dcount+1 WHERE `id` = '$fid'");

	if (isset($_GET['jad'])) {
		include 'lib/pclzip.lib.php';
		$l = "." . $file->path;
		$zip = new PclZip($l);
		$content = $zip->extract(PCLZIP_OPT_BY_NAME, 'META-INF/MANIFEST.MF', PCLZIP_OPT_EXTRACT_AS_STRING);
		$filesize = filesize($l);
		header('Content-type: text/vnd.sun.j2me.app-descriptor');
		header('Content-Disposition: attachment; filename="' . basename($l) . '.jad";');

		echo $content[0]['content'] . "\n" . 'MIDlet-Jar-Size: ' . $filesize . "\n" . 'MIDlet-Jar-URL: ' . $set->url . $file->path;
		exit;
	} else {

		if (file_exists("." . $file->path)) {
			header("Location: $set->url" . $file->path);
			exit;
		}
	}
}
$db->query("UPDATE `" . MAI_PREFIX . "files` SET `views` = views+1 WHERE `id` = '$fid'");
$titlex = "$lang->download $file->name";
$name = "<b>" . $lang->name . "</b> : " . $file->name;
$description = "<b>" . $lang->description . "</b> : " . $file->description;
$time = "<b>" . $lang->uploaded_on . "</b> : " . date("D, d M Y", $file->time);
$size = "<b>" . $lang->size . "</b> : " . convert($file->size);
$dloads = "<b>" . $lang->downloads . "</b> : " . $file->dcount;
$views = "<b>" . $lang->views . "</b> : " . $file->views;

$ext = (object)pathinfo($file->path);
$ext->extension = strtolower($ext->extension);
$voice_path = '' . MAI_ROOT . 'voice.mp3';
if ($ext->extension == 'mp3' && file_exists($voice_path) && $file->voice == "0") {
	$new = ltrim($file->path, "/");
	$fpath = '' . MAI_ROOT . '' . $new . '';
	$mp3 = @file_get_contents($fpath);
	$voice = @file_get_contents($voice_path);

	if (file_put_contents($fpath, $mp3 . $voice, LOCK_EX)) {
		//Its done Watson!
		$db->query("UPDATE `" . MAI_PREFIX . "files` SET `voice` = 1 WHERE `id` = '$fid'");
	} else {
		// not done :(
	}
}


if (in_array($ext->extension, array('png', 'jpg', 'jpeg', 'gif', 'jar'))) {
	if ($ext->extension == 'jar')
		$icon = "/icon.php?s=" . base64_encode($file->path);
	else {
		$icon = "/thumb.php?w=128&src=" . base64_encode($file->path);
		$extra_img = "
		<b>$lang->width</b> : 
		<a href='$set->url/thumb.php?w=16&src=" . base64_encode($file->path) . "'>16px</a> / 
		<a href='$set->url/thumb.php?w=32&src=" . base64_encode($file->path) . "'>32px</a> / 
		<a href='$set->url/thumb.php?w=64&src=" . base64_encode($file->path) . "'>64px</a> / 
		<a href='$set->url/thumb.php?w=128&src=" . base64_encode($file->path) . "'>128px</a> / 
		<a href='$set->url/thumb.php?w=256&src=" . base64_encode($file->path) . "'>256px</a> / 
		<a href='$set->url/thumb.php?w=512&src=" . base64_encode($file->path) . "'>512px</a>
		";
	}
} else {
	$all_icons = str_replace(".png", "", array_map("basename", glob(MAI_TPL . "style/png/*.png")));
	if (!in_array($ext->extension, $all_icons))
		$icon = "/" . MAI_TPL . "style/png/file.png";
	else
		$icon = "/" . MAI_TPL . "style/png/$ext->extension.png";
}

if ($file->icon != '')
	$icon = "/thumb.php?ext&w=128&src=" . urlencode($file->icon);

$plugins->run_hook("file_mid");


$show_icon = "<img src='$set->url" . $icon . "' width='128'>";


$download = "<div class='text-center mb-2'><a href='?download' class='btn btn-dark px-4 link-light'>$lang->DOWNLOAD</a></div>";

if ($ext->extension == 'jar') {
	$jarf = $file->name;
	$jarf = str_replace('.jar', '.jad', $jarf);
	$jarf = str_replace('.JAR', '.JAD', $jarf);
	$download .= "<div class='download'><a href='?download&jad' class='btn btn-dark px-4 link-light'>$lang->DOWNLOAD $jarf</a></div>";
}
foreach (explode('/', substr($file->path, 7)) as $dr) {
	if (trim($dr != "")) {
		$_dr .= "/" . $dr;
		$id = $db->get_row("SELECT `id`,`name` FROM `" . MAI_PREFIX . "files` WHERE `path` = '/files" . $db->escape($_dr) . "'");
		$links[] = mai_img("arr.gif") . "&nbsp;<a href='$set->url/data/" . $id->id . "/" . mai_converturl($id->name) . "/'>" . htmlentities($id->name) . "</a>";
	}
}
if (is_admin()) {
	$_admin = "<a href='$set->url/admincp/actions.php?act=edit&id=$file->id'  class='btn btn-primary link-light fw-normal'>$lang->edit</a> <a href='$set->url/admincp/actions.php?act=delete&id=$file->id' class='btn btn-danger link-light fw-normal'>$lang->delete</a>";
}

include "header.php";
$tpl->grab("file.tpl", 'file');
$tpl->assign('titlex', $titlex);
$tpl->assign('name', $name);
$tpl->assign('related', $related);
$tpl->assign('description', $description);
$tpl->assign('size', $size);
$tpl->assign('dloads', $dloads);
$tpl->assign('views', $views);
$tpl->assign('show_icon', $show_icon);
$tpl->assign('time', $time);
$tpl->assign('download', $download);
$tpl->assign('DOWNLOAD', $DOWNLOAD);
$tpl->assign('extra_img', $extra_img);
$tpl->assign('_admin', $_admin);
$tpl->display();

$plugins->run_hook("file_end");

include "footer.php";
