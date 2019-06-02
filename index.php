<?php
$to_get = isset($_GET["ids"]);
if(!$to_get && !isset($_GET["search"]))
{
	die("[]");
}

$config = require("config.php");
$db = new mysqli($config["host"], $config["user"], $config["pass"], $config["dbname"]);

if($db->connect_error)
{
	die("[]");
}

if($to_get)
{
	require("info.php");
	printInfo($db, $_GET["ids"]);
}
else // to search
{
	require("search.php");
	printSearch($db, $_GET["search"]);
}
