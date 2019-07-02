<?php
function printInfo($db, $ids)
{
	print json_encode(getInfo($db, $ids), JSON_UNESCAPED_SLASHES);
}

function getInfo($db, $ids)
{
	$sql = isset($_GET["nodeps"]) ? queryNoDeps($db, $ids) : queryDeps($db, $ids);
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

function queryNoDeps($db, $ids) : string
{
	$list = toSqlList(explode(",", $ids));
	return "SELECT id, baseurl, files, games FROM addons WHERE id IN $list";
}

function queryDeps($db, $ids) : string
{
	$list = explode(",", $ids);
	joinDeps($db, $list);
	return "SELECT id, baseurl, files, games FROM addons WHERE id IN " . toSqlList($list);
}

function joinDeps($db, &$arr) // TODO: need an SQL wizz to turn this into a query
{
	$sql = "SELECT deps FROM addons WHERE id IN " . toSqlList($arr);
	$res = $db->query($sql);
	$newDepsFetched = false;

	while($row = $res->fetch_assoc())
	{
		$deps = explode(" ", $row["deps"]);
		if(sizeof($deps) < 1) continue;

		foreach($deps as $dep)
		{
			if(strlen($dep) > 0 && !in_array($dep, $arr))
			{
				array_push($arr, $dep);
				$newDepsFetched = true;
			}
		}
	}

	if($newDepsFetched) joinDeps($db, $arr);
}

function toSqlList($arr) : string
{
	return "('" . implode("', '", $arr) . "')";
}

function constructAddon($row)
{
	$addon = array();
	$addon["id"] = $row["id"];
	$addon["url"] = $row["baseurl"];
	$addon["files"] = explode("\r\n", $row["files"]);
	$addon["games"] = explode(" ", $row["games"]);
	return $addon;
}
