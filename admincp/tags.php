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
    header("Location: $set->url");exit;
}
$fid = (int)$_GET['id'];

$links[] = mai_img("arr.gif")." <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif")." <a href='$set->url/index.php'>$lang->file_manager </a>";

require_once('../lib/getid3/getid3.php');
require_once('../lib/getid3/write.php');

if ($_GET['act'] == 'edit') {
    $file = $db->get_row("SELECT * FROM `". MAI_PREFIX ."files` WHERE `id`='$fid'");
    if (!$file) {
        header("Location: $set->url");
        exit;
    } else {
        $mp3_tagformat = 'UTF-8';
        $mp3_handler = new getID3;
        $mp3_handler->setOption(array('encoding'=>$mp3_tagformat));
        $filepath = "..".$file->path;
        $title = "Edit Tags " . $file->name;
        $links[] = mai_img("arr.gif")." <a href='$set->url/data/file/$file->id/".mai_converturl($file->name).".html'>$file->name </a>";
        $links[] = mai_img("arr.gif")." Edit Tags";
        include "../header.php";
        if(file_exists($filepath)) {
            $infos = $mp3_handler->analyze($filepath);
            $tag = mp3tags_get_tags($infos);

            if (isset($_POST['submit'])) {
                if ($_POST['mp3_image_default']) {
                    $_POST['mp3_image_url'] = $set->plugins['image_tag'];
                    $_FILES = null;
                }
                if ($_POST['mp3_image_remove']) {
                    $_POST['mp3_image_url'] = "";
                    $_FILES = null;
                }
                if(mp3tags_writter($filepath, $_POST, $_FILES))
                    echo"<div class='green'>Tags of $file->name edited.</div>";
                else
                    echo"<div class='red'>Failed to write tags!<br></div>";
            }
            else {
                echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?act=edit&id='.$fid.'" enctype="multipart/form-data">
                  <input type="hidden" name="mass" value="1">';
                if($tag['img_data']) {
                    echo '<div class="icon">
                      Cover album -><br>
                      <img src="'.$set->url.'/icon.php?src='.base64_encode(".".$file->path).'" style="max-width:128px;">
                    </div>';
                }
                echo '<div class="content">
                  Song Title: <input size="12" type="text" class="input" name="mp3_title" value="'.($tag['title'] ? htmlentities($tag['title']) : htmlentities($set->plugins['suffix_tag'])).'">
                  </div><div class="content2">
                  Artist: <input size="12" type="text" class="input" name="mp3_artist" value="'.($tag['artist'] ? htmlentities($tag['artist']) : htmlentities($set->plugins['suffix_tag'])).'">
                  </div><div class="content">
                  Album: <input size="12" type="text" class="input" name="mp3_album" value="'.($tag['album'] ? htmlentities($tag['album']) : htmlentities($set->plugins['suffix_tag'])).'">
                  </div><div class="content2">
                  Genre: <input size="12" type="text" class="input" name="mp3_genre" value="'.htmlentities($tag['genre']).'">
                  </div><div class="content">
                  Year:&nbsp;<input size="4" type="text" class="input" mini:hint="phone" name="mp3_year" value="'.($tag['year'] ? htmlentities($tag['year']) : htmlentities($tag2['year'][0])).'">&nbsp;|&nbsp;
                  Track:&nbsp;<input size="3" type="text" mini:hint="phone" class="input" name="mp3_track" value="'.htmlentities($tag['track']).'">
                  </div><div class="content2">
                  Band:&nbsp;<input size="12" type="text" class="input" name="mp3_band" value="'.($tag['band'] ? htmlentities($tag['band']) : htmlentities($set->name)).'">
                  </div><div class="content">
                  Publisher:&nbsp;<input size="12" type="text" class="input" name="mp3_publisher" value="'.($tag['publisher'] ? htmlentities($tag['publisher']) : htmlentities($set->url)).'">
                  </div><div class="content2">
                  Composer:&nbsp;<input size="12" type="text" class="input" name="mp3_composer" value="'.htmlentities($tag['composer']).'">
                  </div><div class="content">
                  Comment (max 25 char):<br>
                  <textarea name="mp3_comment" rows="2">'.($tag['comment'] ? htmlentities($tag['comment']) : htmlentities($set->plugins['comment_tag'])).'</textarea>
                </div>';
                echo '<div class="content">
                  Upload Image (jpg, png, or gif only):<br>
                  <input size="8" type="file" class="input" name="mp3_image_file">
                  </div><div class="content2">
                  Import Image from URL (jpg, png, or gif only):<br>
                  <input size="12" type="text" class="input" name="mp3_image_url" value="">
                </div>';
                if ($set->plugins['image_tag']) {
                    echo '<div class="icon">
                      <img src="'.$set->plugins['image_tag'].'" width="80">&nbsp;
                    <input type="checkbox" name="mp3_image_default" value="1"> Use default image ?</div>';
                }
                echo '<div class="content2">
                  <input type="checkbox" name="mp3_image_remove" value="1"> Remove Image Tags ?
                  </div><div class="content">
                  <input type="submit" name="submit" value="Edit Tags">
                </div></form>';
            }
        } else {
            echo"<div class='red'>File $file->name doesn't exists !<br></div>";
        }
        include "../footer.php";
    }
}
else header('Location: '.$set->url);

?>