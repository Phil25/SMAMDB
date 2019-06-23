<?php
include(__DIR__ . "/database.php");
include(__DIR__ . "/common/header.php");
?>

<section class="section-secondary">
<div class="row" style="max-width:1000px;margin:auto;">
	<div class="col-md-6">
		<div class="card">
			<h3 class="card-title">My Addons</h3>
			<div class="row">
				<div class="col-sm-10">
					<input type="text" id="filterAddons" onkeyup="filterItems('filterAddons', 'myAddons')" placeholder="Filter...">
				</div>
				<div class="col-sm-2">
					<a href="form.php"><button>+</button></a>
				</div>
			</div>
			<div style="height:300px;overflow:hidden;overflow-y:auto;">
				<div class="tags" id="myAddons">
				<?php
				DB::query("SELECT id FROM addons WHERE addedby='" . $_SESSION['steamid'] . "'");
				while($row = DB::getRow())
				{
					echo '<a class="tag tag-rounded tag-green" style="margin:4px" href="editAddon.php?id=' . $row['id'] . '">' . $row['id'] . '</a>';
				}
				?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="card">
			<h3 class="card-title">My Requests</h3>
			<div class="row">
				<div class="col-sm-10">
					<input type="text" id="filterRequests" onkeyup="filterItems('filterRequests', 'myRequests')" placeholder="Filter...">
				</div>
				<div class="col-sm-2">
					<a href="newRequest.php"><button>+</button></a>
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
