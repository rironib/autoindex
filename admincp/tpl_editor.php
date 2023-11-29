<?php

/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 3.0
Licence: GPL v3
*/
## This is a modified version of Master Autoindex. So all source rights goes to ionutvmi ##

// this is not using a tpl file because it's not meant to be edited by itself


include "../inc/init.php";


$plugins->run_hook("tpl_editor_top");

if (!is_admin()) {
    ob_end_clean();
    header("Location: $set->url");
    exit;
}

$links[] = "<li class='breadcrumb-item'><a href='$set->url/admincp/'>$lang->admincp</a></li>";
$links[] = "<li class='breadcrumb-item active' aria-current='page'>$lang->tpl_editor</li>";

$act = $_GET['act'];

if ($act == 'edit') {

    $file = MAI_ROOT . "/" . MAI_TPL . "/" . $_GET['f'];
    if (!file_exists($file))
        die("File does not exists !");


    $links[count($links) - 1] = "<li class='breadcrumb-item'><a href='$set->url/admincp/tpl_editor.php'>$lang->tpl_editor</a></li>";
    $links[] = "<li class='breadcrumb-item active' aria-current='page'>" . basename($file) . "</li>";

    if ($_POST)
        if (file_put_contents($file, $_POST['data']))
            $content .= "<div class='alert alert-success'>$lang->saved</div>";
        else
            $content .= "<div class='alert alert-danger'>$lang->error</div>";

    $content .= "<form action='#' method='post'>
        <div class='list-group mb-2'>
            <div class='list-group-item fs-5 fw-bold active'>Edit " . basename($file) . "</div>
            <div class='list-group-item'>
                <div class='mb-2'>
                    <textarea class='form-control' rows='5' name='data'>" . htmlentities(file_get_contents($file)) . "</textarea>
                </div>
                <div class='text-center'>
                    <input type='submit' class='btn btn-dark px-4' name='ok' value='$lang->save'>
                </div>
            </div>
        </div>
    </form>";
} else {

    $files = glob(MAI_ROOT . "/" . MAI_TPL . "*.tpl");
    $content .= "<div class='list-group mb-2'>
    <div class='list-group-item fs-5 fw-bold active'>$lang->tpl_editor</div>";
    if ($files)
        foreach ($files as $file)
            $content .= "<div class='list-group-item'>&#187; <a href='?act=edit&f=" . urldecode(basename($file)) . "'>" . basename($file) . "</a> " . convert(filesize($file)) . "</div>";
    $content .= "</div>";
    $content .= "<div class='alert alert-warning'> $lang->tpl_notice</div>";
}


include "../header.php";
$tpl->assign('tpl_editor', $lang->tpl_editor);
$tpl->assign('tpl_notice', $lang->tpl_notice);
$plugins->run_hook("tpl_editor");
echo $content;
$plugins->run_hook("tpl_editor_end");
include "../footer.php";
