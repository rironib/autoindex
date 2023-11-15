<?php

/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 3.2
Licence: GPL v3
*/

include "../inc/init.php";

$plugins->run_hook("admin_actions_top");


if (!is_admin()) {
	header("Location: $set->url");
	exit;
}
$fid = (int)$_GET['id'];


$links[] = mai_img("arr.gif") . " <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif") . " <a href='$set->url/index.php'>$lang->file_manager </a>";

// add folder
if (isset($_GET['act']) && $_GET['act'] == 'add') {
	$file = $db->get_row("SELECT * FROM `" . MAI_PREFIX . "files` WHERE `id`='$fid'");
	if (!$file) {
		$file = new stdClass(); // PHP 5.4 fix
		$file->path = "/files";
	}
	if (!is_dir(".." . $file->path)) {
		header("Location: " . $set->url);
		exit;
	}

	$plugins->run_hook("admin_actions_add_top");

	if (isset($_POST['name'])) {
		$name = $db->escape($_POST['name']);
		$icon = $db->escape($_POST['icon']);
		$path = $db->escape($file->path . "/" . $name);

		if ($db->count("SELECT `id` FROM `" . MAI_PREFIX . "files` WHERE `path` = '$path'") == 0) {
			$insertData = array(
				'name'   => $name,
				'path'   => $path,
				'icon'   => $icon,
				'indir'  => (int)$_GET['id'],
				'time'   => time(),
				'isdir'  => '1'
			);

			$insertColumns = implode(", ", array_keys($insertData));
			$insertValues = "'" . implode("', '", $insertData) . "'";

			if ($db->insert("INSERT INTO `" . MAI_PREFIX . "files` ($insertColumns) VALUES ($insertValues)")) {
				$newFolderPath = ".." . $file->path . "/" . $name;
				mkdir($newFolderPath, 0777);

				$plugins->run_hook("admin_actions_add");

				$form .= "<div class='alert alert-success'>$lang->added</div>";

				// Fix the redirection URL construction
				$redirectUrl = $set->url . "/data/" . (int)$_GET['id'] . "/" . $name . "/";
				header("Location: " . $redirectUrl);
				exit; // Make sure to exit after the header to prevent further execution
			}
		}
	}

	$links[] = mai_img("arr.gif") . " $lang->add_folder ";

	$form .= "<form action='#' method='post'>
        <div class='list-group mb-2'>
            <div class='list-group-item fs-5 fw-bold active'>$lang->add_folder</div>
            <div class='list-group-item'>
                <div class='d-flex align-items-center'><b>$lang->name</b> <input type='text' class='form-control ms-2' name='name' value='new'></div>
            </div>
            <div class='list-group-item'>
                <div class='d-flex align-items-center'><b>$lang->icon</b> <input type='text' class='form-control ms-2' name='icon'></div>
            </div>
        </div>
        <div class='text-center mb-2'><input type='submit' class='btn btn-dark px-4 mb-2' value='$lang->add'></div>
    </form>";

	$plugins->run_hook("admin_actions_add_end");
}


