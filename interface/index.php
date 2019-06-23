<?php
session_start();
require(__DIR__ . "/../steamauth/steamauth.php");
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>SMAMDB Manager</title>
	<link rel="stylesheet" href="https://unpkg.com/mustard-ui@latest/dist/css/mustard-ui.min.css">
</head>
<body>

<?php
if(!isset($_SESSION["steamid"])){
	include(__DIR__ . "/title.php");
}else{
	include(__DIR__ . "/../steamauth/userInfo.php");
	include(__DIR__ . "/interface.php");
}
?>

</body>
</html>
