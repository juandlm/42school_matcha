<?php
	$title = 'Edit profile';
	$route = "edit";
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==" crossorigin=""></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<div class="container">
	<div class="add_separator">
		<div class="d-flex flex-column flex-md-row">
			<div class="profile">
				<div class="form-row mb-2 needs-validation">
					<label for="displayname" class="text-matcha">Display name</label>
					<input type="text" id="displayname" class="form-control" placeholder="Display name" value="<?= $name ?>">
					<div class="form-feedback">
					</div>
				</div>
				<div class="form-row mb-2">
					<label for="tags_input" class="text-matcha">Tags</label>
					<div id="tags_div" class="form-control needs-validation">
						<ul class="tag_list bg-matcha-light rounded-pill" style="">
						<?php foreach ($tags as $value) { ?>
							<li class="p-1"><a href="#" role="button" data-id="<?= $value->tag_id; ?>">#<?= $value->tag_name; ?><i class="fas fa-xs fa-times ml-1"></i></a></li>
						<?php } ?>
						</ul>
						<input id="tags_input" type="text" onkeyup="this.value=this.value.replace(/\s+/gi,'')" maxlength="20" autocomplete="off" placeholder="...">
					</div>
					<div class="tags_block">
						<ul id="tag_search" class="tags_ul">
						</ul>
					</div>
					<div class="form-feedback">
					</div>
				</div>
				<div class="form-row mb-2 needs-validation">
					<label for="date_birth_input" class="text-matcha">Date of birth</label>
					<input id="date_birth_input" class="form-control" value="<?= $dob ?>" type="date">
					<div class="form-feedback">
					</div>
				</div>
				<div class="form-row mb-2 needs-validation">
					<label for="gender" class="text-matcha">Gender</label>
					<select id="gender" class="custom-select">
						<option  <?php if (empty($gender['text'])) { ?>selected<?php } ?> value="-1">Choose your gender</option>
						<option <?php if ($gender['text'] == "Male") { ?>selected<?php } ?> value="0">Male</option>
						<option <?php if ($gender['text'] == "Female") { ?>selected<?php } ?> value="1">Female</option>
					</select>
					<div class="form-feedback">
					</div>
				</div>
				<div class="form-row mb-2 needs-validation">
					<label for="sexual" class="text-matcha">Interested in</label>
					<select id="sexual" class="custom-select">
						<option <?php if ($orientation == "Men") { ?>selected<?php } ?> value="0">Men</option>
						<option <?php if ($orientation == "Women") { ?>selected<?php } ?> value="1">Women</option>
						<option <?php if ($orientation == "Men and women") { ?>selected<?php } ?> value="2">Men and women</option>
					</select>
					<div class="form-feedback">
					</div>
				</div>
				<div class="form-row mb-2 needs-validation">
					<label for="bio" class="text-matcha">About you</label>
					<textarea id="bio" class="form-control" maxlength="3000" placeholder="Bio"><?= $bio; ?></textarea>
					<div class="form-feedback">
					</div>
				</div>
				<div class="form-row mb-2">
					<button id="profile" class="btn btn-block btn-matcha" type="button"><i class="fas fa-edit mr-2"></i>Update profile</button>
				</div>
			</div>
			<div class="location">
				<div class="mb-2">
					<h3 class="text-matcha font">Your location</h3>
					<p class="fa-sm">You can either set a <span class="customloc-hint">custom location</span> or let us geolocate you and automatically update it.</p>
					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" id="geolocSwitch" <?= $geoloc === 1 ? 'checked' : '' ?>>
						<label data-toggle="tooltip" class="custom-control-label" for="geolocSwitch">Locate me automatically</label>
					</div>
				</div>
				<div id="mapid" class="my-3">
					<span class="marker-hint <?= $geoloc === 1 ? "d-none" : '' ?>" data-toggle="tooltip" data-placement="right" data-html="true">
						<i class="far fa-question-circle fa-2x text-matcha"></i>
					</span>
				</div>
				<div class="mb-2">
					<span class="disabledsave <?= $geoloc === 0 ? "d-none" : '' ?>" tabindex="0">
						<button type="button" class="btn btn-block btn-matcha" style="pointer-events: none;" disabled><i class="fas fa-map-marker-alt mr-2"></i>Save location</button>
					</span>
					<span class="notyetsave <?= $geoloc === 1 ? "d-none" : '' ?>" tabindex="0">
						<button id="location" type="button" class="btn btn-block btn-matcha" style="pointer-events: none;" disabled><i class="fas fa-map-marker-alt mr-2"></i>Save location</button>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var usr_lat = parseFloat("<?= $location["lat"]; ?>"),
		usr_long = parseFloat("<?= $location["long"]; ?>"),
		geobool = Boolean(<?= $geoloc; ?>);
</script>