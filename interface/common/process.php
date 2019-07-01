<?php
session_start();
require(__DIR__ . "/../../steamauth/steamauth.php");

$table = $_POST["table"];
if($table != "addons" && $table != "appeals")
{
	die("Unknown table: " . $table);
}

$action = $_POST["action"];
if($action != "add" && $action != "edit")
{
	die("Unknown action: " . $action);
}

if(!isset($_SESSION["steamid"]) || empty($_SESSION["steamid"]))
{
	die("User not logged in");
}

$sid = $_SESSION["steamid"];
include(__DIR__ . "/../database.php");

if($table === "addons" && $action === "add")
{
	DB::query("SELECT name FROM users WHERE steamid=$sid");
	if(sizeof(DB::getData()) === 0)
	{
		die("No access to adding addons");
	}
}

if($action === "edit")
{
	DB::query("SELECT addedby FROM $table WHERE id=" . DB::quote($_POST["id"]));
	$row = DB::getData();
	if(empty($row) || $row["addedby"] != $sid)
	{
		die("No access to this addon");
	}
}

require(__DIR__ . "/validators.php");

function redir($good, $action, $table, $msg)
{
	$_SESSION["alert_type"] = $good ? "success" : "danger";
	$_SESSION["alert_msg"] = $msg;
	$_SESSION["form"] = $_POST;

	$_SESSION["form"]["games"] = implode(' ', $_POST["games"]); // make array for form

	$url = "Location: https://smamdb.net/interface/form.php?table=$table&action=$action";
	if($action === "edit") $url .= "&id=" . $_POST["id"];

	header($url);
	exit;
}

if($_POST["pluginid_spec"] == "extension")
{
	$_POST["pluginid"] = -1;
}
else if($_POST["pluginid_spec"] == "unspecified")
{
	$_POST["pluginid"] = 0;
}

try
{
	validateId($action === "add");
	validateAuthor();
	validateDescription();
	validateCategory();
	validatePluginId();
	validateBaseUrl();
	validateFiles();
	validateGames();
	validateDeps();
}
catch(Exception $e)
{
	redir(false, $action, $table, $e->getMessage());
}

$games = implode(' ', $_POST["games"]);
$q = "";

if($action === "add")
{
	$acceptedby = 0;
	if($table === "addons")
	{
		$acceptedby = $_SESSION["steamid"];
	}

	$q = "INSERT INTO $table VALUES ("
		. DB::quote($_POST["id"]) . ", "
		. DB::quote($_POST["author"]) . ", "
		. DB::quote($_POST["description"]) . ", "
		. $_POST["category"] . ", "
		. $_POST["pluginid"] . ", "
		. DB::quote($_POST["baseurl"]) . ", "
		. DB::quote($_POST["files"]) . ", "
		. DB::quote($games) . ", "
		. DB::quote($_POST["deps"]) . ", "
		. $_SESSION["steamid"] . ", $acceptedby, DEFAULT)";
}
else if($action === "edit")
{
	$q = "UPDATE $table SET "
		. "`author`=" . DB::quote($_POST["author"]) . ", "
		. "`description`=" . DB::quote($_POST["description"]) . ", "
		. "`category`=" . DB::quote($_POST["category"]) . ", "
		. "`pluginid`=" . DB::quote($_POST["pluginid"]) . ", "
		. "`baseurl`=" . DB::quote($_POST["baseurl"]) . ", "
		. "`files`=" . DB::quote($_POST["files"]) . ", "
		. "`games`=" . DB::quote($games) . ", "
		. "`deps`=" . DB::quote($_POST["deps"])
		. " WHERE `id`=" . DB::quote($_POST["id"]);
}

$success = DB::query($q);
$msg = ($table === "addons" ? "Addon" : "Appeal")
	. " \"" . $_POST["id"] . "\""
	. " " . ($action === "add" ? "added" : "edited")
	. ($success ? " successfully." : " unsuccessfully.");

redir($success, $action, $table, $msg);
