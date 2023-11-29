<?php

include "inc/init.php";
$plugins->run_hook("usr_set_top");

$links[] = "<li class='breadcrumb-item active' aria-current='page'>$lang->settings</li>";

if ($_POST['items']) {
	$_SESSION['perp'] = (int)$_POST['items'];
	$form .= "<div class='alert alert-success'>$lang->saved </div>";
}

$form .= "<form action='?' method='post'>
<div class='list-group mb-2'>
    <div class='list-group-item fs-5 fw-bold active'>$lang->elements_per_page</div>
	<div class='list-group-item'>

<div class='input-group'>
	<select class='form-select'  name='items'>";
$items = range(5, 50, 5);
foreach ($items as $item) {
	if ($_SESSION['perp'] == $item)
		$form .= "<option value='$item' selected='1'>$item</option>";
	else
		$form .= "<option value='$item'>$item</option>";
}
$form .= "</select>
<input class='btn btn-secondary' type='submit' value='$lang->save'></input>
</div></div></div>
</form>";


include "header.php";
$tpl->grab("usr_set.tpl", "usr_set");
$tpl->assign("form", $form);
$tpl->display();

$plugins->run_hook("usr_set_end");
include "footer.php";
