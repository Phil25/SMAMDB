<?php
session_start();

require(__DIR__ . "/../steamauth/steamauth.php");
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Manager</title>
</head>
<body>
<?php if(!isset($_SESSION['steamid'])) { ?>
	<center>
	<h2>Log in to access this webpage:</h2>
	<?php loginbutton("rectangle"); ?>
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
		echo "<center><h2>Sorry, you do not have access to this website.</h2>";
		logoutbutton();
		echo "</center>";
	}
	else
	{
		include("manager.php");
	}
}
?>
</body>
</html>
