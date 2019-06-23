<?php
session_start();
require(__DIR__ . "/../../steamauth/steamauth.php");

$table = $_POST["table"];
if($table != "addons" && $table != "appeals")
{
	die("Unknown table: " . $table);
}

$action = $_POST["action"];
if($action != "add" && $action != "update" && $action != "delete")
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

require(__DIR__ . "/validators.php");

function redir($good, $action, $table, $msg)
{
	$_SESSION["alert_type"] = $good ? "success" : "danger";
	$_SESSION["alert_msg"] = $msg;
	$_SESSION["form"] = $_POST;

	$kind = $action === "add" ? "new" : "update";
	$object = $table === "addons" ? "addon" : "appeal";

	// TODO: indicate whether `appeal` or `addon` and `add` or `edit`

	header('Location: https://smamdb.net/interface/form.php');
	exit;
}

try
{
	validateId();
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

// TODO: actually perfom database ops

$msg = ($table === "addons" ? "Addon" : "Appeal")
	. " \"" . $_POST["id"] . "\""
	. " " . ($action === "add" ? "added" : "updated")
	. " successfully.";

redir(true, $action, $table, $msg);
