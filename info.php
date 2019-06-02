<?php
function printInfo($db, $IDS)
{
	$addons = explode(",", $IDS);

	$info = getInfo($db, $addons);
	foreach($info as &$addon)
	{
		$addon["files"] = getFiles($db, $addon["id"]);
	}

	print json_encode($info, JSON_UNESCAPED_SLASHES);
}

function getInfo($db, $addons)
{
	$arr = "('" . implode("', '", $addons) . "')";
	$sql = "SELECT id, url FROM addons WHERE id IN $arr";
	$res = $db->query($sql);

	if($res->num_rows == 0)
	{
		return array();
	}

	$rows = array();
	while($row = $res->fetch_assoc())
	{
		$rows[] = $row;
	}

	return $rows;
}

function getFiles($db, $addon)
{
	$alias = "file";
	$sql = "SELECT CONCAT_WS(';', files.path, files.name) AS $alias FROM files WHERE addon='$addon'";
	$res = $db->query($sql);

	if($res->num_rows == 0)
	{
		return array();
	}

	$files = array();
	while($row = $res->fetch_assoc())
	{
		$files[] = $row[$alias];
	}

	return $files;
}
