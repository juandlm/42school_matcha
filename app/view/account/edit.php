<?php
	$title = 'Edit Account';
?>
<div class="container px-5 mx-auto">
	<small class="form-text text-muted pb-3">In order to edit your account data, simply fill the fields corresponding to the information you'd like to change.</small>
	<form action="<?= URL; ?>account/processEdit" method="POST">
		<div class="form-group mb-5">
			<label>Enter your password</label><span class="ml-2 badge badge-info">Required</span>
			<input type="password" name="password" class="form-control" placeholder="Your current password" required autofocus>
		</div>
		<div class="form-group">
			<label>Change your username</label>
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">@</span>
				</div>
				<input type="username" class="form-control" name="new_username" placeholder="Username">
			</div>
		</div>
		<div class="form-group">
			<label>Change your email address</label>
			<div class="input-group">
				<input type="email" class="form-control" name="new_email" placeholder="mail@example.com">
			</div>
		</div>
		<div class="form-group">
			<label>Change your password</label>
			<input type="password" name="new_password" class="form-control" placeholder="New password">
		</div>
		<div class="form-group">
			<label>Confirm your new password</label>
			<input type="password" name="cnew_password" class="form-control" placeholder="New password">
		</div>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
</div>