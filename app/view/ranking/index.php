<?php
	$title = "Global ranking";
?>
<div class="mt-3 container">
	<div class="jumbotron jumbotron-fluid bg-white p-4">
		<h2 class="text-matcha">Global user ranking</h2>
		<div class="table-responsive">
			<table class="table table-striped table-sm text-center">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Location</th>
						<th>Gender</th>
						<th>Rating</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($ranking as $key => $r): ?>
					<tr>
						<td class="position"><?= ($key >= 0 && $key <= 2 ? '<i class="fas fa-award fa-2x fa-fw"></i>' : strval($key + 1)) ?></td>
						<td><?php if ($_SESSION["logged_in"]) {
								echo '<a class="text-matcha" href="'. URL . 'profile/v/' . $r->usr_login .'">' . $r->usr_name . '</a>';
							} else
								echo $r->usr_name ?></td>
						<td><?php $flag = "assets/images/flags/" . $r->usr_flag . ".svg";
							echo $r->usr_city;
							if (file_exists(ROOT . $flag))
								echo '<img title="'. $r->usr_country . '" class="flag d-inline ml-2" src="' . URL . $flag . '" width="25px" height="25px">';
							?>
						</td>
						<td><?= $genders[$r->usr_gender]["icon"] ?></td>
						<td><?= $r->usr_rating ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>