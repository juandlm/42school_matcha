<?php
	$title = '';
	$route = 'members';
?>
<div class="d-flex w-100">
	<div class="sidebar-container d-none d-lg-flex flex-column">
		<div class="sidebar border-right">
			<h6 class="d-flex px-3 my-2 text-matcha">
				<span class="font-weight-bold">Latest notifications</span>
			</h6>
			<ul class="list-group notifications flex-column pb-2 border-bottom">
			</ul>
			<h6 class="d-flex justify-content-between pl-3 pr-2 my-2 text-matcha">
				<span class="font-weight-bold">Your rating</span>
				<span id="ratinginfo" data-html="true" title="<?= htmlspecialchars($ratinghint); ?>"
					class="fa-stack align-self-center text-matcha" style="font-size: 0.6rem">
					<i class="far fa-circle fa-fw fa-stack-2x"></i>
					<i class="fas fa-info fa-fw fa-stack-1x"></i>
				</span>
			</h6>
			<div class="nav flex-column px-4 pb-2 border-bottom">
				<div class="nav-item d-flex flex-row">
					<div class="rating-count <?= $rating_c; ?>"><?= $user_rating; ?></div>
					<?php if ($user_rating >= 200) {
						echo '<div data-toggle="popover" data-trigger="hover" data-content="You\'re popular!" class="fire align-self-center">
								<div class="fire-main">
									<div class="main-fire"></div>
									<div class="particle-fire"></div>
								</div>
								<div class="fire-bottom">
									<div class="main-fire"></div>
								</div>
							</div>';
					} ?>
				</div>
				<a class="text-right text-matcha" href="<?= URL; ?>ranking"><small>View global ranking</small> <i class="fas fa-chevron-right" style="font-size: 10px;"></i></a>
			</div>
			<h6 class="d-flex px-3 my-2 text-matcha">
				<span class="font-weight-bold">You recently viewed</span>
			</h6>
			<div class="d-flex flex-wrap" style="padding-bottom: 20vh;">
			<?php foreach ($lastvisits as $visit) { ?>
				<div class="visit-container">
					<a href="<?= URL . "profile/v/" . $visit->usr_login ?>">
						<svg viewBox="0 0 1 1" width="100%" height="100%">
							<image xlink:href="<?= (stristr($visit->usr_ppic, "http") ? $visit->usr_ppic :  URL . "assets/userphotos/" . $visit->usr_ppic) ?>" width="100%" height="100%" preserveAspectRatio="xMidYMid slice"/>
						</svg>
						<?php if ($visit->isonline) { ?>
						<span class="position-absolute" style="bottom: 0; left: 5px; font-size: 10px;"><i class="fas fa-circle text-success"></i></span>
						<?php } ?>
						<div class="visit-overlay">
							<div>
								<?= $visit->usr_name ?>
							</div>
						</div>
					</a>
				</div>
			<?php } ?>
			</div>
		</div>
	</div>
	<div class="dashboard d-flex flex-column flex-fill px-4 position-relative">
		<div class="matcha-section my-2">
			<span class="section-title">
				Your match suggestions
			</span>
		</div>
		<div class="sticky-top mt-1 rounded mb-2 bg-matcha-light p-1 border-matcha d-flex justify-content-end" style="top: 75px;">
			<span id="suggcount" class="ml-1 mr-auto align-self-center fa-xs text-matcha">
				<?php if (count($suggestions) == 1) {
					echo "We found 1 potential match";
				} else if (count($suggestions) > 1) {
					echo "We found " . count($suggestions) . " potential matches";
				} else {
					echo "We found nobody 😔, try changing your filter settings";
				} ?>
			</span>
			<div class="dropdown filter-menu mr-2 d-inline ">
				<button class="btn btn-matcha btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fas fa-filter fa-fw"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" style="min-width: calc(200px + 10vw);">
					<h6 class="dropdown-header font-weight-bold">Filter</h6>
					<div class="custom-control custom-switch mx-4">
						<input type="checkbox" class="custom-control-input" id="smartFilterSwitch" <?= $_SESSION["smart_filter"] === true ? "checked" : '' ?>>
						<label class="custom-control-label" for="smartFilterSwitch">Smart filter</label>
					</div>
					<div class="dropdown-divider"></div>
					<h6 class="dropdown-header font-weight-bold">Age range</h6>
					<div class="dropdown-item d-flex">
						<div id="slider-age-value-lower" class="slide-value-l"></div>
						<div class="flex-grow-1 px-4">
							<div id="ageSlider"></div>
						</div>
						<div id="slider-age-value-upper" class="slide-value-r"></div>
					</div>
					<h6 class="dropdown-header font-weight-bold">Distance from me <small>(in km)</small></h6>
					<div class="dropdown-item d-flex">
						<div class="flex-grow-1 px-4">
							<div id="distSlider"></div>
						</div>
						<div id="slider-dist-value-upper" class="slide-value-r"></div>
					</div>
					<h6 class="dropdown-header font-weight-bold">Rating affinity <small>(in point difference)</small></h6>
					<div class="dropdown-item d-flex">
						<div id="slider-ratg-value-lower" class="slide-value-l"></div>
						<div class="flex-grow-1 px-4">
							<div id="ratgSlider"></div>
						</div>
						<div id="slider-ratg-value-upper" class="slide-value-r"></div>
					</div>
					<h6 class="dropdown-header font-weight-bold">Tags in common</h6>
					<div class="dropdown-item d-flex">
						<div class="flex-grow-1 px-4">
							<div id="tagSlider"></div>
						</div>
						<div id="slider-tag-value-upper" class="slide-value-r"></div>
					</div>
					<div class="dropdown-divider mt-3"></div>
					<div class="px-3">
						<button id="applyFilter" class="btn btn-block btn-matcha" type="submit">Apply</button>
					</div>
				</div>
			</div>
			<div class="dropdown sort-menu d-inline">
				<button class="btn btn-matcha btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fas fa-sort fa-fw"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
					<h6 class="dropdown-header font-weight-bold">Sort by</h6>
					<button type="button" class="dropdown-item" value="age"><i class="fas text-matcha mr-1"></i>Age</button>
					<button type="button" class="dropdown-item" value="distance"><i class="fas text-matcha mr-1"></i>Distance</button>
					<button type="button" class="dropdown-item" value="rating"><i class="fas text-matcha mr-1"></i>Rating</button>
					<button type="button" class="dropdown-item" value="ctags"><i class="fas text-matcha mr-1"></i>Shared tags</button>
				</div>
			</div>
		</div>
		<div class="suggestions">
		<?php if (!empty($suggestions)) {
			foreach ($suggestions as $s) { ?>
			<div class="dropdown suggestion-item" data-age="<?= $s->usr_age ?>" data-distance="<?= floor($s->usr_dist) ?>" data-rating="<?= $s->usr_rating ?>" data-ctags="<?= $s->common_tags; ?>">
				<svg viewBox="0 0 1 1" width="100%" height="100%">
					<image xlink:href="<?= (stristr($s->usr_ppic, "http") ? $s->usr_ppic :  URL . "assets/userphotos/" . $s->usr_ppic); ?>" width="100%" height="100%" preserveAspectRatio="xMidYMid slice"/>
				</svg>
				<?php if ($s->isonline) { ?>
					<span class="position-absolute" style="bottom: 0; left: 5px; font-size: 10px;"><i class="fas fa-circle text-success"></i></span>
				<?php } ?>
				<div data-toggle="dropdown" class="overlay bg-dark"></div>
				<div class="dropdown-menu">
					<div class="card m-0 p-0">
						<div class="card-header bg-matcha-light text-matcha py-0">
							<span class="font-weight-bold"><?= $s->usr_name, "<span class='ml-1 badge badge-dark bg-matcha align-middle' style='font-size: 10px;'>" . $s->usr_rating . "</span>"; ?></span>
							<div class="fa-xs"><?= $s->usr_age ?> years old • <?= $s->usr_city; ?></div>
						</div>
						<div class="card-body fa-xs py-0">
							<p class="card-text font-italic text-matcha my-1 w-100 text-break">
							<?php foreach ($s->tags as $t)
								printf(in_array($t->tag_id, $user_tags) ? "<b style='cursor: help;' title='Shared tag'>#%s </b>" : "#%s ", $t->tag_name);
							?>
							</p>
							<a href="<?= URL . "profile/v/" . $s->usr_login ?>" class="btn btn-matcha btn-sm fa-sm mb-2">View profile</a>
						</div>
						<div class="card-footer fa-xs py-0">
							<span class="card-text m-0 fa-sm"><?= floor($s->usr_dist) . "km away"; ?></span>
						</div>
					</div>
				</div>
		  	</div>
			<?php }
		} ?>
		</div>
	</div>
</div>
<script type="text/javascript">
	var start_age = "<?= (($user_age - 3) <= 18 ? 18 : ($user_age - 3)); ?>",
		end_age = "<?= (($user_age + 3) >= 99 ? 99 : ($user_age + 3)); ?>",
		user_rating = <?= $user_rating; ?>,
		filter_params = <?= json_encode($_SESSION["custom_filter_params"] ?? null); ?>;
</script>