<section class="section-primary">
<h1>Please sign in through Steam to continue...</h1>
<div class="align-center"><?php loginbutton("rectangle"); ?></div>
</section>

<section class="section-secondary">

<h2 class="align-center">Quick FAQ</h2>

<div class="row" style="max-width:1000px;margin:auto;">
	<div class="col-md-6">
		<div class="card">
			<h3 class="card-title">What is this page for?</h3>
			SMAMDB is a database and a website working in conjunction with <a href="https://github.com/Phil25/SMAM" target="_blank">SMAM</a> to provide a complete, automatic solution of installing and removing SourceMod plugins or extensions. This page is the frontend for SMAMDB, geared toward managing a database of addon metadata, such as its download link, associated files, dependencies or applicable games, to name a few.
		</div>
		<div class="card">
			<h3 class="card-title">SMAM uses this website?</h3>
			By default. However, you may point SMAM at any website which knows how to answer its requests, even your own.
		</div>
	</div>
	<div class="col-md-6">
		<div class="card">
			<h3 class="card-title">Why log in?</h3>
			In order to preserve curation and neatness, only authorized Steam accounts are allowed to directly add addons. You may always request to have your addon added.
		</div>
		<div class="card">
			<h3 class="card-title">File storage?</h3>
			No, SMAMDB stores only information on how to download and install a plugin or extension, which is fetched and used by SMAM. This way, no action is required from the addon's author's part.
		</div>
	</div>
</div>

</section>

<?php include(__DIR__ . "/common/footer.php"); ?>
