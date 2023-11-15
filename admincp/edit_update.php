<?php

include "../inc/init.php";

if (!is_admin()) {
	header("Location: $set->url");
	exit;
}
$id = (int)$_GET['id'];
if ($_GET['done'] == '1') {
	header("Location: $set->url/admincp/update_manager.php?updated=true");
}
$update = $db->get_row("SELECT * FROM `" . MAI_PREFIX . "updates` WHERE `id`='$id'");
$old = $update->text;
if (!$update) {
	header("Location: $set->url");
	exit;
}
$links[] = mai_img("arr.gif") . " <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif") . " Edit Update ";

include "../header.php";

if ($_POST['u_text']) {
	$u_text = $_POST['u_text'];
	$new = $db->escape($u_text);
	$db->query("UPDATE `" . MAI_PREFIX . "updates` SET `text` = '$new' WHERE `id` = '$id'");
	echo '<div class="green">Update Edited Successfully!</div>';
}

echo '<form method="POST" action="edit_update.php?id=' . $id . '&done=1"><div class="list-group mb-2"><div class="list-group-item fs-5 fw-bold active">Edit Update</div><div class="list-group-item"><div class="mb-2"><textarea class="form-control" name="u_text">' . $old . '</textarea></div><div class="text-center"><input class="btn btn-dark px-4" type="submit" value="Edit"/></div></div></form>';
include "../footer.php";
