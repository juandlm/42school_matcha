<?php
	$title = ($name ?? "User Profile") . " (@" . $username . ")";
	$own_profile = false;
	if ($_SESSION["logged_in"] && (strstr($_SERVER['REQUEST_URI'], $_SESSION['user_username']) == $_SESSION['user_username']))
		$own_profile = true;
	$userpictures = array_filter((array)$userpictures);
	$flag = "assets/images/flags/" . $location["flag"] . ".svg";
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==" crossorigin=""></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script type="text/javascript">
	var like_to_name = "<?= $name; ?>",
		liked = Boolean(<?= $liked; ?>),
		recently_liked = Boolean(<?= $recently_liked ?>),
		match = Boolean(<?= $match ?>),
		unmatch = Boolean(<?= $unmatch ?>);
</script>
<div class="container">
	<div class="row">
		<div class="col container-fluid text-center">
			<button class="btn btn-link" type="button" data-toggle="modal" data-target="#picturesModal">
				<img class="w-100" src="<?= (stristr($profilepic, "http") ? $profilepic :  URL . "assets/userphotos/" . $profilepic); ?>" alt="Your profile photo">
				<?php if (count($userpictures)) { ?>
				<div class="d-flex">
					<?php foreach ($userpictures as $key => $value) { ?>
					<div style="flex: 0 0 calc(100%/<?= strval(3 + (count($userpictures) > 3)); ?>);">
						<svg viewBox="0 0 1 1">
							<image xlink:href="<?= URL . "assets/userphotos/" . $value ?>" width="100%" height="100%" preserveAspectRatio="xMidYMid slice"/>
						</svg>
					</div>
					<?php } for ($i = count($userpictures) + 1; $i < 4; $i++) { ?>
					<div style="flex: 0 0 calc(100%/<?= strval(3 + (count($userpictures) > 3)); ?>);">
						<svg viewBox="0 0 1 1">
							<image xlink:href="<?= URL . "assets/images/userpicture.png" ?>" width="100%" height="100%" preserveAspectRatio="xMidYMid slice"/>
						</svg>
					</div>
					<?php } ?>
				</div>
				<?php } ?>
			</button>
			<div class="my-3 mx-auto" id="mapid"></div>
		</div>
		<div class="modal fade" id="picturesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered bg-transparent" role="document">
				<div class="modal-content bg-transparent border-0">
					<?php if ($own_profile): ?><h1 class="text-white text-center">Edit your photos</h1><?php endif; ?>
					<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-interval="false">
						<ol class="carousel-indicators">
							<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
						<?php foreach ($userpictures as $key => $value) { ?>
							<li data-target="#carouselExampleIndicators" data-slide-to="<?= $key ?>"></li>
						<?php } ?>
						</ol>
						<div class="carousel-inner">
							<div class="carousel-item active">
								<img src="<?= (stristr($profilepic, "http") ? $profilepic :  URL . "assets/userphotos/" . $profilepic); ?>" class="d-block w-100" alt="">
							</div>
						<?php foreach ($userpictures as $key => $value) { ?>
							<div class="carousel-item">
								<img src="<?= URL . "assets/userphotos/" . $value ?>" class="d-block w-100" alt="">
								<?php if ($own_profile) { ?>
								<div class="image-controls d-flex justify-content-center align-items-center">
									<a href="<?= URL . "profile/makeProfilePicture/" . strval($key) ?>" class="profile-tooltip" title="Make profile picture"><i class="fas fa-user fa-lg text-white fa-fw"></i></a>
									<span style="width: 10%;"></span>
									<a href="<?= URL . "profile/deleteUserPicture/" . strval($key) ?>" class="profile-tooltip" title="Delete picture"><i class="fas fa-trash-alt fa-lg text-white fa-fw"></i></a>
								</div>
								<?php } ?>
							</div>
						<?php } ?>
						</div>
						<?php if (count($userpictures)) { ?>
						<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="sr-only">Previous</span>
						</a>
						<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="sr-only">Next</span>
						</a>
					<?php } ?>
					</div>
					<?php if ($own_profile) {
						if (count($userpictures) > 3) { ?>
					<div class="position-relative mt-3" id="picLimitData" tabindex="0" data-placement="bottom" data-toggle="tooltip" title="You can only add up to 5 pictures">
						<button type="button" style="pointer-events: none;" class="btn btn-matcha btn-lg btn-block" disabled>
							Picture limit reached
						</button>
					</div>
					<?php } else { ?>
					<div class="position-relative mt-3">
						<button type="button" class="btn btn-matcha btn-lg btn-block">
							<input type="file" name="imageToUpload" id="imgUl" accept="image/*" title="">Add a picture
						</button>
					</div><?php }
					} ?>
				</div>
			</div>
		</div>
		<div class="col-12 col-md-8 content-main prf-main">
			<?= $isonline ? '<small class="user_status text-success blink_text">online</small>' : '<small class="user_status">last seen ' . $lastseen . '</small>' ?>
			<h1 class="d-inline-block align-middle mr-2"><?= $name ?? $username ?></h1>
			<?php if (!empty($tags)) { ?>
			<span id="genderData" class="align-middle mr-2" data-toggle="popover" data-content="<?= $gender["text"]; ?>" data-trigger="hover" data-placement="bottom"><?= $gender["icon"]; ?></span>
			<a href="<?= URL; ?>ranking"><span id="ratingData" class="mr-2 badge badge-<?= $ratingc; ?> align-middle" data-toggle="popover" data-content="User rating<?= $rating >= 200 ? " (popular!)" : '' ?>" data-trigger="hover" data-placement="bottom" style="font-size: 1rem;"><?= $rating, $rating >= 200 ? '<i class="fas fa-fire fa-flip-horizontal orange ml-1">' : '' ?></i></span></a>
			<span id="matchData" class="<?= ($match) ? "hvr-icon-grow" : ($unmatch ? "hvr-icon-sink-away" : ''); ?> align-middle" data-toggle="popover" data-content="<?= ($match) ? "You like each other!" : ($unmatch ? "You are no longer matched" : ($liked ? "You like " . $name : '' )); ?>" data-trigger="hover" data-placement="bottom"><?= ($match) ? '<i class="hvr-icon fa-fw fas fa-heart fa-2x text-matcha"></i>' : ($unmatch ? '<i class="hvr-icon fa-fw fas fa-heart-broken fa-2x gray"></i>' : ($liked ? '<i class="fa-fw far fa-heart fa-2x text-matcha"></i>' : '' )); ?></span>
			<h5 class="text-matcha mb-3 d-flex">
				<div><i class="fas fa-birthday-cake mr-2 fa-md"></i></i><?= $age ?> years old</div>
				<div><i class="fas fa-map-marker-alt ml-4 mr-2 fa-md"></i><?= $location["city"], (file_exists(ROOT . $flag) ? '<img title="'. $location["country"] . '" class="flag d-inline ml-2" src="' . URL . $flag . '" width="25px" height="25px">' : ''); ?></div>
			</h5>
			<div class="mb-1">
				<ul class="tag_list">
					<?php foreach ($tags as $value) { ?>
						<li><span class="text-matcha" data-id="<?= $value->tag_id; ?>">#<?= $value->tag_name; ?></span></li>
					<?php } ?>
				</ul>
				<?php if ($own_profile) { ?>
				<a class="float-right text-matcha profile-tooltip" href="<?= URL . "profile/edit"; ?>" title="Edit profile"><i class="fas fa-lg fa-fw fa-edit"></i></a>
				<?php } ?>
			</div>
			<div class="border-top">
				<p class="mt-1"><b>Interested in:</b> <?= $orientation; ?>
				<b><br>Member since:</b> <?= date("Y-m-d", strtotime($membersince)); ?></p>
				<?php if (!$own_profile) { ?>
					<div id="matchaActions">
						<?php if (!$blocked) {
								if (!$match && !$unmatch) {
									if (!$liked) {
										if ($likedme) {?>
										<button id="actionBtn" name="match" type="button" class="btn btn-matcha btn-lg btn-block hvr-icon-pulse-grow">
											<i class="fas fa-heart mr-2 hvr-icon"></i>Like back
										</button>
										<?php } else { ?>
										<button id="actionBtn" name="like_first" type="button" class="btn btn-matcha btn-lg btn-block my-2">
											<i class="fas fa-heart mr-2"></i>Like
										</button>
										<?php } ?>
									<?php } elseif ($liked && !$recently_liked) { ?>
									<button id="actionBtn" name="like_again" type="button" class="btn btn-matcha btn-lg btn-block my-2">
										<i class="fas fa-history mr-2"></i>Like again
									</button>
									<?php } 
							} else if ($match && !$unmatch) { ?>
								<button id="actionBtn" name="unmatch_ask" type="button" class="btn btn-secondary btn-lg btn-block my-2">
									<i class="fas fa-heart-broken mr-2"></i>Unmatch
								</button>
								<a href="<?= URL . "messages/t/" . $username ?>" id="msgBtn" role="button" class="btn btn-outline-matcha btn-lg btn-block my-2 <?= $liked ? '' : "d-none" ?>">
									<i class="fas fa-comment mr-2"></i>Message
								</a>
								<div class="modal fade" id="confirmUnmatchModal" tabindex="-1" role="dialog" aria-labelledby="confirmUnmatchModalTitle" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-body">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
												Are you sure you want to unmatch <b><?= $name; ?></b>?
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
												<button id="confirmUnmatch" name="unmatch" type="button" class="btn btn-matcha">Confirm</button>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						<?php } else { ?>
							<a href="<?= URL; ?>profile/processBlock/<?= $username; ?>" role="button" class="btn btn-primary btn-lg btn-block my-2">
								<i class="fas fa-unlock-alt mr-2"></i>Unblock
							</a>
						<?php } ?>
					</div>
				<?php } ?>
				<h4 class="mt-4 text-matcha">About me</h4>
				<div class="prf-biography">
					<p>
						<?= $bio; ?>
					</p>
				</div>
				<?php if (!$own_profile) { ?>
				<p class="text-right mt-2"><small><?php if (!$blocked) { ?><a class="text-muted" href="<?= URL; ?>profile/processBlock/<?= $username; ?>">Block this user</a> | <a id="fakeUser" class="text-muted" href="#">Report as fake account</a></small></p><?php } ?>
				<?php } ?>
			</div>
			<?php } else { ?>
				<h4 class="border-top pt-3 text-matcha">This user hasn't set up their profile yet, check back later.</h4>
			<?php } ?>
		</div>
	</div>
</div>
<script type="text/javascript">
	var usr_lat = parseFloat("<?= $location["lat"]; ?>"),
		usr_long = parseFloat("<?= $location["long"]; ?>"),
		usr_city = "<?= $location["city"]; ?>",
		usr_country = "<?= $location["country"]; ?>"
		geobool = Boolean(<?= $geoloc; ?>);
</script>