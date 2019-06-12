<?php
function printInfo($db, $ids)
{
	print json_encode(getInfo($db, $ids), JSON_UNESCAPED_SLASHES);
}

function getInfo($db, $ids)
{
	$list = "('" . implode("', '", explode(",", $ids)) . "')";
	$sql = "SELECT id, url, files FROM addons WHERE id IN $list";
	$res = $db->query($sql);

	if($res->num_rows == 0)
	{
		return array();
	}

	$rows = array();
	while($row = $res->fetch_assoc())
	{
		$rows[] = constructAddon($row);
	}

	return $rows;
}

function constructAddon($row)
{
	$addon = array();
	$addon["id"] = $row["id"];
	$addon["url"] = $row["url"];
	$addon["files"] = explode("\r\n", $row["files"]);
	return $addon;
}
