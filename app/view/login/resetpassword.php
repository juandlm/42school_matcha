<?php 
	$title = 'Reset Password';
?>
<div class="container row mx-auto">
	<div class="col-xl-6 text-center jumbotron">
		<p class="lead">Reset your password</p>
		<hr class="my-4">
		<p>In order to access the website again, please set a new password.</p>
	</div>	
	<div class="col">
		<form action="<?= URL; ?>login/processResetPassword" method="POST">
			<div class="form-group">
				<label>Enter a new password</label>
				<input type="password" name="new_password" class="form-control" placeholder="New password">
			</div>
			<small class="form-text text-muted fa-xs">
				<ul class="text-left fa-ul">
					<li>
						<i class="fa-li fa fa-check text-info"></i>Must be between 6 and 16 characters long
					</li>
					<li>
						<i class="fa-li fa fa-check text-info"></i>Must contain at least one uppercase letter, one lowercase letter and one digit
					</li>
				</ul>
			</small>
			<div class="form-group">
				<label>Confirm your new password</label>
				<input type="password" name="cnew_password" class="form-control" placeholder="">
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
	</div>
</div>