// edit file & folder
if ($_GET['act'] == 'edit') {
	$file = $db->get_row("SELECT * FROM `" . MAI_PREFIX . "files` WHERE `id`='$fid'");
	if (!$file) {
		header("Location: $set->url");
		exit;
	}
	$plugins->run_hook("admin_actions_edit_top");

	if ($file->size > 0)
		$links[] = mai_img("arr.gif") . " <a href='$set->url/data/file/$file->id/" . mai_converturl($file->name) . "'>$file->name </a>";
	else
		$links[] = mai_img("arr.gif") . " <a href='$set->url/data/$file->id/" . mai_converturl($file->name) . "/'>$file->name </a>";
	if ($_POST['name']) {
		$path = "/files" . $_POST['path'];
		$dirid = $db->get_row("SELECT id FROM `" . MAI_PREFIX . "files` WHERE `path`='" . $path . "'")->id;
		$real_path = $path . "/" . basename($file->path);
		if ($db->query("UPDATE `" . MAI_PREFIX . "files` SET `name`='" . $db->escape($_POST['name']) . "', `icon`='" . $db->escape($_POST['icon']) . "', `indir`='" . $dirid . "', `path`= '" . $db->escape($real_path) . "', `description`='" . $db->escape($_POST['description']) . "' WHERE `id`='$file->id'")) {

			if ($file->path != $real_path) {
				if (is_file(".." . $file->path)) {
					rename(".." . $file->path, ".." . $real_path);
				} else {
					dirmv(".." . $file->path, ".." . $real_path);
					$db->query("UPDATE `" . MAI_PREFIX . "files` SET `path`=replace(`path`,'" . $db->escape($file->path) . "','" . $db->escape($real_path) . "') WHERE `path` LIKE '" . $db->escape($file->path) . "%'");
				}
			}
			$form .= "<div class='alert alert-success'>$lang->saved</div>";
			$file->icon = $_POST['icon']; // to keep it updated
			$file->name = $_POST['name']; // to keep it updated
			$file->path = $real_path; // to keep it updated
			$file->description = $_POST['description']; // to keep it updated
			$plugins->run_hook("admin_actions_edit");
		}
	}
	$links[] = mai_img("arr.gif") . " $lang->edit ";

	$form .= "<form action='#' method='post'>
	<div class='list-group mb-2'>
		<div class='list-group-item fs-5 fw-bold active'>" . htmlentities($file->name, ENT_QUOTES) . "</div>
		<div class='list-group-item'>$lang->name</div>
		<div class='list-group-item'><input type='text' class='form-control' name='name' value='" . htmlentities($file->name, ENT_QUOTES) . "'></div>
		<div class='list-group-item'>$lang->icon</div>
		<div class='list-group-item'><input type='text' class='form-control' name='icon' value='" . htmlentities($file->icon, ENT_QUOTES) . "'></div>
		<div class='list-group-item d-flex align-items-center'><span class='me-2'>$lang->path</span> : <select name='path' class='form-control ms-2'><option value=''>./</option>";
	$all_folders = $db->select("SELECT `path` FROM `" . MAI_PREFIX . "files` WHERE `size` = '0'");

	foreach ($all_folders as $folder) {
		$folder2 = substr($folder->path, 6); // remove /files

		if (dirname($file->path) === $folder->path)
			$selected = " selected='vmi'";
		else
			$selected = '';
		$form .= "<option value='$folder2'$selected>$folder2</option>";
	}

	$form .= "</select>/" . basename($file->path) . "</div>
	<div class='list-group-item'>
		<textarea class='form-control' name='description' placeholder='Description'>" . htmlentities($file->description) . "</textarea>
	</div>
	</div>
	<div class='text-center'>
        <input type='submit' class='btn btn-dark px-4 mb-2' value='$lang->save'>
    </div>
	</form>";
	$plugins->run_hook("admin_actions_edit_end");
}

//delete file & folder
if ($_GET['act'] == 'delete') {
	$file = $db->get_row("SELECT * FROM `" . MAI_PREFIX . "files` WHERE `id`='$fid'");
	if (!$file) {
		header("Location: $set->url");
		exit;
	}
	$plugins->run_hook("admin_actions_delete_top");
	if ($file->size > 0)
		$links[] = mai_img("arr.gif") . " <a href='$set->url/data/file/$file->id/" . mai_converturl($file->name) . "'>$file->name </a>";
	else
		$links[] = mai_img("arr.gif") . " <a href='$set->url/data/$file->id/" . mai_converturl($file->name) . "/'>$file->name </a>";
	$links[] = mai_img("arr.gif") . " $lang->delete ";
	if ($_POST['yes']) {
		if (is_dir(".." . $file->path)) {
			deleteAll(".." . $file->path);
			$db->query("DELETE FROM `" . MAI_PREFIX . "files` WHERE `path` LIKE '$file->path%'");
			$plugins->run_hook("admin_actions_delete_a");
		} else {
			@unlink(".." . $file->path);
			$db->query("DELETE FROM `" . MAI_PREFIX . "files` WHERE `id`='$file->id'");
			$plugins->run_hook("admin_actions_delete_b");
		}
		$form = "<div class='alert alert-success'>$lang->data_gone</div>";
	} else {
		$form .= "<form action='#' method='post'>
			<div class='card mb-2'>
  				<div class='card-body'>
    				<h5 class='card-title'>$lang->are_you_sure</h5>
					<div class='text-end'>
						<input type='submit'  class='btn btn-primary' name='yes' value='$lang->yes'>
						<a class='btn btn-danger text-white fw-normal' href='$set->url'> $lang->no </a>
					</div>
  				</div>
			</div>
		</form>";
	}
	$plugins->run_hook("admin_actions_delete_end");
}


