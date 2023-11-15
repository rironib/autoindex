<?php


$file = $_GET['file'];
if($file){

$filename = str_replace('.jar', $_GET['ke'], basename($file));


$content = file_get_contents($file);
header('Content-type: application/octet-stream');
header('Content-disposition: inline; filename = '.$filename);
header('Content-Length: '.strlen($content));
echo $content;

} else {

?>
<?php
/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 1.0
Licence: GPL v3
*/
## This is a modified version of Master Autoindex. So all source rights goes to ionutvmi ##

include "inc/init.php";

$links[] = mai_img("arr.gif")." Rename File";

include "header.php";
?>
<body>
<div class="title"><b>Rename File</b></div><div class="content"><form>
Insert URL:<br/>
<input class="input" name="file" value="http://"><br/>Rename to:<br> <select name="ke">
<option value="_jar">_jar</option>
<option value=".zip">.zip</option>
<option value=".mp3">.mp3</option>
</select><p>
<input class="btn btnC" type="submit" value="Rename">
</div></form>

<?php
include "footer.php";
} ?>
