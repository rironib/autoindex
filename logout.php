<?php

// logout.php

include "inc/settings.php";
session_start();
unset($_SESSION["adminpass"]);

$path_info = parse_url($set->url);
setcookie("pass", 0, time() - 3600 * 24 * 7, $path_info['path']);

header("Location: " . $_SERVER["HTTP_REFERER"]);

exit();
