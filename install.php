<!DOCTYPE HTML>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Installation Wizard</title>
	<link rel="shortcut icon" href="./tpl/style/favicon.png" type="images/x-png" />
	<link rel="stylesheet" href="./tpl/style/css/style.css" type="text/css" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
	<nav class="navbar bg-dark d-flex justify-content-center">
		<div class="fs-3 fw-bold text-white">
			Installation Wizard
		</div>
	</nav>

	<div class="container py-2">
		<div class="list-group mb-2">
			<div class="list-group-item fs-5 fw-bold active">Server Configuration</div>
			<div class="list-group-item bg-primary-subtle">
				PHP Version: <?php
								$php = phpversion();
								echo "<b>$php</b> ";
								if ($php > 5.0) {
									echo '<span class="badge bg-success">GOOD</span>';
								} else {
									echo '<span class="badge bg-danger">BAD</span>';
								}
								?>
			</div>
			<small class="list-group-item"><span class="badge bg-secondary">Note</span> PHP version should be 5.x or higher</small>
			<div class="list-group-item bg-primary-subtle">
				cURL Library: <?php

								if (function_exists('curl_version')) {
									echo '<b>Found</b> <span class="badge bg-success">GOOD</span>';
								} else {
									echo '<b>Not Found</b> <span class="badge bg-danger">BAD</span>';
								}
								?>
			</div>
			<small class="list-group-item"><span class="badge bg-secondary">Note</span> If cURL library not installed allow_url_fopen should be <em>on</em></small>
			<div class="list-group-item bg-primary-subtle">
				allow_url_fopen: <?php

									if (ini_get('allow_url_fopen')) {
										echo '<b>On</b> <span class="badge bg-success">GOOD</span>';
									} else {
										echo '<b>Off</b> <span class="badge bg-danger">BAD</span>';
									}
									?>
			</div>
			<small class="list-group-item"><span class="badge bg-secondary">Note</span> Required if cURL is not installed</small>
			<div class="list-group-item bg-primary-subtle">
				GD Library: <?php

							if (extension_loaded('gd') && function_exists('gd_info')) {
								echo '<b>Found</b> <span class="badge bg-success">GOOD</span>';
							} else {
								echo '<b>Not Found</b> <span class="badge bg-danger">BAD</span>';
							}
							?>
			</div>
			<small class="list-group-item"><span class="badge bg-secondary">Note</span> Required for image watermarking</small>
		</div>


		<?php
		if ($_POST) {

			$fp = fopen('inc/settings.php', 'w');

			$content = '
<?php
$set->db_name = "' . $_POST['name'] . '";
$set->db_user = "' . $_POST['user'] . '";
$set->db_host = "' . $_POST['host'] . '";
$set->db_pass = "' . $_POST['pass'] . '";
$set->name = "' . $_POST['site_name'] . '"; // site name
$set->url = "' . $_POST['site_url'] . '"; // site url
$set->logo = "' . $_POST['site_logo'] . '"; // logo url
$set->perpage = "10"; // how many records per page
define("MAI_PREFIX","' . $_POST['prefix'] . '");
						';

			if (!fwrite($fp, trim($content)));

			fclose($fp);

			include "inc/settings.php";

			include "lib/mysql.class.php";

			$db = new dbConn($set->db_host, $set->db_user, $set->db_pass, $set->db_name);

			if (!$db->query("CREATE TABLE IF NOT EXISTS `" . $_POST['prefix'] . "files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `path` text NOT NULL,
  `indir` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL,
  `dcount` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `icon` text NOT NULL,
  `description` text NOT NULL,
  `isdir` int(11) NOT NULL,
  `voice` int(11) NOT NULL DEFAULT '0',
  `watermark` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"));

			if (!$db->query("INSERT INTO `" . $_POST['prefix'] . "files` (`id`, `name`, `path`, `indir`, `views`, `dcount`, `time`, `size`, `icon`, `isdir`) VALUES
(1, 'New folder', '/files/New folder', 0, 0, 0, 1348259936, 0, '', 1), 
(2, '6828.jpg', '/files/New folder/6828.jpg', 1, 0, 0, 1348259936, 183086, '', 0), 
(3, '4171388.png', '/files/4171388.png', 0, 0, 0, 1348259936, 14854, '', 0);"));

			if (!$db->query("CREATE TABLE IF NOT EXISTS `" . $_POST['prefix'] . "plugins_settings` (
  `name` varchar(200) NOT NULL,
  `value` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `type` text NOT NULL,
  `plugin` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;"));

			if (!$db->query("INSERT INTO `" . $_POST['prefix'] . "plugins_settings` (`name`, `value`, `title`, `description`, `type`, `plugin`) VALUES
('top_sort', 'views', 'Sort Top Files By', 'The top files will be sorted by this criteria', 'select \nviews=Views Number \ndcount=Downloads Number', 'top'),
('top_number', '20', 'Top Files No', 'The number of files to be displayed, keep in mind the there in so pagination', 'text', 'top'),
('top_sort_type', 'DESC', 'Sort files', 'Order the files should be sorted <b>ASC</b> or <b>DESC</b>', 'select \nASC=ASC \nDESC=DESC', 'top');"));


			if (!$db->query("CREATE TABLE IF NOT EXISTS `" . $_POST['prefix'] . "updates` (
  `id` int(11) NOT NULL auto_increment,
  `text` text NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;"));

			if (!$db->query("CREATE TABLE IF NOT EXISTS `" . $_POST['prefix'] . "request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `reply` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"));

			if (!$db->query("CREATE TABLE IF NOT EXISTS `" . $_POST['prefix'] . "settings` (
	`admin_pass` varchar(100) NOT NULL,
	`main_msg` text NOT NULL,
	`active_plugins` text NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;"));

			$adminPassword = hash('sha256', trim($_POST['admin_pass']));
			$mainMsg = 'Welcome to our site !\r\nHope you enjoy it :D';
			$activePlugins = 'a:0:{}';

			if (!$db->query("INSERT INTO `" . $_POST['prefix'] . "settings` (`admin_pass`, `main_msg`, `active_plugins`) VALUES ('$adminPassword', '$mainMsg', '$activePlugins')"));


			// 			if (!$db->query("INSERT INTO `" . $_POST['prefix'] . "settings` (`admin_pass`, `main_msg`, `active_plugins`) VALUES
			//   ('" . sha1(trim($_POST['admin_pass'])) . "', 'Welcome to our site !\r\nHope you enjoy it :D', 'a:0:{}');"));

			if (!$db->query("CREATE TABLE IF NOT EXISTS `" . $_POST['prefix'] . "files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"));
			if ($error) {
				echo "<div class='alert alert-danger my-2'>Fatal Error!<br/> Check if <b>/inc/settings.php</b> is writable and if your prefix is correct.</div>";
			} else {
				echo "<div class='list-group my-2'><div class='list-group-item bg-success fw-bold text-center text-white'>Installation Completed!</div><div class='list-group-item bg-success-subtle text-center'>Installation completed successfully. Your site is ready to use now.</div><div class='list-group-item bg-warning-subtle text-center'>Please delete install.php file from server right now!</div></div>";
			}
		} else {

			@chmod("files", 0777);
			@chmod("inc/settings.php", 0666);

			echo "<form action='?' method='post'>
				<div class='list-group mb-2'>
					<div class='list-group-item fs-5 fw-bold active'>Installation Wizard</div>
					<div class='list-group-item fw-bold bg-primary-subtle'>Database Host</div>
					<div class='list-group-item'>
						<input type='text' class='form-control' name='host' value='localhost'>
					</div>
					<small class='list-group-item bg-secondary-subtle'><span class='badge bg-secondary'>Note</span> Insert your MySQL Database Host. Usually its <em>localhost</em> or <em>mysql.xxxx.ext</em>.</small>
					<div class='list-group-item fw-bold bg-primary-subtle'>Database User</div>
					<div class='list-group-item'>
						<input type='text' class='form-control' name='user' value=''>
					</div>
					<small class='list-group-item bg-secondary-subtle'><span class='badge bg-secondary'>Note</span> Insert your database username</small>
					<div class='list-group-item fw-bold bg-primary-subtle'>Database Password</div>
					<div class='list-group-item'>
						<input type='text' class='form-control' name='pass' placeholder=''>
					</div>
					<small class='list-group-item bg-secondary-subtle'><span class='badge bg-secondary'>Note</span> Insert your database password</small>
					<div class='list-group-item fw-bold bg-primary-subtle'>Database Name</div>
					<div class='list-group-item'>
						<input type='text' class='form-control' name='name' value=''>
					</div>
					<small class='list-group-item bg-secondary-subtle'><span class='badge bg-secondary'>Note</span> Insert your database name.</small>
					<div class='list-group-item fw-bold bg-primary-subtle'>Table Prefix:</div>
					<div class='list-group-item'>
						<input type='text' class='form-control' name='prefix' value='nai_'>
					</div>
					<small class='list-group-item bg-secondary-subtle'><span class='badge bg-secondary'>Note</span> If you don't know whats this then keep it as is.</small>
				</div>

				<div class='list-group mb-2'>
					<div class='list-group-item fs-5 fw-bold active'>Site Setup</div>
					<div class='list-group-item fw-bold bg-primary-subtle'>Site Title</div>
					<div class='list-group-item'>
						<input type='text' class='form-control' name='site_name' value='Next AutoIndex'>
					</div>
					<small class='list-group-item bg-secondary-subtle'><span class='badge bg-secondary'>Note</span> The name of your site</small>
					<div class='list-group-item fw-bold bg-primary-subtle'>Site Logo URL (Keep blank if you don't have)</div>
					<div class='list-group-item'>
						<input type='text' class='form-control' name='site_logo' placeholder='https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/logo.png'>
					</div>
					<small class='list-group-item bg-secondary-subtle'><span class='badge bg-secondary'>Note</span> The full URL to your logo. You can always change this later.</small>
					<div class='list-group-item fw-bold bg-primary-subtle'>Site URL</div>
					<div class='list-group-item'>
						<input type='text' class='form-control' name='site_url' value='https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "'>
					</div>
					<small class='list-group-item bg-secondary-subtle'><span class='badge bg-secondary'>Note</span> The full URL to your site without any slash(/) at end.</small>
				</div>

				<div class='list-group mb-2'>
    				<div class='list-group-item fs-5 fw-bold active'>Admin Credentials</div>
					<div class='list-group-item'>
						<div class='input-group'>
							<input type='password' class='form-control' name='pass' placeholder='{$password}'
							aria-label='{$password}' id='passwordField'>
							<button class='btn btn-secondary' type='button' id='show'>SHOW</button>
						</div>
					</div>
				</div>

				
			

				<div class='my-3 text-center'>
					<input type='submit' class='btn btn-dark px-4' value='Start Installation'></input>
				</div>
			</form>";
		}
		?>
		<footer class="bg-light text-center text-white">
			<!-- Footer Menu -->
			<div class="bg-secondary-subtle p-2 text-dark">
				<a href="<?php echo dirname($_SERVER['REQUEST_URI']); ?>" class="text-decoration-none"> Home </a> |
				<a href="<?php echo dirname($_SERVER['REQUEST_URI']); ?>/tos.php" class="text-decoration-none">TOS</a> |
				<a href="<?php echo dirname($_SERVER['REQUEST_URI']); ?>/admincp" class="text-decoration-none">Admin Panel</a>
			</div>

			<!-- Copyright -->
			<div class="copyright bg-dark text-center p-3">ⓒ 2023 <a class="text-light" href="#">NextAutoIndexPro</a></div>
		</footer>
	</div>

	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var passwordField = document.getElementById("passwordField");
			var showButton = document.getElementById("show");

			showButton.addEventListener("click", function() {
				if (passwordField.type === "password") {
					passwordField.type = "text";
					showButton.textContent = "HIDE";
				} else {
					passwordField.type = "password";
					showButton.textContent = "SHOW";
				}
			});
		});
	</script>

</body>

</html>