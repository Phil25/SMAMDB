<?php
session_start();
require(__DIR__ . "/../../steamauth/steamauth.php");

$table = $_GET["table"];
if($table != "addons" && $table != "appeals")
{
	die("Unknown table: " . $table);
}

if(!isset($_SESSION["steamid"]) || empty($_SESSION["steamid"]))
{
	die("User not logged in");
}

$sid = $_SESSION["steamid"];
include(__DIR__ . "/../database.php");

$id = DB::quote($_GET["id"]);
DB::query("SELECT addedby FROM $table WHERE id=$id");

$row = DB::getRow();
if(empty($row) || $row["addedby"] != $sid)
{
	die("You have no access to this addon.");
}

DB::query("DELETE FROM $table WHERE id=$id");
header("Location: https://smamdb.net/interface/");
