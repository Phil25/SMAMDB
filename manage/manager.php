<div class="row">
<div class="col-sm-6">

<?php
$addon = array();

if(isset($_GET["id"]))
{
	$addon["id"] = $_GET["id"];
	$idesc = $db->real_escape_string($addon["id"]);
	$addon_data = $db->query("SELECT * FROM addons WHERE id='$idesc'")->fetch_assoc();
	if($addon_data)
	{
		if($addon_data["addedby"] == $_SESSION["steamid"])
		{
			$addon["author"] = $addon_data["author"];
			$addon["forumid"] = $addon_data["forumid"];
			$addon["url"] = $addon_data["url"];
			$addon["files"] = $addon_data["files"];
		}
		else
		{
			$_POST["iderror"] = "No access to addon: $idesc";
		}
	}
	else
	{
		$_POST["iderror"] = "Addon not found: $idesc";
	}
}

$iderror = isset($_POST["iderror"]);
$editing = isset($_GET["id"]);

?>

<form action="/manage/process.php" method="post" style="padding-left:50px;padding-right:50px">
<fieldset>
<legend>Editing addon: <?php echo $addon["id"]; ?></legend>

<?php
if($iderror)
{
	echo '<p class="alert alert-danger">' . $_POST["iderror"] . '</p>';
}
?>

<div class="form-control-group">
	<div class="form-control">
		<label>Name <?php if($editing) echo " (locked for editing)" ?></label>
		<input type="text" name="id" placeholder="Addon ID" autofocus <?php if($editing || $iderror) echo "readonly"; ?> required value="<?php echo $addon["id"] ?>">
	</div>

	<div class="form-control">
		<label>Author</label>
		<input type="text" name="author" placeholder="Addon author" required value="<?php echo $addon["author"] ?>">
	</div>
</div>

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
<fieldset>
<legend>Plugin ID</legend>
<input type="radio" name="forumid" value="known"<?php echo $known_checked; ?>>
<input type="number" min="1" placeholder="Plugin ID" name="forumid_num" value="<?php echo $known_value; ?>"/><br>
<input type="radio" name="forumid" value="extension"<?php echo $extension_checked; ?>>Extension<br>
<input type="radio" name="forumid" value="unspecified"<?php echo $unspecified_checked; ?>>Unspecified
</fieldset>

<div class="form-control">
<label>
Base URL
</label>
<span class="float-right tooltip">(?)
	<span class="tooltip-text">
		<p>URL which will be searched for the specified files.</p>
		<p>AlliedModders forums and GitHub are treated individually. Information/examples on selecting base URL here TODO.</p>
	</span>
</span>
<input type="text" name="url" placeholder="URL searched for files" value="<?php echo $addon["url"]; ?>" required>
</div>

Files
<span class="float-right tooltip">(?)
	<span class="tooltip-text"><ul>
		<li>Separated by newline.</li>
		<li>Path and speparated by semicolon (<code>;</code>)</li>
		<li>Format: <code>(path)/;(file)</code> (for SM root:<code>./;(file)</code>).</li>
		<li>Relative to <code>(mod)/addons/sourcemod/</code>.</li>
		<li>May go up two directories at most (up to <code>(mod)/</code>).</li>
		<li>Archives are extracted preserving their inner directory structure.</li>
		<li>Do not include directories.</li>
		<li>View cohesive examples here TODO.</li>
	</ul></span>
</span>
<textarea name="files" cols="40" rows="5" placeholder="plugins/;thriller.smx
gamedata/;thriller.plugin.txt
plugins/;AdvancedInfinteAmmo.smx
./;funcommandsX_.*.zip
../../;.*" style="width:100%" required><?php echo $addon["files"]; ?></textarea>

<?php
	if(!$iderror)
	{
		echo '<br><input class="float-right" type="submit" value="' . ($editing ? "Update" : "Add") . '">';
	}
?>
</fieldset>
</form>


</div>

<div class="col-sm-6">

<div style="padding-left:50px;padding-right:50px"><fieldset>
<legend>Your addons</legend>

<script>
function filterAddons()
{
	let input = document.getElementById('filterInput');
	let filter = input.value.toUpperCase();
	let ul = document.getElementById('myTags');
	let as = ul.getElementsByTagName('a');

	let text;
	for(let i = 0; i < as.length; ++i)
	{
		text = as[i].innerText.toUpperCase();
		as[i].style.display = text.indexOf(filter) > -1 ? "" : "none";
	}
}
</script>

<div class="row">
	<div class="col-sm-8">
		<input type="text" id="filterInput" onkeyup="filterAddons()" placeholder="Filter...">
	</div>
	<div class="col-sm-4">
		<center><form><input type="submit" value="New addon"></form></center>
	</div>
</div>

<fieldset><ul class="tags" id="myTags">
<?php
$myaddons = $db->query("SELECT id, author FROM addons WHERE addedby='" . $_SESSION['steamid'] . "'");
if($myaddons->num_rows > 0)
{
	while($row = $myaddons->fetch_assoc())
	{
		echo '<a class="tag tag-rounded" href="?id=' . $row['id'] . '">' . $row['id'] . '</a>';
	}
}
?>
</ul></fieldset>

</fieldset></div>

<div style="padding-left:50px;padding-right:50px"><fieldset>
<legend>Login information</legend>

<div class="row">
<div class="col-sm-6">
	Logged in as <?php echo $steamprofile["personaname"]; ?><br>
	<?php echo $_SESSION["steamid"]; ?>
</div>
<div class="col-sm-6">
	<div style="float:right;"><?php logoutbutton(); ?></div>
</div>
</div></fieldset></div>

</div>
</div>
