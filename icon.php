<?php
/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 1.0
Licence: GPL v3
*/
## This is a modified version of Master Autoindex. So all source rights goes to ionutvmi ##
include "inc/init.php";
include "lib/pclzip.lib.php";

$plugins->run_hook("icon_top");

$file = substr(base64_decode($_GET["s"]),1);
$q = array("icon.png","ico.png","i.png","icono.png","Icon.png","Ico.png","I.png","Icono.png","ICON.png","ICO.png","I.png","ICONO.png","ICON.PNG","ICO.PNG","I.PNG","ICONO.PNG","icons/icon.png","icons/ico.png","icons/i.png","icons/icono.png","i","I","i1.png","AppIcon01.png");	
$zip = new PclZip($file);	
$ar = $zip->extract(PCLZIP_OPT_BY_NAME,$q,PCLZIP_OPT_EXTRACT_IN_OUTPUT);

$plugins->run_hook("icon");
	
if(!empty($ar)) {
header("Content-type: image/png");
}else {
$cz=file_get_contents(MAI_TPL."style/png/jar.png");
header("Content-type: image/png");
echo $cz;
}

$plugins->run_hook("icon_end");