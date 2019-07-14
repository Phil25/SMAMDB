<?php
function printInfo($db, $ids)
{
	print json_encode(getInfo($db, $ids), JSON_UNESCAPED_SLASHES);
}

function getInfo($db, $ids)
{
	$list = isset($_GET["nodeps"]) ? queryNoDeps($db, $ids) : queryDeps($db, $ids);
	$sql = "SELECT id, author, description, baseurl, files, deps FROM addons WHERE id IN $list";
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
	return $list;
}

function queryDeps($db, $ids) : string
{
	$list = explode(",", $ids);
	joinDeps($db, $list);
	return toSqlList($list);
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
	$addon["author"] = $row["author"];
	$addon["description"] = $row["description"];
	$addon["url"] = $row["baseurl"];
	if(!empty($row["files"])) $addon["files"] = explode("\r\n", $row["files"]);
	if(!empty($row["deps"])) $addon["deps"] = explode(" ", $row["deps"]);
	#$addon["games"] = explode(" ", $row["games"]);
	return $addon;
}
