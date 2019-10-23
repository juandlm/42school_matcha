<?php
	$title = 'Email Preferences';
?>
<div class="container mx-auto row">
	<div class="col">
		<div class="card mx-auto" style="height: auto; max-width: 400px;">
			<img src="<?= URL; ?>assets/images/email.jpg" class="card-img-top">
			<div class="card-body">
				<h6 class="card-title font-weight-bold">Change how you want to be notified by email</h6>
				<p class="card-text">Toggle emails for likes, messages and comments. You're currently getting email notifications for:</p>
				<form action="<?= URL; ?>account/processPreferences" method="POST">
					<div class="form-group">
						<div class="custom-control custom-switch">
							<input type="checkbox" name="plikes" class="custom-control-input" <?= $likes == 1 ? "checked" : ''; ?> id="likesSwitch">
							<label class="custom-control-label" for="likesSwitch">Likes</label>
						</div>
						<div class="custom-control custom-switch">
							<input type="checkbox" name="pmessages" class="custom-control-input" <?= $messages == 1 ? "checked" : ''; ?> id="messagesSwitch">
							<label class="custom-control-label" for="messagesSwitch">Messages</label>
						</div>
						<div class="custom-control custom-switch">
							<input type="checkbox" name="pvisits" class="custom-control-input" <?= $visits == 1 ? "checked" : ''; ?> id="visitsSwitch">
							<label class="custom-control-label" for="visitsSwitch">Visits</label>
						</div>
					</div>
					<button type="submit" class="btn btn-primary">Submit</button>
				</form>
			</div>
		</div>
	</div>
</div>