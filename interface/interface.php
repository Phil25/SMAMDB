<?php
include(__DIR__ . "/database.php");
include(__DIR__ . "/common/header.php");

function spawnAddons($table)
{
	DB::query("SELECT id FROM $table WHERE addedby='" . $_SESSION["steamid"] . "'");
	$green = $table === "addons" ? " tag-green" : "";
	while($row = DB::getRow())
	{
		echo '<a class="tag tag-rounded' . $green . '" style="margin:4px" href="form.php?table=' . $table . '&action=edit&id=' . $row["id"] . '">' . $row["id"] . '</a>';
	}
}
?>

<section class="section-secondary">
<div class="row" style="max-width:1000px;margin:auto;">
	<div class="col-md-6">
		<div class="card">
			<h3 class="card-title">Your addons</h3>
			<p>Your addons which have been accepted and are available for download by anyone.</p>
			<div class="row">
				<div class="col-sm-10">
					<input type="text" id="filterAddons" onkeyup="filterItems('filterAddons', 'myAddons')" placeholder="Filter...">
				</div>
				<div class="col-sm-2">
					<a href="form.php?table=addons&action=add"><button>+</button></a>
				</div>
			</div>
			<div style="height:300px;overflow:hidden;overflow-y:auto;">
				<div class="tags" id="myAddons">
					<?php spawnAddons('addons'); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="card">
			<h3 class="card-title">Your appeals</h3>
			<p>Your addon appeals which are under review to be accepted. These cannot be downloaded by any user.</p>
			<div class="row">
				<div class="col-sm-10">
					<input type="text" id="filterRequests" onkeyup="filterItems('filterRequests', 'myAppeals')" placeholder="Filter...">
				</div>
				<div class="col-sm-2">
					<a href="form.php?table=appeals&action=add"><button>+</button></a>
				</div>
			</div>
			<div style="height:300px;overflow:hidden;overflow-y:auto;">
				<div class="tags" id="myAppeals">
					<?php spawnAddons('appeals'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
</section>

<?php include(__DIR__ . "/common/footer.php"); ?>

<script>
function filterItems(inputName, ulName){
	let input = document.getElementById(inputName);
	let filter = input.value.toUpperCase();
	let ul = document.getElementById(ulName);
	let as = ul.getElementsByTagName('a');

	let text;
	for(let i = 0; i < as.length; ++i)
	{
		text = as[i].innerText.toUpperCase();
		as[i].style.display = text.indexOf(filter) > -1 ? "" : "none";
	}
}
</script>
