<?php
	$title = 'Account Panel';
?>
<div class="container">
    <div class="row text-center">
		<div class="col">
			<a href ="<?= URL; ?>account/emailpreferences" class="hvr-icon-bob text-dark">
				<div class="card bg-light">
					<div class="card-body">
						<i class="fas fa-envelope fa-2x my-3 hvr-icon text-matcha"></i>
						<h5>Email preferences</h5>
						<hr class="w-75">
						<p class="font-weight-light">Change how you want to be notified</p>
					</div>
				</div>
			</a>
		</div>
		<div class="col">
			<a href ="<?= URL; ?>account/edit" class="hvr-icon-bob text-dark">
				<div class="card bg-light">
					<div class="card-body">
						<i class="fas fa-user-edit fa-2x my-3 hvr-icon text-matcha"></i>
						<h5>Edit account</h5>
						<hr class="w-75">
						<p class="font-weight-light">Change your account information</p>
					</div>
				</div>
			</a>
		</div>
		<div class="col">
			<a href ="<?= URL; ?>account/deactivate" class="hvr-icon-bob text-dark">
				<div class="card bg-light">
					<div class="card-body">
						<i class="fas fa-user-slash fa-2x my-3 hvr-icon text-matcha"></i>
						<h5>Deactivate account</h5>
						<hr class="w-75">
						<p class="font-weight-light">Disable your profile and pictures</p>
					</div>
				</div>
			</a>
		</div>
		<div class="col">
			<a href ="<?= URL; ?>account/blocking" class="hvr-icon-bob text-dark">
				<div class="card bg-light">
					<div class="card-body">
						<i class="fas fa-ban fa-2x my-3 hvr-icon text-matcha"></i>
						<h5>Blocking</h5>
						<hr class="w-75">
						<p class="font-weight-light">Manage who you've blocked</p>
					</div>
				</div>
			</a>
		</div>
    </div>
</div>