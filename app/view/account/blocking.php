<?php
	$title = 'Blocking';
?>
<div class="container mx-auto px-5">
	<div class="alert alert-info" role="alert">
		<h4 class="alert-heading">Blocking settings</h4>
		<p>Browse and manage the users you've blocked</p>
		<hr>
		<p class="mb-0 font-italic"><small>If you feel something is wrong, don't hesitate to contact us or even the local authorities.</small></p>
	</div>
	<div class="jumbotron bg-light py-3">
		<h6 class="text-matcha font-weight-bold">Blocked users</h6>
		<ul class="list-unstyled">
			<?php if (!empty($blockedusers)) {
				foreach ($blockedusers as $b) { ?>
				<li class="ml-2"><i class="fas fa-circle text-matcha fa-fw align-middle mr-1" style="font-size: 8px;"></i><?= $b->usr_name . " (@" . $b->usr_login . ')'; ?><small class="ml-2"><a class="text-matcha" href="<?= URL . "profile/v/" . $b->usr_login; ?>">View profile</a></small></li>
			<?php }
			} else { ?>
			<li>You haven't blocked anybody, great!</li>
			<?php } ?>
		</ul>
	</div>
</div>