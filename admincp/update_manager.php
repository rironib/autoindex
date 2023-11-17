<?php

include "../inc/init.php";
include '' . MAI_ROOT . 'lib/pagination.class.php';

if (!is_admin()) {
	header("Location: $set->url");
	exit;
}

$links[] = mai_img("arr.gif") . " <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif") . " Update Manager";
$act = $_GET['act'];

include "../header.php";

echo '<form method="POST" action="update_manager.php?act=add">
		<div class="list-group mb-2">
   			<div class="list-group-item fs-5 fw-bold active">Update Manager</div>
			<div class="list-group-item">
				<div class="mb-2">
					<textarea id="updateText" class="form-control" placeholder="Write your update here..." name="u_text" style="height: 100px"></textarea>
				</div>
				<div class="text-center">
					<input type="button" id="clear" class="btn btn-dark px-4" value="Clear" onclick="clearTextarea()">
					<input type="button" id="paste" class="btn btn-dark px-4" value="Paste" onclick="pasteTextarea()">
					<input type="submit" class="btn btn-dark px-4" value="Add">
				</div>
			</div>
			<div class="list-group-item d-flex align-items-center">
				<input type="text" id="demo" class="form-control me-2" value="» &lt;a href=&quot;&quot;&gt;&lt;/a&gt; was added!">
				<input type="button" class="btn btn-dark" onclick="copyToClipboard()" value="Copy">
        	</div>
		</div>
	</form>
	
	<script>
		// Clear Textarea
		function clearTextarea() {
        	var textarea = document.getElementById("updateText");
        	textarea.value = "";
    	}
		//Paste to Textarea
        function pasteTextarea() {
            var textarea = document.getElementById("updateText");
            navigator.clipboard.readText()
                .then((clipboardText) => {
                    textarea.value = clipboardText;
                })
                .catch((error) => {
                    console.error("Error reading from clipboard: ", error);
                });
        }
		// Copy to CLipboard
        function copyToClipboard() {
            var copyText = document.getElementById("demo");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
        }
    </script>';
$msg = '';
switch ($act) {
	case 'del':
		if ($_GET['id']) {

			$id = (int)$_GET['id'];

			$update = $db->get_row("SELECT * FROM `" . MAI_PREFIX . "updates` WHERE `id`='$id'");
			if (!$update) {
				$msg .= '<div class="red">Unable to delete the update. As, It doesn\'t exist!</div>';
			} else {
				$msg .= '<div class="green">Update has been Deleted!</div>';
				$db->query("DELETE FROM " . MAI_PREFIX . "updates WHERE `id` = '$id'");
			}
		}
		break;
	case 'add':
		if ($_POST['u_text']) {
			$u_text = $_POST['u_text'];
			$add = array(
				"text" => $db->escape($u_text),
				"time" => time()
			);
			$db->insert_array(MAI_PREFIX . "updates", $add);
			$msg .= '<div class="alert alert-success">Update Added Successfully!</div>';
		} else {
		}
		break;
}

echo $msg;

$total_results = $db->count("SELECT `id` FROM `" . MAI_PREFIX . "updates`");
if ($total_results > 0) {
	echo "<div class='list-group mb-2'>
		<div class='list-group-item fs-5 fw-bold active'>Latest Updates</div>";
	$perpage = 10;
	$page = (int)$_GET['page'] == 0 ? 1 : (int)$_GET['page'];
	if ($page > ceil($total_results / $perpage)) $page = ceil($total_results / $perpage);
	$start = ($page - 1) * $perpage;

	$s_pages = new pag($total_results, $page, $perpage);

	$show_pages = "<span class='page-link text-dark' href='#'>$lang->pages : </span>" . $s_pages->pages;

	$data = $db->select("SELECT * FROM `" . MAI_PREFIX . "updates` ORDER BY id DESC LIMIT $start,$perpage");
	foreach ($data as $d) {
		echo "<div class='list-group-item'><div class='d-flex justify-content-between align-items-center'>" . htmlspecialchars($d->text) . "<span><a class='btn btn-dark text-light fw-normal mx-1' href='$set->url/admincp/edit_update.php?id=$d->id'>EDIT</a> <a class='btn btn-danger text-light fw-normal mx-1' href='$set->url/admincp/update_manager.php?act=del&id=$d->id'>DELETE</a></span></div><hr style='margin: 0.5rem 0;'><small><b>Date: </b>" . ago($d->time) . "</small>
</div>
";
	}
	echo "</div>";
	echo "<div class='pagination justify-content-center m-2'>" . $show_pages . "</div>";
}

include "../footer.php";
