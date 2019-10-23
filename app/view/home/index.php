<?php
	$title = 'Welcome';
?>
<style>
.main-wrapper {
	padding-top: 60px;
}
</style>
<div id="myCarousel" class="carousel slide" data-ride="carousel">
	<ol class="carousel-indicators">
		<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
		<li data-target="#myCarousel" data-slide-to="1"></li>
		<li data-target="#myCarousel" data-slide-to="2"></li>
	</ol>
	<div class="carousel-inner">
		<div class="carousel-item active">
			<img src="<?= URL; ?>assets/images/carousel1.jpg" width="100%" height="100%" background="#777" color="#777" text=" " title=" ">
			<div class="container">
				<div class="carousel-caption text-left">
					<h1 style="text-shadow: 1.5px 1.5px #5c5c5c;">Love is on the web</h1>
					<p style="text-shadow: 1.5px 1.5px #5c5c5c;">Let us prove to you we are real matchmakers, the search is over. Your soulmate is online.</p>
					<p><a class="btn btn-lg btn-success" href="<?= URL; ?>signup" role="button">Sign up today</a></p>
				</div>
			</div>
		</div>
		<div class="carousel-item">
			<img src="<?= URL; ?>assets/images/carousel2.jpg" width="100%" height="100%" background="#777" color="#777" text=" " title=" ">
			<div class="container">
				<div class="carousel-caption text-dark">
					<p class="font-italic" style="text-shadow: 2px 2px #FFF;">
						"Start mackin' on my black berry, she got jealous I was tappin' on my Blackberry.<br>
						So she didn't keep in touch and I can't tell the difference, we ain't see each other much.<br>
						Aside from some late night texts, a couple Os, a few Xs."
					</p>
					<p>- Jay Z</p>
					<p><a class="btn btn-lg btn-primary" href="<?= URL; ?>login" role="button">Log in to learn more</a></p>
				</div>
			</div>
		</div>
		<div class="carousel-item">
			<img src="<?= URL; ?>assets/images/carousel3.jpg" width="100%" height="100%" background="#777" color="#777" text=" " title=" ">
			<div class="container">
				<div class="carousel-caption text-right">
					<h1>Peace is the deeper meaning of presence</h1>
					<p>Without fulfillment, one cannot reflect. We can no longer afford to live with pain. Only a prophet of the universe may manifest this osmosis of love.</p>
					<p><a class="btn btn-lg btn-matcha" href="http://erppy.co" role="button">Become enlightened</a></p>
				</div>
			</div>
		</div>
	</div>
	<a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
	<span class="carousel-control-prev-icon" aria-hidden="true"></span>
	<span class="sr-only">Previous</span>
	</a>
	<a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
	<span class="carousel-control-next-icon" aria-hidden="true"></span>
	<span class="sr-only">Next</span>
	</a>
</div>
<div class="container marketing">
	<div class="row">
		<div class="col-lg-4">
			<img src="<?= URL; ?>assets/images/fp1.png" width="140" height="140" background="#777" color="#777" class="rounded-circle">
			<h2>Bender</h2>
			<p class="font-italic"><a class="text-matcha" href="https://www.youtube.com/watch?v=SgyqAMYtcmA">Bender knows love, and love doesn't share itself with the world. Love is suspicious, love is needy, love is fearful, love is greedy. My friends, there is no great love without great jealousy!</a><br>Oh yeah, and join Matcha or whatever.</p>
		</div>
		<div class="col-lg-4">
			<img src="<?= URL; ?>assets/images/fp2.png" width="140" height="140" background="#777" color="#777" class="rounded-circle">
			<h2>Mega Man</h2>
			<p class="font-italic">世界平和を願う優しい心を持つロックマンは、Dr.ワイリーの野望を阻止するため、そしてワイリーに手駒として利用されている兄弟ロボットたちを救うために、戦いの場へと旅立つのであった。</p>
		</div>
		<div class="col-lg-4">
			<img src="<?= URL; ?>assets/images/fp3.png" width="140" height="140" background="#777" color="#777" class="rounded-circle">
			<h2>Janet</h2>
			<p class="font-italic">Hi, I'm technically not a girl, nor a robot. I'm Janet, I was not programmed to be able to love but I fell in love with Jason, whom I met on Matcha; which is the only online dating site available in The Good Place.</p>
		</div>
	</div>
	<hr class="featurette-divider">
	<div class="row featurette">
		<div class="col-md-5">
			<h2 class="featurette-heading">HOT SINGLES! <span class="text-muted">Where?</span></h2>
			<p class="lead">In your area.</p>
		</div>
		<div class="col-md-7">
			<img src="<?= URL; ?>assets/images/f1.png" background="#eee" color="#aaa" class="bd-placeholder-img-lg featurette-image img-fluid mx-auto">
		</div>
	</div>
	<hr class="featurette-divider">
	<div class="row featurette">
		<div class="col-md-5 order-md-2">
			<h2 class="featurette-heading">Show them your worth. <span class="text-muted">Spread your love.</span></h2>
			<p class="lead">Showcase the best version of yourself.</p>
		</div>
		<div class="col-md-7 order-md-1">
			<img src="<?= URL; ?>assets/images/f2.png" background="#eee" color="#aaa" class="bd-placeholder-img-lg featurette-image img-fluid mx-auto">
		</div>
	</div>
	<hr class="featurette-divider">
	<div class="row featurette">
		<div class="col-md-4">
			<h2 class="featurette-heading">Reach for the stars. <span class="text-muted">Love is competitive.</span></h2>
			<p class="lead">No mercy for the weak.</p>
		</div>
		<div class="col-md-8">
			<img src="<?= URL; ?>assets/images/f3.png" background="#eee" color="#aaa" class="bd-placeholder-img-lg featurette-image img-fluid mx-auto">
		</div>
	</div>
</div>