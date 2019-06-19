<?php
session_start();

require(__DIR__ . "/../steamauth/steamauth.php");
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Manager</title>
	<link rel="stylesheet" href="https://unpkg.com/mustard-ui@latest/dist/css/mustard-ui.min.css">
</head>
<body>
<?php if(!isset($_SESSION["steamid"])) { ?>

<center>
<h2>Please log in to access this page.</h2>
<?php loginbutton("rectangle"); ?>
<br>
<br>
<a href="https://github.com/Phil25/SMAMDB/wiki#what-is-smamdb">What is this?</a>
</center>

<?php
} else {
	$sid = $_SESSION["steamid"];
	$config = require(__DIR__ . "/../config.php");
	$db = new mysqli($config["host"], $config["user"], $config["pass"], $config["dbname"]);

	if($db->connect_error)
	{
		die("Error connecting to database.");
	}

	$sql = "SELECT name FROM users WHERE steamid=$sid";
	$res = $db->query($sql);

	if($res->num_rows == 0)
	{
?>
<center>
	<h2>You do not have access to this page.</h2>
	<?php logoutbutton(); ?>
</center>
<br/>

<a href="https://github.com/Phil25/SMAMDB/wiki/Submitting-a-plugin-or-extension">How to submit your plugin/extension</a>.

<?php
	}
	else
	{
		include(__DIR__ . "/../steamauth/userInfo.php");
		include("manager.php");
	}
}
?>
</body>
</html>
