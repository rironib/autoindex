<?php

//Mp3 Tag Editor & Reader
//Author:azkha  

if (!defined("MAI_PREFIX"))
    die("You can't access the plugin directly !");

$plugins->add_hook("file","mp3tags_show");
$plugins->add_hook("index_files","mp3tags_icon");
$plugins->add_hook("icon_top","mp3tags_image");
$plugins->add_hook("icon_top","mp3tags_view_image");
$plugins->add_hook("admin_import_form","mp3tags_import");
$plugins->add_hook("admin_upload_form","mp3tags_upload");
$plugins->add_hook("admin_import_form_mid","mp3tags_edit");
$plugins->add_hook("admin_upload_form_mid","mp3tags_edit");
$plugins->add_hook("admin_options","mp3tags_mass_editor");

function mp3tags_editor_info() {
    return array(
      "name" => "Read and Edit Mp3 Tags  Plugin <b style='color:red'>v2</b>",
      "author" => "azkha",
      "author_site" => "http://facebook.com/azkha.dira",
      "description" => "This will be show file information like resolution, playtime (on video or image file), preview list of archive file and show tags  of mp3 files like (title, artist, album, etc.) and you can edit thats tags also (only mp3).",
    );
}

function mp3tags_editor_install(){
    global $db, $set;
    $settings_data = array(
      "name" => "edit_tags",
      "value" => "1",
      "title" => "Auto edit tags:",
      "description" => "Do you want to auto edit tags when you upload or import?",
      "type" => "yesno",
      "plugin" => "mp3tags_editor",
    );
    $settings_data2 = array(
      "name" => "suffix_tag",
      "value" => " - ".strtoupper($_SERVER['HTTP_HOST']),
      "title" => "Insert a tag suffix:",
      "description" => "the suffix of the artist and album tags. Result like -> Britney Spears - azkha.com",
      "type" => "textarea",
      "plugin" => "mp3tags_editor",
    );
    $settings_data3 = array(
      "name" => "image_tag",
      "value" => "$set->url/mp3cover.jpg",
      "title" => "Image URL:",
      "description" => "This for change album cover image tags.",
      "type" => "textarea",
      "plugin" => "mp3tags_editor",
    );
    $settings_data4 = array(
      "name" => "comment_tag",
      "value" => "Download from http://".$_SERVER['HTTP_HOST'],
      "title" => "Tag Comment:",
      "description" => "Enter your tag comment max 25 characters.",
      "type" => "textarea",
      "plugin" => "mp3tags_editor",
    );
    $settings_data5 = array(
      "name" => "show_tags",
      "value" => "1",
      "title" => "Show Tags:",
      "description" => "Show tags of mp3 file in download page?",
      "type" => "yesno",
      "plugin" => "mp3tags_editor",
    );
    $db->insert_array(MAI_PREFIX."plugins_settings",$settings_data);
    $db->insert_array(MAI_PREFIX."plugins_settings",$settings_data2);
    $db->insert_array(MAI_PREFIX."plugins_settings",$settings_data3);
    $db->insert_array(MAI_PREFIX."plugins_settings",$settings_data4);
    $db->insert_array(MAI_PREFIX."plugins_settings",$settings_data5);
}

function mp3tags_editor_is_installed(){
    global $db;
    if($db->count("SELECT `name` FROM `".MAI_PREFIX."plugins_settings` WHERE `plugin`='mp3tags_editor'") > 0)
        return true;
    return false;
}

function mp3tags_editor_uninstall(){
    global $db;
    $db->query("DELETE FROM `".MAI_PREFIX."plugins_settings` WHERE `plugin`='mp3tags_editor'");
}

// edit - auto edit tags
function mp3tags_edit() {
    global $set, $path, $ext, $f, $_name, $_POST, $_FILES;
    $_name = ($_POST['f'] ? $_name : $f);
    $filepath = $path.'/'.$_name;
    
    if($set->plugins['edit_tags'] == 1) {
        if(file_exists($filepath) AND $ext->extension == 'mp3') {
            include_once "../lib/getid3/getid3.php";
            include_once "../lib/getid3/write.php";
            mp3tags_writter($filepath, $_POST, $_FILES);
        }
    }
}

