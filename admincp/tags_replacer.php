<?php

/****************************************************************
*****************************************************************
** Mp3 Tags Editor Plugin ************************************************
*****************************************************************
** Author           : azkha                                                                                                                                                 **
** Homepage   : www.azkha.com                                                                                                                             **
** Contact:                                                                                                                                                                       **
**     Email         : dirakmoe@gmail.com | azkha.dira@yahoo.com | diera_luv_k-moe@mig33.com          **
**     Facebook : www.fb.com/azkha.dira                                                                                                               **
**     Twitter     : @azkha_dira                                                                                                                                     **
** Description  : This will be show file information like :                                                                                      **
**                          resolution (video or image file),                                                                                                   **
**                          duration (video and audio file)                                                                                                     **
**                          preview list (archive file)                                                                                                              **
**                          mp3 tags like (title, artist, album, etc.)                                                                                      **
**                          and you can edit mp3 tags also.                                                                                                  **
** Setting         : Goto Admin Panel -> Plugin Manager -> Install this plugin                                                     **
**                          change setting for this plugin like                                                                                               **
**                          auto edit tags, show tags, add suffix, comment and cover image tags.                          **
**                          example suffix result like -> Artist : Britney Spears - www.azkha.com                           **
**                          you can add image by upload or import (upload http) as Album cover image (id3v2)  **
** Created at   : September 28, 2012                                                                                                                        **
** Modified at  : April 25, 2013                                                                                                                                    **
*****************************************************************
** Please do not change or remove all of azkha properties..                                                                            **
** if you remodify this file, enter your about here                                                                                               **
** Enjoy ;-)                                                                                                                                                                       **
*****************************************************************
*****************************************************************/

include "../inc/init.php";

if(!is_admin()) {
    header("Location: $set->url");
    exit;
}

$fid = (int)$_GET['id'];

$links[] = mai_img("arr.gif")." <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif")." <a href='$set->url/index.php'>$lang->file_manager </a>";

require_once('../lib/getid3/getid3.php');
require_once('../lib/getid3/write.php');

if (isset($_POST['submit'])) {
    $path = "..".$_POST['path'];
    $ok = "";
    $err = "";
    if (!empty($_POST['target'])) {
        $target = $_POST['target'];
        $replace = $_POST['replace'];
        $dir = @opendir($path);
        while (false !== ($f = @readdir($dir)))
        {
            if ($f == '.' OR $f == '..')
                continue;
            $ext = (object)pathinfo($f);
            $ext = $ext->extension;
            if ($ext != 'mp3')
                continue;

            if(file_exists($path.'/'.$f)) {
                if(mp3tags_replacer($target, $replace, $path.'/'.$f))
                    $ok .= "Tags of $f replaced.<br>";
                else
                    $err .= "Unable to replacing tags of $f !!!<br>";
            }
            else
                $err .= "File $f doesn't exists !!!<br>";
        }
        @closedir($dir);
    }
}
if (isset($_POST['change'])) {
    $path = "..".$_POST['path'];
    $ok = "";
    $err = "";
    if ($_POST['mp3_image_default']) {
        $_POST['mp3_image_url'] = $set->plugins['image_tag'];
        $_FILES = null;
    }
    if ($_POST['mp3_image_remove']) {
        $_POST['mp3_image_url'] = "";
        $_FILES = null;
    }
    $dir = @opendir($path);
    while (false !== ($f = @readdir($dir)))
    {
        if ($f == '.' OR $f == '..')
            continue;
        $ext = (object)pathinfo($f);
        $ext = $ext->extension;
        if ($ext != 'mp3')
            continue;
        if(file_exists($path.'/'.$f)) {
            if(mp3tags_writter($path.'/'.$f, $_POST, $_FILES))
                $ok .= "Tags of $f edited.<br>";
            else
                $err .= "Unable to writing tags of $f !!!<br>";
        }
        else
            $err .= "File $f doesn't exists !!!<br>";
    }
    @closedir($dir);
}
if (isset($_POST['suffix'])) {
    $path = "..".$_POST['path'];
    $ok = "";
    $err = "";
    if ($_POST['mp3_suffix']) {
        $dir = @opendir($path);
        while (false !== ($f = @readdir($dir)))
        {
            if ($f == '.' OR $f == '..')
                continue;
            $ext = (object)pathinfo($f);
            $ext = $ext->extension;
            if ($ext != 'mp3')
                continue;
            if (file_exists($path.'/'.$f)) {
                if (mp3tags_replacer("", "", $path.'/'.$f, $_POST['mp3_suffix']))
                    $ok .= "Suffix added to $f tags.<br>";
                else
                    $err .= "Unable to adding suffix tags of $f !!!<br>";
            }
            else
                $err .= "File $f doesn't exists !!!<br>";
        }
        @closedir($dir);
    } else {
        $err .= "Suffix text form can't be empty !!!<br>";
    }
}

$links[] = mai_img("arr.gif")." Tags Replacer";
$title = ($_GET['act'] == 'change' ? 'Change tags all of mp3 file' : 
  ($_GET['act'] == 'suffix' ? 'Add Suffix to mp3 Tags' : 
  ($_GET['act'] == 'replace' ? 'Replace text tags all of mp3 file' :
  'Mass Mp3Tags Editor'
)));
include "../header.php";

if ($ok)
    echo '<div class="green">'.$ok.'</div>';
if (err)
    echo '<div class="red">'.$err.'</div>';

