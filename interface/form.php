<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>SMAMDB Manager</title>
	<link rel="stylesheet" href="https://unpkg.com/mustard-ui@latest/dist/css/mustard-ui.min.css">
</head>
<body>

<?php
if(!isset($_SESSION["steamid"]) || !isset($_GET["action"])){
	header("Location: https://smamdb.net/interface/");
	exit;
}else{
include(__DIR__ . "/database.php");
require(__DIR__ . "/../steamauth/steamauth.php");
include(__DIR__ . "/../steamauth/userInfo.php");
include(__DIR__ . "/common/header.php");
?>

<section class="section-secondary">

<div class="card" style="max-width:1000px;margin:auto;">
	<?php
	function fetchAddon($id)
	{
		DB::query("SELECT * FROM addons WHERE id=" . DB::quote($id));
		return DB::getRow();
	}

	function isGamesAny($games)
	{
		return !isset($games) || empty($games) || in_array("any", $games) || (sizeof($games) === 1 && empty($games[0]));
	}

	if(isset($_SESSION["alert_type"]))
	{
		echo '<h5><p class="alert alert-' . $_SESSION["alert_type"] . '">' . $_SESSION["alert_msg"] . '</p></h5>';
		unset($_SESSION["alert_type"]);
		unset($_SESSION["alert_msg"]);
	}

	$editing = $_GET["action"] === "edit" && isset($_GET["id"]);

	$form = ($editing && !isset($_SESSION["form"])) ? fetchAddon($_GET["id"]) : $_SESSION["form"];
	unset($_SESSION["form"]);

	if($form["pluginid"] > 0)
	{
		$form["pluginid_spec"] = "specified";
	}
	else if($form["pluginid"] == 0)
	{
		$form["pluginid_spec"] = "unspecified";
	}
	else
	{
		$form["pluginid_spec"] = "extension";
	}

	$form["games"] = explode(' ', $form["games"]);

	echo '<h3 class="card-title">' . ($editing ? ('Editing ' . $_GET["id"]) : 'Adding new addon') . '</h3>';
	?>
	<form action="common/process.php" method="post">
		<input type="hidden" name="table" value="<?php echo $_GET["table"]; ?>">
		<input type="hidden" name="action" value="<?php echo $_GET["action"]; ?>">
		<div class="row">
			<div class="col-sm-6" style="padding:16px;">
				<div class="form-control-group">
					<div class="form-control">
						<?php
						if($editing)
						{
							echo '<label>Name <i>(locked from editing)</i></label>';
							echo '<input type="text" maxlength="32" name="id" placeholder="Addon ID" value="' . $form["id"] . '" readonly required>';
						}
						else
						{
							echo '<label>Name</label>';
							echo '<input type="text" maxlength="32" name="id" placeholder="Addon ID" onkeyup="validateId(this, \'idError\')" value="' .  $form["id"] . '" autofocus required>';
						}
						?>
						<p class="validation-error" id="idError"></p>
					</div>
					<div class="form-control">
						<label>Author</label>
						<input type="text" maxlength="64" name="author" placeholder="Addon's author" value="<?php echo $form["author"]; ?>" required>
					</div>
				</div>
				<div class="form-control">
					<label>Description</label>
					<input type="text" maxlength="128" name="description" placeholder="Short description" value="<?php echo $form["description"]; ?>" required>
				</div>
				<div class="form-control">
					<label>Category</label>
					<select name="category">
						<option value="0" <?php echo (!isset($form["category"]) || $form["category"] === 0) ? "selected" : ""; ?>>Select...</option>
						<?php
						DB::query("SELECT id, name FROM categories");
						while($row = DB::getRow())
						{
							echo '<option value="' . $row["id"] . '"' . ($form["category"] === $row["id"] ? "selected" : "") . '>' . $row["name"] . '</option>';
						}
						?>
					</select>
				</div>
				<fieldset>
					<legend>Plugin ID</legend>
					<div class="form-control">
						<label>
							<input type="radio" name="pluginid_spec" value="specified" onchange="pluginIdChanged(this.value, 'inputPluginId')" <?php echo ($form["pluginid_spec"] === "specified") ? "checked" : ""; ?>>
							Specified <input id="inputPluginId" type="number" placeholder="Plugin ID" name="pluginid" value="<?php echo $form["pluginid"]; ?>">
						</label>
					</div>
					<div class="form-control">
						<label>
							<input type="radio" name="pluginid_spec" value="extension" onchange="pluginIdChanged(this.value, 'inputPluginId')" <?php echo ($form["pluginid_spec"] === "extension") ? "checked" : ""; ?>>
							Extension
						</label>
					</div>
					<div class="form-control">
						<label>
							<input type="radio" name="pluginid_spec" value="unspecified" onchange="pluginIdChanged(this.value, 'inputPluginId')" <?php echo ($form["pluginid_spec"] === "unspecified") ? "checked" : ""; ?>>
							Unspecified <span class="tooltip">(?)<span class="tooltip-text">Plugin may not have ID in some cases.</span></span>
						</label>
					</div>
				</fieldset>
			</div>
			<div class="col-sm-6" style="padding:16px;">
				<div class="form-control">
					<label><span class="tooltip">Base URL<span class="tooltip-text">
						<a href="https://github.com/Phil25/SMAMDB/wiki/Submitting-a-plugin-or-extension#selecting-base-url-" target="_blank">Learn about Base URL</a>
						<br>
						<a href="https://github.com/Phil25/SMAMDB/wiki/Example-Submissions" target="_blank">Examples</a>
					</span></span></label>
					<input type="text" maxlength="128" name="baseurl" placeholder="URL searched for files" value="<?php echo $form["baseurl"]; ?>" required>
				</div>
				<div class="form-control">
					<label><span class="tooltip">Files<span class="tooltip-text">
						<a href="https://github.com/Phil25/SMAMDB/wiki/Submitting-a-plugin-or-extension#inputting-files-" target="_blank">Learn about Files</a>
						<br>
						<a href="https://github.com/Phil25/SMAMDB/wiki/Example-Submissions" target="_blank">Examples</a>
					</span></span></label>
					<textarea name="files" maxlength="1024" cols="40" rows="4" placeholder="plugins/;thriller.smx
gamedata/;thriller.plugin.txt
./;funcommandsX_.*.zip
../../;.*" onkeyup="validateFiles(this.value, 'filesError')" required><?php echo $form["files"]; ?></textarea>
					<p class="validation-error" id="filesError"></p>
				</div>
				<fieldset>
				<legend>Applicable games</legend>
				<div class="form-control">
					<label>
						<input type="checkbox" name="games[]" value="any" onchange="checkedAny(this.checked, 'individualGames')" <?php echo isGamesAny($form["games"]) ? "checked" : ""; ?>>Any
					</label>
				</div>
				<div class="form-control" id="individualGames">
				<?php
				DB::query("SELECT short, full FROM games");
				while($row = DB::getRow())
				{
					echo '<label style="margin-right:25px;float:left;white-space:nowrap;">'
						.'<input type="checkbox" name="games[]" value="' . $row["short"] . '" ' . (in_array($row["short"], $form["games"]) ? "checked" : "") . '>' . $row["full"]
						.'</label>';
				}
				?>
				</div>
				</fieldset>
				<div class="form-control">
					<label>Dependencies <i>(optional)</i></label>
					<input type="text" maxlength="128" name="deps" placeholder="Addon IDs separated by space" onkeyup="depsChanged(this.value, 'depsError')" value="<?php echo $form["deps"]; ?>">
					<p class="validation-error" id="depsError"></p>
				</div>
			</div>
		</div>
		<div class="align-center">
			<div class="form-control">
				<input type="submit">
			</div>
		</div>
	<form>
</div>

</section>

<script type="text/javascript" src="common/form.js"></script>
<script type="text/javascript">
checkedAny(<?php echo json_encode(isGamesAny($form["games"])); ?>, 'individualGames');
pluginIdChanged(<?php echo json_encode($form["pluginid_spec"]); ?>, 'inputPluginId');

const ids = new Set([
	<?php
	DB::query("SELECT id FROM addons");
	while($row = DB::getRow())
	{
		echo '"' . $row["id"] . '", ';
	}
	?>
]);
</script>

<?php }
include(__DIR__ . "/common/footer.php");
?>

</body>
</html>
