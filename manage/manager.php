<style>
.tooltip {
	position: relative;
	display: inline-block;
}
.tooltip .tooltiptext {
	visibility: hidden;
	width: 500px;
	background-color: LightGray;
	padding: 10px;
	border-style: solid;
	border-width: 2px;
	/* Position the tooltip */
	position: absolute;
	z-index: 1;
	top: -5px;
	left: 105%;
}
.tooltip:hover .tooltiptext {
	visibility: visible;
}
.code {
	font-family: monospace;
	background-color: DarkGray
}
</style>

<div style="max-width:500px;padding:20px;margin:20px">

<?php
$addon = array();

if(isset($_GET["id"]))
{
	$addon["id"]= $_GET["id"];
	$idesc		= $db->real_escape_string($addon["id"]);
	$addon_data	= $db->query("SELECT * FROM addons WHERE id='$idesc'")->fetch_assoc();
	if($addon_data)
	{
		if($addon_data["addedby"] == $_SESSION["steamid"])
		{
			$addon["author"] = $addon_data["author"];
			$addon["forumid"] = $addon_data["forumid"];
			$addon["url"] = $addon_data["url"];
			$addon["files"] = "";
			$files = $db->query("SELECT file FROM files WHERE addon='$idesc'");
			if($files->num_rows > 0)
			{
				while($file = $files->fetch_assoc())
				{
					$addon["files"] .= $file["file"] . "\n";
				}
			}
		}
		else
		{
			$_POST["iderror"] = "No access: $idesc";
		}
	}
	else
	{
		$_POST["iderror"] = "Not found: $idesc";
	}
}

?>

<form><fieldset>
<legend>Load addon</legend>
<input type="text" name="id" placeholder="Addon ID" required>
<input type="submit" value="Load">
<?php if(isset($_GET["id"]))
{
	if(!isset($_POST["iderror"]))
	{
		echo "Loaded: " . $_GET["id"];
	}
	else
	{
		echo $_POST["iderror"];
	}
}
?>
</fieldset></form>

<form action="/manage/process.php" method="post"><fieldset>
<legend>Edit addon</legend>

Name <input type="text" name="id" placeholder="Addon ID" style="float:right;" autofocus required value="<?php echo $addon["id"] ?>">
<br><br>

Author <input type="text" name="author" placeholder="Addon author" style="float:right;" required value="<?php echo $addon["author"] ?>">
<br><br>

Plugin ID
<div class="tooltip">(?)
	<span class="tooltiptext">Found on top of AlliedModders thread.</span>
</div>
<br>
<?php
if(isset($addon["forumid"]))
{
	$known_checked = ($addon["forumid"] > 0) ? "checked" : "";
	$known_value = ($addon["forumid"] > 0) ? $addon["forumid"]: "";
	$extension_checked = ($addon["forumid"] < 0) ? "checked" : "";
	$unspecified_checked = ($addon["forumid"] == 0) ? "checked" : "";
}
else
{
	$known_checked = "checked";
}
?>
<input type="radio" name="forumid" value="known" <?php echo $known_checked ?>>
<input type="number" min="1" placeholder="Plugin ID" name="forumid_num" value="<?php echo $known_value; ?>"/><br>
<input type="radio" name="forumid" value="extension"<?php echo $extension_checked; ?>>Extension<br>
<input type="radio" name="forumid" value="unspecified"<?php echo $unspecified_checked; ?>>Unspecified<br>
<br>

Base URL
<div class="tooltip">(?)
	<span class="tooltiptext">
		<p>URL which will be searched for the specified files.</p>
		<p>AlliedModders forums and GitHub are treated individually. Information/examples on selecting base URL here TODO.</p>
	</span>
</div>
<input type="url" name="url" placeholder="URL searched for files" style="width:100%" value="<?php echo $addon["url"]; ?>" required>
<br><br>

Files
<div class="tooltip">(?)
	<span class="tooltiptext"><ul>
		<li>Separated by newline.</li>
		<li>Path and speparated by semicolon (<span class="code">;</span>)</li>
		<li>Format: <span class="code">(path)/;(file)</span> (for SM root:<span class="code">./;(file)</span>).</li>
		<li>Relative to <span class="code">(mod)/addons/sourcemod/</span>.</li>
		<li>May go up two directories at most (up to <span class="code">(mod)/</span>).</li>
		<li>Archives are extracted preserving their inner directory structure.</li>
		<li>Do not include directories.</li>
		<li>View cohesive examples here TODO.</li>
	</ul></span>
</div>
<textarea name="files" cols="40" rows="5" placeholder="plugins/;thriller.smx
gamedata/;thriller.plugin.txt
plugins/;AdvancedInfinteAmmo.smx
./;funcommandsX_.*.zip
../../;.*" style="width:100%" required><?php echo $addon["files"]; ?></textarea>
<br><br>

<input type="submit" value="Add/Update">
</fieldset></form>

<form><fieldset style="background-color:Crimson;">
<legend style="background-color:white;">Delete addon</legend>
<input type="text" name="delete" placeholder="Addon ID to delete" required>
<input type="submit" value="Delete WITHOUT confirmation">
</fieldset></form>

<br><br>

Logged in as <?php echo $steamprofile["personaname"] . " -- " . $_SESSION["steamid"]; ?>

<div style="float:right;"><?php logoutbutton(); ?></div>

</div>
