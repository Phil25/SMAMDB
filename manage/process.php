<?php
session_start();

require(__DIR__ . "/../steamauth/steamauth.php");

if(!isset($_SESSION["steamid"]))
{
	die("No access.");
}

$sid = $_SESSION["steamid"];
$config = require(__DIR__ . "/../config.php");
$db = new mysqli($config["host"], $config["muser"], $config["mpass"], $config["dbname"]);

if($db->connect_error)
{
	die("Error connecting to database.");
}

$sql = "SELECT name FROM users WHERE steamid=$sid";
$res = $db->query($sql);

if($res->num_rows == 0)
{
	die("You have no access to this page.");
}

checkPost("id");
checkPost("forumid");
checkPost("url");
checkPost("files");
checkPost("author");

$id			= $_POST["id"];
$forumid	= getNumForumId($_POST["forumid"]);
$url		= $_POST["url"];
$author		= $_POST["author"];
$files		= array_map('trim', explode("\n", $_POST["files"])); // explode and trim each

validateFiles($files);

$idesc		= $db->real_escape_string($id);
$forumidesc	= $db->real_escape_string($forumid);
$urlesc		= $db->real_escape_string($url);
$authoresc	= $db->real_escape_string($author);

$sql = "SELECT addedby FROM addons WHERE id='$idesc'";
$res = $db->query($sql);

if($res->num_rows == 0) // Addon not found
{
	$sql = "INSERT INTO addons(id, forumid, url, author, addedby) VALUES" .
	"('$idesc', '$forumidesc', '$urlesc', '$authoresc', '" . $_SESSION["steamid"] . "');";

	if($db->query($sql))
	{
		err("Addon $idesc added successfully!");
	}
	else
	{
		err("Adding addon $idesc failed.");
	}
}
else // Addon found
{
	if($res->fetch_assoc()["addedby"] != $_SESSION["steamid"])
	{
		err("You have no access to $idesc");
	}

	$sql = "UPDATE addons set forumid='$forumidesc', url='$urlesc', author='$authoresc' WHERE id='$idesc'";

	if($db->query($sql))
	{
		err("Addon $idesc updated successfully!");
	}
	else
	{
		err("Updating addon $idesc failed.");
	}
}

function err($message)
{
	echo "<script type='text/javascript'>alert(\"$message\"); window.history.back();</script>";
	exit;
}

function checkPost($val) : void
{
	if(!isset($_POST[$val]) || empty($_POST[$val]))
	{
		err("$val not set");
	}
}

function getNumForumId($idstr) : int
{
	if($idstr == "known")
	{
		if(isset($_POST["forumid_num"]) && !empty($_POST["forumid_num"]))
		{
			return $_POST["forumid_num"];
		}
		else
		{
			return 0;
		}
	}
	else if($idstr == "extension")
	{
		return -1;
	}
	else // if($idstr == "unspecified")
	{
		return 0;
	}
}

function validateFiles($files)
{
	foreach($files as $file)
	{
		if($file[0] == "/")
		{
			err($file . " error: cannot start with /");
		}

		if(substr($file, -1) == "/")
		{
			err($file . " error: cannot end with /");
		}

		if(strpos($file, "/") === False)
		{
			err($file . " error: must contain path and name (path)/(file)");
		}
	}
}
