<?php

include "../inc/init.php";

if(!is_admin()) {
    header("Location: $set->url");exit;
}
$id = (int)$_GET['id'];
if($_GET['done'] == '1'){
header("Location: $set->url/admincp/update_manager.php?updated=true");
}
$update = $db->get_row("SELECT * FROM `". MAI_PREFIX ."updates` WHERE `id`='$id'");
$old=$update->text;
if(!$update) {
	header("Location: $set->url");
	exit;
}
$links[] = mai_img("arr.gif")." <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif")." Edit Update ";

include "../header.php";

echo '<div class="title">Edit Update</div>';
if($_POST['u_text']){
$u_text=$_POST['u_text'];
$new=$db->escape($u_text);
$db->query("UPDATE `". MAI_PREFIX ."updates` SET `text` = '$new' WHERE `id` = '$id'");
 echo'<div class="green">Update Edited Successfully!</div>';
}
echo'<div class="content"><form method="POST" action="edit_update.php?id='.$id.'&done=1">
<textarea name="u_text">'.$old.'</textarea>
<br/>
<input type="submit" value="Edit"/>
</form>
</div>';
include "../footer.php";

?>