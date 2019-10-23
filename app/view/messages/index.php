<?php
	$title = 'Messages';
?>
<style>
@media (max-width: 767.98px) { 
	<?= isset($user_data["username"]) ? ".messages" : ".contacts" ?> {
		width: 100%;
	}
}
</style>
<div class="conversations d-flex">
	<div class="contacts <?= isset($user_data["username"]) ? "d-none" : "d-flex" ?> d-md-flex flex-column">
		<div class="input-group border-bottom p-3">
			<input id="searchBar" type="text" class="form-control" placeholder="Search" aria-label="Recipient's username" aria-describedby="button-addon2">
			<div class="input-group-append">
			<button class="btn btn-default" type="button" id="button-addon2"><i class="fas fa-search text-matcha"></i></button>
			</div>
		</div>
		<?php if (!empty($matches)) { ?>
		<div class="container-fluid border-bottom">
			<div class="text-center text-matcha">
				<h6 id="matchesSd" class="font-weight-bold p-0 mb-0 mt-2 hvr-icon-bob" style="cursor: pointer;">Your matches
					<div>
						<i class="fas fa-chevron-<?= $_SESSION["msg_show_matches"] === true ? "up" : "down" ?> hvr-icon"></i>
					</div>
				</h6>
			</div>
			<div class="glider-contain mb-3" style="width: 90%; <?= $_SESSION["msg_show_matches"] === true ? '' : "display: none;" ?>">
				<div class="glider px-3">
					<?php foreach ($matches as $value) { ?>
					<div>
						<a href="<?= URL . "messages/t/" . $value->mat_login; ?>" title="<?= $value->mat_name; ?>">
							<svg viewBox="0 0 1 1" width="80px" height="80px" class="rounded-circle">
								<image id="pPicImg" xlink:href="<?= (stristr($value->mat_ppic, "http") ? $value->mat_ppic :  URL . "assets/userphotos/" . $value->mat_ppic) ?>" width="100%" height="100%" preserveAspectRatio="xMidYMid slice"/>
							</svg>
						</a>
					</div>
					<?php } ?>
				</div>
				<button role="button" aria-label="Previous" class="glider-prev">
					<i class="fas fa-xs fa-chevron-left"></i>
				</button>
				<button role="button" aria-label="Next" class="glider-next">
					<i class="fas fa-xs fa-chevron-right"></i>
				</button>
			</div>
		</div>
		<div id="conList">
			<?php if (!empty($conversations)) {
				foreach ($conversations as $key => $value) { 
					echo $value;
				}
			} ?>
		</div>
		<?php } ?>
	</div>
	<div class="messages border-left <?= !isset($user_data["username"]) ? "d-none" : "d-flex" ?> d-md-flex flex-column position-relative justify-content-<?= !isset($user_data["username"]) ? "center" : "end" ?>">
		<?php if (isset($user_data["username"])) { ?>
		<a href="<?= URL ?>messages" class="back-to-contacts d-block d-md-none ms-small bg-light">
			<i class="fas fa-chevron-left text-dark"></i>
		</a>
		<?php if (empty($messages)) { ?>
		<div class="match-info">
			<a href="<?= URL . "profile/v/" . $user_data["username"] ?>">
				<svg viewBox="0 0 1 1" width="100%" height="100%" class="rounded-circle">
					<image id="pPicImg" xlink:href="<?= (stristr($user_data["userppic"], "http") ? $user_data["userppic"] :  URL . "assets/userphotos/" . $user_data["userppic"]) ?>" width="100%" height="100%" preserveAspectRatio="xMidYMid slice"/>
				</svg>
			</a>				
			<p class="m-3"><small>You matched with <b><?= $user_data["userdisplayname"] ?></b> on <b><?= date("F d Y", strtotime($user_data["match_info"]->mat_date)); ?></b></small></p>
		</div>
		<?php } ?>
		<div class="message-log pl-4 pl-md-2 pr-2">
			<?= $messages; ?>
		</div>
		<div class="input-group border-top bg-white">
			<input id="messageInput" type="text" class="form-control border-0" placeholder="Type a message" aria-label="Type a message" aria-describedby="button-addon2">
			<div class="input-group-append">
				<button id="sendMsg" class="btn btn-default" type="button" id="button-addon2"><i class="fas fa-lg text-matcha fa-paper-plane"></i></button>
			</div>
		</div>
		<?php } else { ?>
		<div class="align-self-center text-center silver">
			<i class="far fa-comment-dots fa-7x"></i>
			<p id="msgHint" class="m-2">Don't be shy, start a conversation!</p>
		</div>
		<?php } ?>
	</div>
</div>
<script type="text/javascript">
	var msgToday = Boolean(<?= intval(isset($group_date) && $group_date == "Today") ?>);
</script>