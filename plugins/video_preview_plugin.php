<?php
// Video Preview Plugin
// Author: Miraz Mac
$plugins->add_hook("index_files","video_preview");
$plugins->add_hook("file","video_preview2");
function video_preview_info(){
return    array(
"name" => "Video Preview Plugin",
"author" => "Miraz Mac",
"author_site" => "http://mirazmac.info",
"description" => "Video Preview Plugin for Next AutoIndex. Requires ffmpeg installed",
);
}
function video_preview(){
global $icon,$ext,$d;
if(in_array($ext->extension,array("3gp","avi","mp4","wmv","flv")))
$icon = "/show.php?s=".base64_encode($d->path);
}
function video_preview2($value){
global $ext,$icon,$show_icon,$file;
if(in_array($ext->extension,array("3gp","avi","mp4","wmv","flv"))){
$new_icon = "/show.php?s=".base64_encode($file->path);
$show_icon = str_replace($icon,$new_icon,$show_icon);
}
return $value;
}
