<?php
	$title = 'Deactivate Account';
?>
<div class="container mx-auto px-5">
	<div class="alert alert-warning" role="alert">
		<h4 class="alert-heading">Read me!</h4>
		<p>Deactivating your account will effectively disable and make your profile inaccessible, along with your photos and information.</p>
		<hr>
		<p class="mb-0 font-italic"><small>We're sad to see you go, but if you decide to come back, all you have to do is log in to reactivate your account.</small></p>
	</div>
	<form action="<?= URL; ?>account/processDeactivation" method="POST">
		<div class="form-group">
			<label>Enter your password</label><span class="ml-2 badge badge-info">Required</span>
			<input type="password" name="password" class="form-control" placeholder="Your current password" required autofocus>
		</div>
		<div class="form-check">
			<small class="form-text text-muted pb-3">
				<input class="form-check-input" type="checkbox" name="confirmcheck" required>
				<label class="form-check-label" for="confirmcheck">
				I confirm that I want to deactivate my account.
				</label>
			</small>
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>