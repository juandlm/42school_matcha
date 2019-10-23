<?php
	$title = 'Sign up';
?>
<div class="container">
	<div class="add_separator">
		<div class="row text-center">
			<div class="col-sm-5 col-md-5 col-lg-5">
				<p>Sign up</p>
				<form method="POST" onSubmit="return false;" autocomplete="off">
					<div class="form-row mb-2">
						<div class="input-group needs-validation">
							<div class="input-group-prepend">
								<span class="input-group-text">@</span>
							</div>
							<input id="username" type="text" class="form-control rounded-right" placeholder="Username" required>
							<div class="form-feedback">
							</div>
						</div>
						<small id="collapseUHint" class="collapse form-text text-muted fa-xs">
							<ul class="text-left fa-ul mt-1">
								<li><i class="fa-li fa fa-check text-info"></i>Must be between 3 and 20 characters long</li>
								<li><i class="fa-li fa fa-check text-info"></i>Can only contain alphanumeric (aA-zZ and 0-9), underscore (_) and hyphen (-) characters</li>
							</ul>
						</small>
					</div>
					<div class="form-row mb-2 needs-validation">
						<input type="email" id="email" class="form-control" placeholder="Email" required>
						<div class="form-feedback">
						</div>
						<small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
					</div>
					<div class="form-row mb-2 needs-validation">
						<input type="password" id="password" class="form-control" placeholder="Password" required>
						<div class="form-feedback">
						</div>
						<small id="collapsePHint" class="collapse form-text text-muted fa-xs">
							<ul class="text-left fa-ul mt-1">
								<li>
									<i class="fa-li fa fa-check text-info"></i>Must be between 8 and 16 characters long
								</li>
								<li>
									<i class="fa-li fa fa-check text-info"></i>Must contain at least one uppercase letter, one lowercase letter and one digit
								</li>
							</ul>
						</small>
					</div>
					<div class="form-row mb-2 needs-validation">
						<input type="password" id="cpassword" class="form-control" placeholder="Confirm password" required>
						<div class="form-feedback">
						</div>
					</div>
					<div class="form-row mb-2">
						<div class="form-check text-left">
							<small class="form-text text-muted pb-3 needs-validation">
								<input class="form-check-input" type="checkbox" id="toscheck" name="toscheck" required>
								<label class="form-check-label" for="toscheck">
									I accept the <a href ="<?= URL; ?>home/termsofservice">Terms of Service</a>.
								</label>
								<div class="form-feedback">
								</div>
							</small>
						</div>
					</div>
					<div class="form-row mb-2">
						<button id="signup" class="btn btn-block btn-success">Sign up</button>
					</div>
				</form>
				<div class="mb-2 small">
					<p><i class="fas fa-lg fa-fw fa-info-circle text-info mr-1"></i>After signing up, you will need to complete your profile in order to use Matcha.</p>
					<p>Your profile will be accessible at <b>http:<?= URL ?>profile/v/</b></p>
				</div>
			</div>
			<div class="col-sm-2 col-md-2 col-lg-2">
				<h3 class="oauth_separator"><i class="fas fa-2x fa-sign-in-alt text-matcha"></i></h3>
			</div>
			<div class="col-sm-5 col-md-5 col-lg-5">
				<p>You can also use these credentials</p>
				<div class="mb-2">
					<a href="<?= URL; ?>social/facebook"><div class="facebook__submit social_club">Sign up with Facebook</div></a>
				</div>
				<div class="mb-2">
					<a href="<?= URL; ?>social/intra"><div class="intra__submit social_club">Sign up with your 42 account</div></a>
				</div>
			</div>
		</div>
	</div>
</div>