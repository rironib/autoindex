<?php
// WaterMark Thumb plugin
// master autoindex
// author: Sahzada (sanjay sharma)
// Site: azubawap.asia
error_reporting(0);
header('Content-type: image/jpeg');
header('Cache-Control: public');
header('Pragma: cache');

$font = 'sahzada.ttf';
$sitename = 'AZUBAWAP'; //chage sitename
$W = 55;
$H = 55;

$file = substr(base64_decode($_GET["s"]),1);

$pic = urldecode(htmlspecialchars($file));
if(substr($pic,0,1) != '.'){

$type = strtolower(pathinfo($pic, PATHINFO_EXTENSION));

if($type == 'gif'){$old = imageCreateFromGif($pic);}
elseif($type == 'jpg' || $type == 'jpeg' || $type == 'jpe'){$old = imageCreateFromJpeg($pic);}
elseif($type == 'png'){$old = imageCreateFromPNG($pic);}
else{
exit;
}

list($wn,$hn) = getimagesize($pic);


$new = imageCreateTrueColor($W, $H);
imageCopyResampled($new, $old, 0, 0, 0, 0, $W, $H, $wn, $hn);
$bar = imageCreateTrueColor(10, 53);
imageCopyResized($new, $bar, 44, 1, 0, 0, 10, 53, $W, $H);

$TextColor = imagecolorallocate($new, 255, 255, 255);

ImageTTFtext($new, 10, 90, 52, 52, $TextColor, $font, $sitename);

imageJpeg($new,null,100);
imagedestroy($new);
}

?>