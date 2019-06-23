<?php

function checkSet($property) : bool
{
	return isset($_POST[$property]) && !empty($_POST[$property]);
}

function validateId() : bool
{
	if(!checkSet("id"))
	{
		throw new Exception("Addon ID is not set.");
	}

	$id = $_POST["id"];

	if(strlen($id) > 32)
	{
		throw new Exception("Addon ID is too long.");
	}

	if(preg_match("/[^\w-.]/", $id))
	{
		throw new Exception("Addon ID can only contain letters, numbers, minus (-) and dot (.).");
	}

	DB::query("SELECT id FROM addons WHERE id=" . DB::quote($id));
	if(sizeof(DB::getData()) > 0)
	{
		throw new Exception("Addon ID is already in use.");
	}

	return true;
}

function validateAuthor() : bool
{
	if(!checkSet("author"))
	{
		throw new Exception("Author not specified.");
	}

	if(strlen($_POST["author"]) > 64)
	{
		throw new Exception("Author name is too long.");
	}

	return true;
}

function validateDescription() : bool
{
	if(!checkSet("description"))
	{
		throw new Exception("Description not specified.");
	}

	if(strlen($_POST["description"]) > 128)
	{
		throw new Exception("Description is too long.");
	}

	return true;
}

function validateCategory() : bool
{
	if(!checkSet("category"))
	{
		throw new Exception("Category not specified.");
	}

	if($_POST["category"] === 0)
	{
		throw new Exception("Category not selected.");
	}

	if($_POST["category"] < 1 || $_POST["category"] > 8)
	{
		throw new Exception("Invalid category.");
	}

	return true;
}

function validatePluginId() : bool
{
	if(!checkSet("pluginid"))
	{
		throw new Exception("Plugin ID not specified.");
	}

	if($_POST["pluginid"] === "specified")
	{
		if(!checkSet("pluginid_num"))
		{
			throw new Exception("Plugin ID number not specified.");
		}

		if(!is_numeric($_POST["pluginid_num"]))
		{
			throw new Exception("Plugin ID is not a number.");
		}

		if($_POST["pluginid_num"] < 1)
		{
			throw new Exception("Invalid plugin ID number.");
		}
	}
	else if($_POST["pluginid"] != "extension" && $_POST["pluginid"] != "unspecified")
	{
		throw new Exception("Invalid plugin ID.");
	}

	return true;
}

function validateBaseUrl() : bool
{
	if(!checkSet("baseurl"))
	{
		throw new Exception("Base URL not specified.");
	}

	$baseurl = $_POST["baseurl"];

	if(strlen($baseurl) > 128)
	{
		throw new Exception("Base URL is too long.");
	}

	// insert http:// in front if not exists to satisfy the shit validator
	$baseurlproto = strpos($baseurl, "http") !== 0 ? "http://$baseurl" : $baseurl;

	if(!filter_var($baseurlproto, FILTER_VALIDATE_URL))
	{
		throw new Exception("Base URL is not a valid URL.");
	}

	return true;
}

function validateFiles() : bool
{
	if(!checkSet("files"))
	{
		throw new Exception("Files not specified.");
	}

	$files = $_POST["files"];

	if(strlen($files) > 1024)
	{
		throw new Exception("Files filed is too long.");
	}

	$sep = "\r\n";
	$file = strtok($files, $sep);

	while($file !== false)
	{
		if(strlen($file) < 3)
		{
			throw new Exception("Invalid file: \"" . $file . "\".");
		}

		$semis = substr_count($file, ';');

		if($semis < 1)
		{
			throw new Exception("Invalid file: \"" . $file . "\". Path and filename must be separated with semicolon.");
		}
		else if($semis === 1)
		{
			if($file[0] === ';')
			{
				throw new Exception("Invalid file: \"" . $file . "\". No path specified.");
			}
			else if(substr($file, -1) === ';')
			{
				throw new Exception("Invalid file: \"" . $file . "\". No filename specified.");
			}
		}
		else
		{
			throw new Exception("Invalid file: \"" . $file . "\". Multiple semicolons.");
		}

		$file = strtok($sep);
	}

	return true;
}

function validateGames() : bool
{
	if(!checkSet("games"))
	{
		throw new Exception("Applicable games are not set.");
	}

	return true;
}

function validateDeps() : bool
{
	if(checkSet("deps"))
	{
		$deps = $_POST["deps"];

		if(strlen($deps) > 128)
		{
			throw new Exception("Dependencies are too long.");
		}

		$sep = " ";
		$dep = strtok($deps, $sep);

		while($dep !== false)
		{
			if(strlen($dep) > 32)
			{
				throw new Exception("Invalid dependency: \"" . $dep . "\". Addon ID is too long.");
			}

			if(preg_match("/[^\w-.]/", $dep))
			{
				throw new Exception("Invalid dependency: \"" . $dep . "\". Addon ID contains illegal characters.");
			}

			$dep = strtok($sep);
		}
	}

	return true;
}
