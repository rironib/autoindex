<?php
// WaterMark Thumb plugin
// master autoindex
// author: Sahzada (sanjay sharma)
// Site: azubawap.asia

/**
* Watermark Plugin - This will  add the wartermark imageTTFtext genrator in your image thumb 
* author: Sahzada
* 10 Nov 2012
*/

$plugins->add_hook("index_files","watermark_preview");
$plugins->add_hook("file","watermark_preview2");

function watermark_info(){
    return    array(    
    "name" => "Watermark Plugin",
    "author" => "Sahzada (Sanjay)",
    "author_site" => "http://azubawap.asia",
    "description" => "This will allow imageTTFtext watermark on images files(requires gd lib installed)",
    );
}


function watermark_preview(){
    global $icon,$ext,$d;
    if(in_array($ext->extension,array("gif","jpg","png")))
        $icon = "/icon3.php?s=".base64_encode($d->path);
    
}

function watermark_preview2($value){
    global $ext,$icon,$show_icon,$file;
    if(in_array($ext->extension,array("gif","jpg","png"))){
        $new_icon = "/icon3.php?s=".base64_encode($file->path);
        $show_icon = str_replace($icon,$new_icon,$show_icon);
    }
    

    return $value;

}

