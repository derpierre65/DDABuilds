<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="section-heading">Welcome to DDA Builder!</h1>
			<p class="lead section-lead">Either browse through the existing builds or log in via Steam and create your own to share them with the world.</p>
			<p class="section-paragraph">DDA Builder site originally created by Chakratos at
				<a href="https://dundefplanner.com" target="_blank">dundefplanner.com</a> for Dungeon Defender 1 builds.<br>
			</p>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		var imagecount = 1;
		var rnd = Math.floor((Math.random() * imagecount) + 1);
		$('#headerimage').css('background-image', 'url("/assets/images/index/' + rnd + '.jpg"');
	});
</script>