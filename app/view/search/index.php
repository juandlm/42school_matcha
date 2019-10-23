<?php
	$title = 'Search';
?>
<div class="container">
	<div class="matcha-search">
		<form method="POST" class="ms-small">
			<div class="inner-form">
				<div class="basic-search">
					<div class="input-field">
						<input id="searchInput" type="text" placeholder="Enter a name, a username or even a country or a city">
						<div class="icon-wrap">
							<svg class="fa fa-search fa-w-16" fill="#ccc" aria-hidden="true" data-prefix="fas" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
								<path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
							</svg>
						</div>
					</div>
				</div>
				<div class="advanced-search fa-sm">
					<h6 class="font-weight-bold text-matcha mb-4">Advanced search</h6>
					<div class="row">
						<div class="col mb-4">
							<p class="text-mseco">Age range</p>
							<div class="d-flex">
								<div id="slider-age-value-lower" class="slide-value-l"></div>
								<div class="flex-grow-1 px-4">
									<div id="ageSlider"></div>
								</div>
								<div id="slider-age-value-upper" class="slide-value-r"></div>
							</div>
						</div>
						<div class="col mb-4">
							<p>Distance from me <small class="text-muted">(in km)</small></p>
							<div class="d-flex">
								<div class="flex-grow-1 px-4">
									<div id="distSlider"></div>
								</div>
								<div id="slider-dist-value-upper" class="slide-value-r"></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col mb-4">
							<p>Rating affinity <small class="text-muted">(in point difference)</small></p>
							<div class="d-flex">
								<div id="slider-ratg-value-lower" class="slide-value-l"></div>
								<div class="flex-grow-1 px-4">
									<div id="ratgSlider"></div>
								</div>
								<div id="slider-ratg-value-upper" class="slide-value-r"></div>
							</div>
						</div>
						<div class="col mb-4">
							<p>Tags in common</p>
							<div class="d-flex">
								<div class="flex-grow-1 px-4">
									<div id="tagSlider"></div>
								</div>
								<div id="slider-tag-value-upper" class="slide-value-r"></div>
							</div>
						</div>
					</div>
					<div class="d-flex justify-content-end">
						<button class="btn btn-outline-secondary mr-2" id="resetSearch" type="button">Reset</button>
						<button class="btn btn-matcha">Search</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="position-relative">
	<div id="sortBtn">
		<div class="sticky-top float-right">
			<div class="dropdown sort-menu d-inline">
				<button class="btn btn-matcha btn mr-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Sort <i class="fas fa-sort fa-fw"></i>
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
	</div>
	<div id="searchResults" class="d-flex flex-wrap">
	</div>
</div>
<script type="text/javascript">
	var start_age = "<?= (($user_age - 3) <= 18 ? 18 : ($user_age - 3)); ?>",
		end_age = "<?= (($user_age + 3) >= 99 ? 99 : ($user_age + 3)); ?>",
		user_rating = <?= $user_rating; ?>,
		filter_params = <?= json_encode($_SESSION["custom_search_params"] ?? null); ?>;
</script>
