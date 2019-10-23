<?php
	$title = 'Login';
?>

	<div class="container">
		<div class="add_separator">
			<div class="row text-center">
				<div class="col-sm-5 col-md-5 col-lg-5">
					<p>Login</p>
					<form method="POST" action="<?= URL; ?>login/processLogin">
						<div class="mb-2">
							<input type="text" name="login" class="form-control" placeholder="Username or email" required>
						</div>
						<div class="mb-2">
							<input type="password" name="password" class="form-control" placeholder="Password" required>
						</div>
						<!-- <div class="mb-2">
							<input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
							<label class="form-check-label" for="rememberMe"><small>Remember me</small></label>
						</div> -->
						<div class="mb-2">
							<button id="auth" class="btn btn-block btn-primary">Login</button>
						</div>
						<small class="form-text text-muted pb-3">Forgot your password? <a href="<?= URL; ?>login/forgotpassword">Click here.</a></small>
					</form>
				</div>
				<div class="col-sm-2 col-md-2 col-lg-2">
					<h3 class="oauth_separator"><i class="fas fa-2x fa-sign-in-alt text-matcha"></i></h3>
				</div>
				<div class="col-sm-5 col-md-5 col-lg-5">
					<p>You can also use these credentials</p>
					<div class="mb-2">
						<a href="<?= URL; ?>social/facebook"><div class="facebook__submit social_club">Log in with Facebook</div></a>
					</div>
					<div class="mb-2">
						<a href="<?= URL; ?>social/intra"><div class="intra__submit social_club">Log in with your 42 account</div></a>
					</div>
				</div>
			</div>
		</div>
	</div>
