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
if(!isset($_SESSION["steamid"])
|| !isset($_GET["table"])
|| ($_GET["table"] != "addons" && $_GET["table"] != "appeals")
|| !isset($_GET["id"])){
	header("Location: https://smamdb.net/interface/");
	exit;
}else{
include(__DIR__ . "/database.php");
require(__DIR__ . "/../steamauth/steamauth.php");
include(__DIR__ . "/../steamauth/userInfo.php");
include(__DIR__ . "/common/header.php");

$id = $_GET["id"];
$table = $_GET["table"];

DB::query("SELECT author, description, addedby FROM $table WHERE id=" . DB::quote($id));
$addon = DB::getRow();

if(empty($addon) || $addon["addedby"] != $_SESSION["steamid"])
{
	header("Location: https://smamdb.net/interface/");
	exit;
}
?>

<section class="section-secondary">

<div class="card" style="max-width:800px;margin:auto;">
	<h3 class="card-title">
		Warning! You are about to delete the
		<?php echo ($table === "addons" ? ' addon "' : ' appeal "') . $id . '"'; ?>
	</h3>
	<?php echo "<b>Author</b>: " . $addon["author"] . "<br><b>Description</b>: " . $addon["description"]; ?>
	<br>
	<b>This operation cannot be undone.</b>
	<?php if($table === "addons"){ ?>
	Deleting this addon means that users will not be able to download it. To re-add it, it will have to go through the appeal process again.
	<?php } ?>
	Are you sure you want to delete this?
	<ul class="card-actions">
		<li><a href="form.php?table=<?php echo $table; ?>&action=edit&id=<?php echo $id; ?>">
			<button type="button" class="button-success">No, keep it</button>
		</a></li>
		<li><a href="common/deleted.php?table=<?php echo $table; ?>&id=<?php echo $id; ?>">
			<button type="button" class="button-danger">Yes, delete it</button>
		</a></li>
	</ul>
</div>

</section>

<?php }
include(__DIR__ . "/common/footer.php");
?>

</body>
</html>
