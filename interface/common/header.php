<?php
$basename = basename($_SERVER["SCRIPT_FILENAME"], ".php");
?>

<section class="section-primary">
	<div class="row">
		<div class="col-md-9">
			<h3>SMAM Database Interface</h3>
			<ul class="breadcrumbs">
				<?php
				if($basename === "index")
				{
					echo '<li>interface home</li>';
				}
				else // in form
				{
					echo '<li><a href="/interface">interface home</a></li>';

					$editing = $_GET["action"] === "edit";
					if($editing)
					{
						echo '<li>editing ' . $_GET["id"] . '</li>';
					}
					else
					{
						echo '<li>new ' . ($_GET["table"] === "addons" ? "addon" : "appeal") . '</li>';
					}
				}
				?>
			</ul>
		</div>
		<div class="col-md-3 align-center">
			Logged in as <strong><?php echo htmlspecialchars($steamprofile["personaname"]); ?></strong>
			<?php logoutbutton(); ?>
		</div>
	</div>
</section>