// edit settings.php
if ($_GET['act'] == 'sphp') {
	$plugins->run_hook("admin_actions_editset_top");

	$links[count($links) - 1] = mai_img("arr.gif") . " $lang->config_editor";

	$file = MAI_ROOT . "/inc/settings.php";
	if (!file_exists($file))
		die("File does not exists!");

	if ($_POST)
		if (file_put_contents($file, $_POST['data']))
			$form .= "<div class='alert alert-success'>$lang->saved</div>";
		else
			$form .= "<div class='alert alert-danger'>$lang->error</div>";

	$form .= "<form action='#' method='post'>
					<div class='list-group mb-2'>
						<div class='list-group-item fs-5 fw-bold active'>$lang->config_editor</div>
						<div class='list-group-item'>
							<div class='mb-2'>
                				<textarea class='form-control' name='data'>" . htmlentities(file_get_contents($file)) . "</textarea>
							</div>
							<div class='text-center'>
                    			<input type='submit' class='btn btn-dark px-4' name='ok' value='$lang->save'>
                			</div>
						</div>
					</div>
            </form>
        ";
}


// edit settings
if ($_GET['act'] == 'rtxt') {
	$plugins->run_hook("admin_actions_editset_top");

	$file = MAI_ROOT . "/robots.txt";
	if (!file_exists($file))
		die("File does not exists!");

	$links[count($links) - 1] = mai_img("arr.gif") . " $lang->edit robots.txt";

	if ($_POST)
		if (file_put_contents($file, $_POST['data']))
			$form .= "<div class='alert alert-success'>$lang->saved</div>";
		else
			$form .= "<div class='alert alert-danger'>$lang->error</div>";

	$form .= "<form action='#' method='post'>
					<div class='list-group mb-2'>
					<div class='list-group-item fs-5 fw-bold active'>$lang->edit robots.txt</div>
					<div class='list-group-item'>
						<div class='mb-2'>
                    		<textarea class='form-control' name='data'>" . htmlentities(file_get_contents($file)) . "</textarea>
                		</div>
						<div class='text-center'>
                    		<input type='submit' class='btn btn-dark px-4' name='ok' value='$lang->save'>
                		</div>
					</div>
				</div>
            </form>";

	$plugins->run_hook("admin_actions_editset_end");
}

// change password
if ($_GET['act'] == 'editset') {
	$plugins->run_hook("admin_actions_editset_top");

	$links[count($links) - 1] = mai_img("arr.gif") . " $lang->settings";

	if ($_POST['msg']) {
		if ($_POST['msg']) {
			if (trim($_POST['pass']) != '') {
				$pass = ", `admin_pass` = '" . sha1($_POST['pass']) . "'";
				$_SESSION['adminpass'] = sha1($_POST['pass']);
			}
			if ($db->query("UPDATE `" . MAI_PREFIX . "settings` SET $pass")) {
				$form .= "<div class='alert alert-success'>$lang->saved</div>";
				$plugins->run_hook("admin_actions_editset");
			} else {
				$form .= "<div class='alert alert-danger'>$lang->error</div>";
			}
		}
	} else {
		$form .= "<form action='#' method='post'>
						<div class='list-group mb-2'>
							<div class='list-group-item fs-5 fw-bold active'>$lang->change_password</div>
							<div class='list-group-item d-flex justify-content-center'>
								<input class='form-control me-3' type='password' name='pass'>
								<input type='submit' class='btn btn-dark px-4' value='$lang->save'>
							</div>
							<div class='list-group-item'>
								<span class='badge bg-secondary'>Note</span> $lang->keep_blank
							</div>
						</div>
					</form>";
	}
}


include "../header.php";
$tpl->grab("admin_actions.tpl", "admin_actions");
$tpl->assign("form", $form);
$tpl->display();
$plugins->run_hook("admin_actions_end");
include "../footer.php";
