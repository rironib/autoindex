<?php
/**
* Apk preview Plugin - This will allow preview on apk files
* author: ionutvmi
* 15 Dec 2012
*/

$plugins->add_hook("icon_top","apk_get");
$plugins->add_hook("index_files","apk_preview");
$plugins->add_hook("file","apk_preview2");

function apk_info(){
    return    array(    
    "name" => "Apk Preview Plugin",
    "author" => "ionutvmi",
    "author_site" => "http://master-land.net",
    "description" => "This will allow preview on apk files.",
    );
}


function apk_preview(){
    global $icon,$ext,$d;
    if(in_array($ext->extension,array("apk")))
        $icon = "/icon.php?s=".base64_encode($d->path);
    
}

function apk_preview2($value){
    global $ext,$icon,$show_icon,$file;
    if(in_array($ext->extension,array("apk"))){
        $new_icon = "/icon.php?s=".base64_encode($file->path);
        $show_icon = str_replace($icon,$new_icon,$show_icon);
    }
    

    return $value;
}


function apk_get(){
	global $ext;
	$file = MAI_ROOT.substr(base64_decode($_GET["s"]),1);
	$info = (object)pathinfo($file);
	if(!in_array($info->extension,array("apk")))
		return true;
	
	$zip = new PclZip($file);
	
	if ($zip->extract(PCLZIP_OPT_BY_PREG, "/png$/", PCLZIP_OPT_EXTRACT_IN_OUTPUT))
		header('Content-Type: image/png'); 
		else {
		header("Content-type: image/png");
		echo file_get_contents(MAI_TPL."style/png/file.png");
}

}