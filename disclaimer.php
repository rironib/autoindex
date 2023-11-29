<?php
/*
Project Name: Next Auto Index
Project URI: http://wapindex.mirazmac.info
Project Version: 1.0
Licence: GPL v3
*/

include "inc/init.php";

$links[] = "<li class='breadcrumb-item active' aria-current='page'>Disclaimer</li>";

include "header.php";

echo "
<div class='list-group mb-2'>
    <div class='list-group-item fs-5 fw-bold active'>Disclaimer</div>
	<div class='list-group-item'><img src='$set->url/tpl/style/images/gdir.gif'/>&nbsp; Please read the disclaimer before download anything from <b>$set->name</b></div>
    <div class='list-group-item'><img src='$set->url/tpl/style/images/gdir.gif'/>&nbsp; <b>$set->name</b> is a promotional website only, All files placed here are for introducing purpose only.</div>
    <div class='list-group-item'><img src='$set->url/tpl/style/images/gdir.gif'/>&nbsp; Please, buy original contents from author or developer site!</div>
    <div class='list-group-item'><img src='$set->url/tpl/style/images/gdir.gif'/>&nbsp; If you do not agree to all the terms, please disconnect from this site now itself.</div>
    <div class='list-group-item'><img src='$set->url/tpl/style/images/gdir.gif'/>&nbsp; By remaining at this site, you affirm your understanding and compliance of the above disclaimer and absolve this site of any responsibility henceforth</div>
    <div class='list-group-item'><img src='$set->url/tpl/style/images/gdir.gif'/>&nbsp; All files found on this site have been collected from various sources across the web and are believed to be in the 'public domain'.</div>
    <div class='list-group-item'><img src='$set->url/tpl/style/images/gdir.gif'/>&nbsp; All the logos and stuff are the property of their respective owners</div>
    <div class='list-group-item'><img src='$set->url/tpl/style/images/gdir.gif'/>&nbsp; If you are the rightful owner of any contents posted here, and object to them being displayed or If you are one of representativities of copy rights department and you dont like our conditions of store, Please Contact Us. We will remove it in 24 hour!</div>
    <div class='list-group-item'><img src='$set->url/tpl/style/images/gdir.gif'/>&nbsp; Download files at your own risk!!! </div>
</div>";

include "footer.php";
