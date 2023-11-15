<?php
// Video Preview Plugin
// Requires ffmpeg
// Author: Miraz Mac
error_reporting(0);
@ini_set('display_errors', 0);
header('Content-type: image/gif');
header('Cache-Control: public');
header('Pragma: cache');
include "inc/init.php";
$W = 200;
$H = 150;
$xfile = substr(base64_decode($_GET["s"]),1);
$file=''.MAI_ROOT.''.$xfile.'';
$pic = urldecode($file[0]);
$media = new ffmpeg_movie($file);
$k_frame=intval($media->getFrameCount());
$wn=$media->GetFrameWidth();
$hn=$media->GetFrameHeight();
$frame = $media->getFrame(30);
if ($frame)
{
$gd = $frame->toGDImage();
$new = imageCreateTrueColor($W, $H);
imageCopyResized($new, $gd, 0, 0, 0, 0, $W, $H, $wn, $hn);

imageGif($new,null,100);
}
?>
