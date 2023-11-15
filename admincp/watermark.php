<?php


@set_time_limit(0);

include "../inc/init.php";

$fid = (int)$_GET['video'];

$file = $db->get_row("SELECT * FROM `". MAI_PREFIX ."files` WHERE `id`='$fid'");
$ext = (object)pathinfo($file->path);
$ext->extension = strtolower($ext->extension);
if($ext->extension == 'mp4' || $ext->extension == '3gp' || $ext->extension == 'avi' || $ext->extension == 'mpg' || $ext->extension == 'flv' && is_admin()){
if($file->watermark == "1"){
header('Location: '.$set->url.'');
exit();
}
}
else{
	header("Location: $set->url");
	exit;
}

$links[] = mai_img("arr.gif")." <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif")." Watermark ";
if(!extension_loaded('ffmpeg')){
die('FFMPEG Not Installed!');
}
include "../header.php";
echo"<div class='content'>";
$watermark_path=''.MAI_ROOT.'watermark.png'; // path to watermark file
$vdn=ltrim($file->path, "/");
$vdpath=''.MAI_ROOT.''.$vdn.'';
$temp_dir=''.MAI_ROOT.'temp';
if(!file_exists($temp_dir)){
mkdir($temp_dir,0777);
}
chmod($temp_dir,0777);
$temp=''.MAI_ROOT.'temp/'.$file->name.'';
if(!file_exists($watermark_path)){
echo'No Watermark File Found. Please create a file named <b>watermark.png</b> in <i>Next AutoIndex Root Folder</i>!';
}
else{
echo'<b>Current Watermark:('.$watermark_path.')</b><br/>
<img src="'.$set->url.'/watermark.png"/>
<br/>
Applying to <b>'.$file->path.'</b> 
';
exec('ffmpeg -y -i "'.$vdpath.'" -i "'.$watermark_path.'" -filter_complex "overlay" "'.$temp.'"');
if(copy($temp, $vdpath)){
@unlink($temp);
echo'<br/>Watermarked Video Successfully!';
// Update the database
$db->query("UPDATE `". MAI_PREFIX ."files` SET `watermark` = 1 WHERE `id` = '$fid'");
}
else{
echo'Unable to Watermark!';
}

}
echo"</div>";
include "../footer.php";