if ($_GET['act']) {
    echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?act='.$_GET['act'].'" enctype="multipart/form-data">
        <input type="hidden" name="mass" value="'.($_GET['act'] == 'suffix' ? 2 : 1).'">
        <div class="content">
        <select name="path">
    <option value="/files">** SELECT PATH **</option>';
    $all_folders = $db->select("SELECT `name`, `path` FROM `". MAI_PREFIX ."files` WHERE `size` = '0' ORDER BY `path`");
    foreach($all_folders as $folder) {
        echo "<option ".($_POST['path'] == $folder->path ? "selected " : "")."value='$folder->path'>".mp3tags_dirname_list($folder->name, $folder->path)."</option>";
    }
    echo '</select></div><div class="content2">';
    
    if ($_GET['act'] == 'change') {
        echo 'Song Title: <input size="12" type="text" class="input" name="mp3_title" value="'.htmlentities($_POST['mp3_title']).'">
            </div><div class="content">
            Artist: <input size="12" type="text" class="input" name="mp3_artist" value="'.htmlentities($_POST['mp3_artist']).'">
            </div><div class="content2">
            Album: <input size="12" type="text" class="input" name="mp3_album" value="'.htmlentities($_POST['mp3_album']).'">
            </div><div class="content">
            Genre: <input size="12" type="text" class="input" name="mp3_genre" value="'.htmlentities($_POST['mp3_genre']).'">
            </div><div class="content2">
            Year:&nbsp;<input size="4" type="text" class="input" mini:hint="phone" name="mp3_year" value="'.htmlentities($_POST['mp3_year']).'">&nbsp;|&nbsp;
            Track:&nbsp;<input size="3" type="text" mini:hint="phone" class="input" name="mp3_track" value="'.htmlentities($_POST['mp3_track']).'">
            </div><div class="content">
            Band:&nbsp;<input size="12" type="text" class="input" name="mp3_band" value="'.htmlentities($_POST['mp3_band']).'">
            </div><div class="content2">
            Publisher:&nbsp;<input size="12" type="text" class="input" name="mp3_publisher" value="'.htmlentities($_POST['mp3_publisher']).'">
            </div><div class="content">
            Composer:&nbsp;<input size="12" type="text" class="input" name="mp3_composer" value="'.htmlentities($_POST['mp3_composer']).'">
            </div><div class="content2">
            Comment (max 28 char):<br>
            <textarea name="mp3_comment" rows="2">'.(isset($_POST['mp3_comment']) ? htmlentities($_POST['mp3_comment']) : htmlentities($set->plugins['comment_tag'])).'</textarea>
        </div>';
        $img = $set->plugins['image_tag'];
        if($img) {
            echo '<div class="icon">
                Default Cover album -><br>
                <img src="'.$img.'" width="128"><br>
            <input type="checkbox" name="mp3_image_default" value="1"> Use this image ?</div>';
        }
        echo '<div class="content">
            Upload Image (jpg, png, or gif only):<br>
            <input size="8" type="file" class="input" name="mp3_image_file">
            </div><div class="content2">
            Import Image from URL (jpg, png, or gif only):<br>
            <input size="15" type="text" class="input" name="mp3_image_url" value="">
            </div><div class="content">
            <input type="checkbox" name="mp3_image_remove" value="1"> Remove All Image Album ?
            </div><div class="content2">
            <input type="submit" name="change" value="Change Tags">
            </div></form>
            <a href="'.$_SERVER['PHP_SELF'].'?act=suffix"><div class="download">Mass Suffix Adder</div></a>
        <a href="'.$_SERVER['PHP_SELF'].'?act=replace"><div class="download">Text Tags Replacer</div></a>';
    }
    else if ($_GET['act'] == 'suffix') {
        echo 'Suffix Text: <input size="12" type="text" class="input" name="mp3_suffix" value="'.($_POST['mp3_suffix'] ? htmlentities($_POST['mp3_suffix']) : htmlentities($set->plugins['suffix_tag'])).'">
            </div><div class="content">
            <input type="submit" name="suffix" value="Add Suffix">
            </div></form>
            <a href="'.$_SERVER['PHP_SELF'].'?act=replace"><div class="download">Text Tags Replacer</div></a>
        <a href="'.$_SERVER['PHP_SELF'].'?act=change"><div class="download">Mass Tags Editor</div></a>';
    }
    else {
        echo 'Target Text: <input size="12" type="text" class="input" name="target" value="'.htmlentities($_POST['target']).'">
            </div><div class="content">
            Replace Text: <input size="12" type="text" class="input" name="replace" value="'.htmlentities($_POST['replace']).'">
            </div><div class="content2">
            <input type="submit" name="submit" value="Replace Tags">
            </div></form>
            <a href="'.$_SERVER['PHP_SELF'].'?act=suffix"><div class="download">Mass Suffix Adder</div></a>
        <a href="'.$_SERVER['PHP_SELF'].'?act=change"><div class="download">Mass Tags Editor</div></a>';
    }
} else {
    echo '<div class="title">Mass Mp3Tags Replacer & Editor</div>
        <a href="'.$_SERVER['PHP_SELF'].'?act=replace"><div class="download">Text Tags Replacer</div></a>
        <a href="'.$_SERVER['PHP_SELF'].'?act=suffix"><div class="download">Suffix Tags Adder</div></a>
    <a href="'.$_SERVER['PHP_SELF'].'?act=change"><div class="download">Mass Tags Editor</div></a>';
}

include "../footer.php";

?>