// show tags
function mp3tags_show($value) {
    global $set, $lang, $icon, $show_icon, $file, $ext;
    if ($file AND $set->plugins['show_tags'] == 1 AND file_exists(".".$file->path)) {
        include_once "./lib/getid3/getid3.php";
        $mp3_tagformat = 'UTF-8';
        $filepath = ".".$file->path;
        $mp3Tags = new getID3;
        $mp3Tags->setOption(array('encoding'=>$mp3_tagformat, 'tempdir'=>'./temp/'));
        $tagInfo = $mp3Tags->analyze($filepath);
        $tags = mp3tags_get_tags($tagInfo);
        
        $othersInfo = "<div class='title2'>$lang->tags_info </div>";
        $mp3tagsInfo = "";
        
        if ($tags['title'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_title : ".htmlentities($tags['title'])."</div>";
        if ($tags['artist'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_artist : ".htmlentities($tags['artist'])."</div>";
        if ($tags['album'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_album : ".htmlentities($tags['album'])."</div>";
        if ($tags['genre'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_genre : ".htmlentities($tags['genre'])."</div>";
        if ($tags['year'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_year : ".(int)$tags['year']."</div>";
        if ($tags['track'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_track : ".htmlentities($tags['track'])."</div>";
        if ($tags['band'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_band : ".htmlentities($tags['band'])."</div>";
        if ($tags['publisher'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_publisher : ".htmlentities($tags['publisher'])."</div>";
        if ($tags['composer'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_composer : ".htmlentities($tags['composer'])."</div>";
        if ($tags['comment'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_comment : ".htmlentities($tags['comment'])."</div>";
        if ($tags['playtime_string'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_playtime : ".htmlentities($tags['playtime_string'])."</div>";
        if ($tags['kbps'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_bitrate : ".$tags['kbps']." kbps</div>";
        if ($tags['audio_mode'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_channel : ".htmlentities($tags['audio_mode'])."</div>";
        if ($tags['width'] AND $tags['height'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_resolution : ".$tags['width']." x ".$tags['height']."</div>";
        if ($tags['file_format'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_fileformat : ".htmlentities($tags['file_format'])."</div>";
        if ($tags['mime_type'])
            $mp3tagsInfo .= "<div class='content".(++$i % 2 == 0 ? 2 : "")."'>$lang->tag_mime : ".htmlentities($tags['mime_type'])."</div>";
        
        if ($ext->extension == "mp3") {
            if ($file->icon == "") {
                $new_icon = "/icon.php?src=".base64_encode($filepath);
                $show_icon = str_replace($icon, $new_icon, $show_icon);
            }
            if (is_admin()) {
                $adm = " | <a href='$set->url/admincp/tags.php?act=edit&id=$file->id'>$lang->edit Tags</a>";
                $value = str_replace("{\$_admin}", "{\$_admin}".$adm, $value);
            }
            if ($mp3tagsInfo)
                $value = str_replace("{\$extra_img}", $othersInfo.$mp3tagsInfo."{\$extra_img}", $value);
        }
        else if ($tags['zip'] OR $tags['tar']) {
            if (isset($_GET['preview'])) {
                include "lib/pagination.class.php";
                if ($tags['zip']) {
                    include_once("lib/pclzip.lib.php");
                    $zip = new PclZip($filepath);
                    $lists = $zip->listContent();
                }
                else {
                    include "lib/PEAR.php";
                    include "lib/tar.php";
                    $zip = new Archive_Tar($filepath);
                    $lists = $zip->listContent();
                }
                if ($_GET['name']) {
                    $name = base64_decode($_GET['name']);
                    $cInfo = (object)pathinfo($name);
                    $cExt = strtolower($cInfo->extension);
                    $zipInfo = "<div class='title'>Preview <span style='color:blue;'>$name</span> from <span style='color:green;'>$file->name</span></div>";
                    $zipInfo .= "<a href='$set->url/icon.php?f=".base64_encode($filepath)."&n=".$_GET['name']."'><div class='download'>$lang->download ".basename($name)."</div></a>";
                    if (in_array($cExt, array("jpg", "jpeg", "png", "gif", "bmp", "ico"))) {
                        $zipInfo .= "<div class='icon'><img src='$set->url/icon.php?f=".base64_encode($filepath)."&n=".$_GET['name']."' style='max-width: 128px;'></div>";
                    }
                    else if (in_array($cExt, array("php", "phps", "php5", "bak", "html", "htm", "xhtml", "js", "xml", "tpl", "java", "css", "txt", "dat", "mf")) OR preg_match('/\.htaccess/', $name)) {
                        if ($tags['zip']) {
                            $zipExtract = $zip->extract(PCLZIP_OPT_BY_NAME, $name, PCLZIP_OPT_EXTRACT_AS_STRING);
                            $str = @$zipExtract[0]['content'];
                        }
                        else {
                            $str = $zip->extractInString($name);
                        }
                        if ($str) {
                            if ($cExt == "php") {
                                $zipInfo .= "<div class='content'>".highlight_string($str, true)."</div>";
                            }
                            else {
                                $hlgstr = highlight_string("<?php\n".$str."\n?>", true);
                                $hstr = preg_replace('#(<span[^>]+>)(&lt;|<)\?php#is', "$1", $hlgstr);
                                $hstr = preg_replace('#<span[^>]+>\?(&gt;|>)</span>#is', "", $hstr);
                                $zipInfo .= "<div class='content'>$hstr</div>";
                            }
                            $zipInfo .= "<a href='$set->url/icon.php?f=".base64_encode($filepath)."&n=".$_GET['name']."'><div class='download'>$lang->download ".basename($name)."</div></a>";
                        }
                        else
                            $zipInfo .= "<div class='red'>$name $lang->empty_file</div>";
                    }
                    else {
                        $zipInfo .= "<div class='red'>$lang->unsupported</div>";
                    }
                    $zipInfo .= "<a href='?download'><div class='download'>$lang->download $file->name</div></a>";
                }
                else {
                    $total_results = count($lists);
                    $perpage = $_SESSION['perp'] ? (int)$_SESSION['perp'] : $set->perpage;
                    $page = (int)$_GET['page'] == 0 ? 1 : (int)$_GET['page'];
                    if ($page > ceil($total_results/$perpage))
                        $page = ceil($total_results/$perpage);
                    $start = ($page-1)*$perpage;
                    $end = $page * $perpage;
                    $s_pages = new pag($total_results,$page,$perpage);
                    $show_pages = $lang->pages.": ".$s_pages->pages;
                    
                    $zipInfo = "<div class='title'>List File of <span style='color:green;'>$file->name</span></div>";
                    for ($z = $start; $z < $end; $z++) {
                        if ($lists[$z])
                            $zipInfo .= "<div class='content".(++$x%2==0 ? 2 : '')."'>".mp3tags_zip_listname($lists[$z])."</div>";
                    }
                    $zipInfo .= $show_pages;
                }
                $zipInfo .= "<div class='title'><= <a href='$set->url/data/file/$file->id/".mai_converturl($file->name).".html'>$lang->back_to $file->name</a></div>";
                $value = $zipInfo;
            }
            else {
                $mp3tagsInfo .= "<div class='content'><a href='$set->url/data/file/$file->id/".mai_converturl($file->name).".html?preview'>Preview List</a></div>";
                $value = str_replace("{\$extra_img}", $mp3tagsInfo."{\$extra_img}", $value);
            }
        }
        else {
            if ($mp3tagsInfo)
                $value = str_replace("{\$extra_img}", $mp3tagsInfo."{\$extra_img}", $value);
        }
    }
    return $value;
}

// icon - Index files icon
function mp3tags_icon() {
    global $lang, $icon, $d, $_admin, $ext;
    if($ext->extension == 'mp3') {
        if($d->icon == "")
            $icon = "/icon.php?src=".base64_encode(".".$d->path);
    }
}

// image - Generate album picture
function mp3tags_image() {
    if (isset($_GET['src'])) {
        $file = base64_decode($_GET['src']);
        $info = (object)pathinfo($file);
        $info->extension = strtolower($info->extension);

        if ($info->extension == "mp3") {
            $def_type = (isset($_GET['dir']) ? 'folder' : 'mp3');
            $def_img = @file_get_contents("./".MAI_TPL."style/png/".$def_type.".png");
            include_once "./lib/getid3/getid3.php";
            if (file_exists($file)) {
                $mp3Tags = new getID3;
                $mp3Tags->setOption(array('encoding'=>$mp3_tagformat));
                $tagInfo = $mp3Tags->analyze($file);
                $tags = mp3tags_get_tags($tagInfo);
                if ($tags['img_data']) {
                    header('Content-Type: image/jpeg');
                    echo $tags['img_data'];
                    exit;
                } else {
                    header('Content-Type: image/png');
                    echo $def_img;
                    exit;
                }
            } else {
                header('Content-Type: image/png');
                echo $def_img;
                exit;
            }
        }
    }
}

// view_image
function mp3tags_view_image() {
    if ($_GET['f']) {
        $file = base64_decode($_GET['f']);
        $name = base64_decode($_GET['n']);
        $filename = basename($name);
        $info = (object)pathinfo($name);
        $ext = strtolower($info->extension);

        if (file_exists($file)) {
            include_once "lib/getid3/getid3.php";
            $mp3Tags = new getID3;
            $mp3Tags->setOption(array('encoding'=>$mp3_tagformat));
            $tagInfo = $mp3Tags->analyze($file);
            $tags = mp3tags_get_tags($tagInfo);
            
            if ($tags['zip']) {
                include_once "lib/pclzip.lib.php";
                $zip = new PclZip($file);
                $content = $zip->extract(PCLZIP_OPT_BY_NAME, $name, PCLZIP_OPT_EXTRACT_AS_STRING);
                $result = @$content[0]['content'];
            }
            if ($tags['tar']) {
                include "lib/PEAR.php";
                include "lib/tar.php";
                $archive = new Archive_Tar($file);
                $result = $archive->extractInString($name);
            }
            
            if (!$result) {
                if (file_exists(MAI_TPL."style/png/$ext.png"))
                    $cz = @file_get_contents(MAI_TPL."style/png/$ext.png");
                else
                    $cz = @file_get_contents(MAI_TPL."style/png/file.png");
                header("Content-type: image/png");
                echo $cz;
                exit;
            }
            if (in_array($ext, array("jpg", "jpeg", "png", "gif", "ico", "bmp"))) {
                $mimeType = str_replace(array("jpg", "ico"), array("jpeg","x-icon"), $ext);
                header("Content-type: image/$mimeType");
            }
            else {
                header("Content-Type: application/octet-stream");
            }
            header("Content-Disposition: inline; filename=$filename");
            header("Content-Length: ".strlen($result));
            echo $result;
            exit;
        }
        else {
            if (file_exists(MAI_TPL."style/png/$ext.png"))
                $cz = @file_get_contents(MAI_TPL."style/png/$ext.png");
            else
                $cz = @file_get_contents(MAI_TPL."style/png/file.png");
            header("Content-type: image/png");
            echo $cz;
            exit;
        }
    }
}

// zip_listname
function mp3tags_zip_listname($listname) {
    $result = "";
    if (is_array($listname)) {
        $name = $listname['filename'];
        $dir = (int)$listname['folder'];
        $size = (int)$listname['size'];
        $result = "";
        if ($dir == 1) {
            $desc = " [DIR]";
        }
        else {
            if ($size > 1024)
                $desc = " [".round(((int)$size/1024), 2)." KB]";
            else
                $desc = " [$size Bytes]";
            $name = "<a href='?preview&name=".base64_encode($name)."'>$name</a>";
        }
        if ($name)
            return "<span style='color:blue;'>$name</span>$desc";
    }
    return "<span style='color:blue;'><a href='$set->url/icon.php?f=$filepath&n=$listname'>$listname</a></span>";
}

// nolink - replace link
function mp3tags_nolink($text) {
    $text = preg_replace('#(\-|\#|\@)\s*(.+)\.([\w\d]{2, 4})#is', '', $text);
    $text = preg_replace('#http://(www\.)?(.+)#is', '', $text);
    $text = preg_replace('#(www|wap|m|blog)\.(.+)#is', '', $text);
    return $text;
}

// spacer
function mp3tags_spacer($txt) {
    $txt=str_replace('_', ' ', $txt);
    return $txt;
}

// dirname_list - all path name on db
function mp3tags_dirname_list($dirname, $dirpath) {
    $path = substr($dirpath, 0, 7) == "/files/" ? preg_replace('#^/files/(.*?)#is',"$1",$dirpath) : $dirpath;
    $expath = @explode("/", $path);
    $x = count($expath);
    if ($x == 1)
        $listDirName = $dirname;
    else {
        $prefix = "";
        for ($i = 1; $i < $x; $i++) {
            $prefix .= "-";
        }
        $listDirName = $prefix . " " . $dirname;
    }
    return $listDirName;
}

// mass_editor - add link in admin panel
function mp3tags_mass_editor($value) {
    global $lang;
    $value = str_replace("</div>", "<br/>{\$mark} <a href='{\$url}/admincp/tags_replacer.php'>Mass Mp3Tags Editor </a></div>", $value);
    return $value;
}

// writter
function mp3tags_writter($filename, $__POST = null, $__FILES = null, $in_path = "..") {
    global $set;
    $mp3_tagformat = 'UTF-8';
    $mp3_handler = new getID3;
    $mp3_handler->setOption(array('encoding'=>$mp3_tagformat));
    $info = $mp3_handler->analyze($filename);
    $infos = mp3tags_get_tags($info);
    $exp = @explode(' - ', basename($filename));
    $art = $exp[0];
    $exps = @explode('.mp3', $exp[1]);
    $ttl = $exps[0];
    $suffix = ($__POST['mass'] == 1 ? '' : ($__POST['mp3_suffix'] ? $__POST['mp3_suffix'] : $set->plugins['suffix_tag']));
    $mp3_data['title'][] = (!empty($__POST['mp3_title']) ? trim($__POST['mp3_title']) : 
      ($infos['title'] ? mp3tags_nolink($infos['title']) : mp3tags_spacer($ttl)).$suffix);
    $mp3_artist = (!empty($__POST['mp3_artist']) ? trim($__POST['mp3_artist']) : 
      ($infos['artist'] ? mp3tags_nolink($infos['artist']) : mp3tags_spacer($art)).$suffix);
    $mp3_data['artist'][] = $mp3_artist;
    $mp3_data['album'][] = (!empty($__POST['mp3_album']) ? trim($__POST['mp3_album']) : 
      ($infos['album'] ? mp3tags_nolink($infos['album']) : '').$suffix);
    $mp3_data['tracknumber'][] = (!empty($__POST['mp3_track']) ? trim($__POST['mp3_track']) : $infos['track']);
    $mp3_data['comment'][] = (!empty($__POST['mp3_comment']) ? trim($__POST['mp3_comment']) : 
      ($set->plugins['comment_tag'] ? $set->plugins['comment_tag'] : mp3tags_nolink($infos['comment'])));
    $mp3_data['genre'][] = (!empty($__POST['mp3_genre']) ? trim($__POST['mp3_genre']) : mp3tags_nolink($infos['genre']));
    $mp3_data['year'][] = (!empty($__POST['mp3_year']) ? (int)$__POST['mp3_year'] : (int)$infos['year']);
    $mp3_data['band'][] = (!empty($__POST['mp3_band']) ? trim($__POST['mp3_band']) : 
      ($infos['band'] ? mp3tags_nolink($infos['band']).$suffix : $mp3_artist));
    $mp3_data['composer'][] = (!empty($__POST['mp3_composer']) ? trim($__POST['mp3_composer']) : mp3tags_nolink($infos['composer']).$suffix);
    $mp3_data['publisher'][] = (!empty($__POST['mp3_publisher']) ? trim($__POST['mp3_publisher']) : 
      ($infos['publisher'] ? mp3tags_nolink($infos['publisher']).$suffix : 
      ($__POST['mass'] == 1 ? '' : $set->url)));
    $cover_img_def = ($__POST['mass'] == 1 ? '' : str_replace($set->url, $in_path, $set->plugins['image_tag']));
    $cover_img_url = (empty($__POST['mp3_image_url']) ? '' : str_replace($set->url, $in_path, trim($__POST['mp3_image_url'])));

    if (file_exists($__FILES['mp3_image_file']['tmp_name']) OR ($cover_img_url AND !empty($cover_img_url))) {
        if (file_exists($__FILES['mp3_image_file']['tmp_name'])) {
            $udata = @file_get_contents($__FILES['mp3_image_file']['tmp_name']);
            $uinfo = (object)pathinfo($__FILES['mp3_image_file']['name']);
            $utype = $uinfo->extension ? $uinfo->extension : 'jpg';
        }
        else {
            $udata = @file_get_contents($cover_img_url);
            $uinfo = (object)pathinfo($cover_img_url);
            $utype = $uinfo->extension;
        }
        $umime = 'image/'.str_replace('jpg', 'jpeg', $utype);
        $uname = basename($filename).".".$utype;
        
        if ($__POST['mp3_image_remove'] != 1 AND strlen($udata) > 1024) {
            if (in_array($utype, array("jpg","jpeg","png","gif"))) {
                $mp3_data['attached_picture'][0]['data'] = $udata;
                $mp3_data['attached_picture'][0]['picturetypeid'] = $utype;
                $mp3_data['attached_picture'][0]['description'] = $uname;
                $mp3_data['attached_picture'][0]['mime'] = $umime;
            }
        }
    }
    else {
        if ($cover_img_def AND $__POST['mp3_image_def'] == 1) {
            $ddata = @file_get_contents($cover_img_def);
            $dinfo = (object)pathinfo($cover_img_def);
            $dtype = strtolower($dinfo->extension);
        }
        else {
            $ddata = $infos['img_data'];
            $dinfo = (object)pathinfo($infos['img_name']);
            $dtype = $dinfo->extension ? (in_array(strtolower($dinfo->extension), array("jpg","jpeg","png","gif")) ? strtolower($dinfo->extension) : 'jpg') : 'jpg';
        }
        $dmime = 'image/'.str_replace('jpg', 'jpeg', $dtype);
        $dname = basename($filename).".".$dtype;
        
        if ($__POST['mp3_image_remove'] != 1 AND strlen($ddata) > 1024) {
            if (in_array($dtype, array("jpg","jpeg","png","gif"))) {
                $mp3_data['attached_picture'][0]['data'] = $ddata;
                $mp3_data['attached_picture'][0]['picturetypeid'] = $dtype;
                $mp3_data['attached_picture'][0]['description'] = $dname;
                $mp3_data['attached_picture'][0]['mime'] = $dmime;
            }
        }
    }

    $writeTags = new getid3_writetags;
    $writeTags->filename = $filename;
    $writeTags->tagformats = array('id3v1', 'id3v2.3');
    $writeTags->overwrite_tags = true;
    //$writeTags->remove_other_tags = false;
    $writeTags->tag_encoding = $mp3_tagformat;
    $writeTags->tag_data = $mp3_data;

    if ($writeTags->WriteTags())
        return true;
    else
        return false;
}

// replacer
function mp3tags_replacer($target, $replace, $filename, $suffix = '') {
    $mp3_handler = new getID3;
    $mp3_handler->setOption(array('encoding'=>'UTF-8'));
    $infos = $mp3_handler->analyze($filename);
    getid3_lib::CopyTagsToComments($infos);
    $tag = mp3tags_get_tags($infos);

    $mp3_writter = new getid3_writetags;
    $mp3_writter->filename = $filename;
    $mp3_writter->tagformats = array('id3v1', 'id3v2.3');
    $mp3_writter->overwrite_tags = true;
    $mp3_writter->tag_encoding = 'UTF-8';
    //$mp3_writter->remove_other_tags = false;

    $mp3_data['title'][] = str_replace($target, $replace, $tag['title']).$suffix;
    $mp3_data['artist'][]  = str_replace($target, $replace, $tag['artist']).$suffix;
    $mp3_data['album'][]   = str_replace($target, $replace, $tag['album']).$suffix;
    $mp3_data['tracknumber'][] = (int)$tag['track'];
    $mp3_data['comment'][] = str_replace($target, $replace, $tag['comment']);
    $mp3_data['genre'][] = $tag['genre'];
    $mp3_data['year'][] = (int)$tag['year'];
    $mp3_data['composer'][] = str_replace($target, $replace, $tag['composer']);
    $mp3_data['publisher'][] = str_replace($target, $replace, $tag['publisher']);
    $mp3_data['band'][] = str_replace($target, $replace, $tag['band']).$suffix;
                
    if ($tag['img_data']) {
        $mp3_data['attached_picture'][0]['data'] = $tag['img_data'];
        $mp3_data['attached_picture'][0]['picturetypeid'] = $tag['img_id'];
        $mp3_data['attached_picture'][0]['description'] = $tag['img_name'];
        $mp3_data['attached_picture'][0]['mime'] = $tag['img_mime'];
    }
    $mp3_writter->tag_data = $mp3_data;

    if($mp3_writter->WriteTags())
        return true;
    else
        return false;
}

// get_tags - easy get tags info
function mp3tags_get_tags($tagInfo) {
    global $set;
    $result = array();
    $audio = $tagInfo['audio'];
    $video = $tagInfo['video'];
    $tags = $tagInfo['tags'];
    $id3v1 = $tagInfo['id3v1'];
    $id3v2 = $tagInfo['id3v2'];
    $img1 = $id3v2['APIC'][0];
    $img2 = $tagInfo['comments']['picture'][0];

    $result['title'] = ($id3v1['title'] ? $id3v1['title'] :
      ($tags['id3v1']['title'][0] ? $tags['id3v1']['title'][0] :
      ($tags['id3v2']['title'][0] ? $tags['id3v2']['title'][0] : "")));
    $result['artist'] = ($id3v1['artist'] ? $id3v1['artist'] :
      ($tags['id3v1']['artist'][0] ? $tags['id3v1']['artist'][0] :
      ($tags['id3v2']['artist'][0] ? $tags['id3v2']['artist'][0] : "")));
    $result['album'] = ($id3v1['album'] ? $id3v1['album'] :
      ($tags['id3v1']['album'][0] ? $tags['id3v1']['album'][0] :
      ($tags['id3v2']['album'][0] ? $tags['id3v2']['album'][0] : "")));
    $result['genre'] = ($id3v1['genre'] ? $id3v1['genre'] :
      ($tags['id3v1']['genre'][0] ? $tags['id3v1']['genre'][0] :
      ($tags['id3v2']['genre'][0] ? $tags['id3v2']['genre'][0] : "")));
    $result['year'] = ($id3v1['year'] ? $id3v1['year'] :
      ($tags['id3v1']['year'][0] ? $tags['id3v1']['year'][0] :
      ($tags['id3v2']['year'][0] ? $tags['id3v2']['year'][0] : "")));
    $result['track'] = ($id3v1['track'] ? $id3v1['track'] :
      ($tags['id3v1']['track'][0] ? $tags['id3v1']['track'][0] :
      ($tags['id3v2']['track'][0] ? $tags['id3v2']['track'][0] : "")));
    $result['comment'] = ($id3v1['comment'] ? $id3v1['comment'] :
      ($tags['id3v1']['comment'][0] ? $tags['id3v1']['comment'][0] :
      ($tags['id3v2']['comment'][0] ? $tags['id3v2']['comment'][0] : "")));
    $result['band'] = $tags['id3v2']['band'][0];
    $result['publisher'] = $tags['id3v2']['publisher'][0];
    $result['composer'] = $tags['id3v2']['composer'][0];
    $result['playtime'] = $tagInfo['playtime_seconds'];
    $result['playtime_string'] = $tagInfo['playtime_string'];
    $result['bitrate'] = $tagInfo['bitrate'];
    $result['kbps'] = ceil($tagInfo['bitrate']/1000);
    $result['audio_mode'] = $audio['channelmode'];
    $result['audio_format'] = $audio['dataformat'];
    $result['frame_rate'] = $video['frame_rate'];
    $result['video_format'] = $video['dataformat'];
    $result['width'] = $video['resolution_x'];
    $result['height'] = $video['resolution_y'];
    $result['file_format'] = $tagInfo['fileformat'];
    $result['mime_type'] = $tagInfo['mime_type'];
    $result['img'] = ($img1['data'] ? $img1 : $img2);
    $result['img_data'] = ($img1['data'] ? $img1['data'] : $img2['data']);
    $result['img_id'] = $img1['picturetypeid'];
    $result['img_type'] = $img1['picturetype'];
    $result['img_name'] = ($img1['description'] ? $img1['description'] : $set->name.'.jpg');
    $result['img_mime'] = ($img1['mime'] ? $img1['mime'] : $img2['image_mime']);
    $result['zip'] = $tagInfo['zip']['files'];
    $result['tar'] = $tagInfo['tar']['files'];
    return $result;
}

// import
function mp3tags_import($value){
    global $lang;
    $link = "";
    if (isset($_GET['mp3'])) {
        $link .= 'mp3&';
        $import_img_form = "Cover image FILE:<br><input type='file' name='mp3_image_file'><br>";
        $import_img_form .= "Cover image URL:<br><input size='10' type='text' name='mp3_image_url'><br>";
        $import_img_form .= "<input type='checkbox' name='mp3_image_def' value='1' checked='azkha'> Replace cover image with default image ?<br>";
        $value = str_replace("<input type='submit'", $import_img_form."<input type='submit'", $value);
        $upText = "All Imports";
    }
    else {
        $upLink = "mp3";
        $upText = "Import MP3";
    }
    $judul = $_GET['judul'] ? htmlspecialchars(urldecode($_GET['judul'])) : "";
    $link .= "judul=".urlencode($judul);
    $value = str_replace("<form action='?'", " | <a href='?$upLink'>$upText</a><br/><form enctype='multipart/form-data' action='?$link'", $value);
    $value = str_replace("name='n[]'", "name='n[]' value='$judul'", $value);
    return $value;
}

// upload
function mp3tags_upload($value){
    global $lang;
    $link = "";
    if (isset($_GET['mp3'])) {
        $upload_img_form = "Cover image FILE:<br><input type='file' name='mp3_image_file'><br>";
        $upload_img_form .= "Cover image URL:<br><input size='10' type='text' name='mp3_image_url'><br>";
        $upload_img_form .= "<input type='checkbox' name='mp3_image_def' value='1' checked='azkha'> Replace cover image with default image ?<br>";
        $value = str_replace("<input type='submit'", $upload_img_form."<input type='submit'", $value);
        $upText = "All Uploads";
        $formLink = "mp3";
    }
    else {
        $upLink = "mp3";
        $upText = "Upload MP3";
    }
    $value = str_replace("<form action='?'", " | <a href='?$upLink'>$upText</a><br/><form action='?$formLink'", $value);
    return $value;
}

