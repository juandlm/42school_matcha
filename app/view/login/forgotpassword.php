<?php 
	$title = 'Forgot Password';
?>
<div class="container row mx-auto">
	<div class="col-xl-6 text-center jumbotron">
		<p class="lead">Forgot your password? It happens!</p>
		<hr class="my-4">
		<p>In order to reset it, please enter your email address.</p>
	</div>
	<div class="col">
		<form action="<?= URL; ?>login/processForgotPassword" method="POST">
			<div class="form-group">
				<label>Email address</label>
				<div class="input-group">
					<input type="email" name="email" class="form-control" placeholder="mail@example.com" required autofocus>
				</div>
			</div>
			<button type="submit" class="btn btn-matcha">Submit</button>
		</form>
	</div>
</div>