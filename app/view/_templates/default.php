<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= empty($title) ? $title : $title . ' - '; ?>Matcha</title>
	<link rel="apple-touch-icon" sizes="180x180" href="<?= URL ?>assets/ico/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?= URL ?>assets/ico/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?= URL ?>assets/ico/favicon-16x16.png">
	<link rel="manifest" href="<?= URL ?>assets/ico/site.webmanifest">
	<?php
		Matcha\Lib\Helper::include_all("css", $route, [
			"bootstrap.min.css",
			"all.min.css",
			"hover-min.css",
			"colors.min.css",
			"toast.css",
			"glider.min.css",
			"nouislider.min.css",
			"style.css",
		]);
	?>
	<script type="text/javascript">
	var matchaUrl = "http:<?= URL ?>",
		isLoggedIn = Boolean(<?= $_SESSION["logged_in"] = intval(isset($_SESSION['user_id'], $_SESSION['user_username'], $_SESSION['token'])) ?>);
	</script>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top p-0 px-4 ms-small">
		<a class="navbar-brand logo" href="<?= URL; ?>" style="color: #E60A54;">
			<img src="<?= URL ?>assets/images/logo.svg" width="45px" height="42px">atcha
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
			<?php
			$_SESSION["last_action"] = time();
			if ($_SESSION["logged_in"]) {
				$userManager = new Matcha\Model\UserManager;
				$user = $userManager->fetchUserData($_SESSION['user_username']);
				$userManager->updateLastSeen($user, $_SESSION['last_action']);
			?>
			<div>
				<?php if (stristr($_SERVER["REQUEST_URI"], "search") == "search") { ?>
					<div id="resultCount"></div>
				<?php } else { ?>
				<div class="form-inline input-group" onclick="location.href = matchaUrl+'search';" style="width: 300px;">
					<input class="form-control" type="search" placeholder="Looking for someone?" name="search">
					<div class="input-group-append">
						<button class="btn btn-outline-matcha" type="submit"><i class="fas fa-search"></i></button>
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="ml-auto py-3">
				<div class="dropdown mr-3 d-inline d-inline-block">
					<button type="button" class="btn btn-outline-matcha position-relative" id="notificationsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-bell fa-lg fa-fw"></i><span id="notifUnseen" class="badge badge-danger position-absolute" style="top: -5px;"></span>
					</button>
					<div class="dropdown-menu dropdown-menu-lg-right" aria-labelledby="notificationsDropdown">
						<ul class="list-group notifications">
							<?php if (!empty($notifications)) {
								foreach ($notifications as $value) { ?>
								<a class="hvr-icon-pop" href="<?= URL . "profile/v/" . $value->noti_usr_login_from ?>">
									<li class="dropdown-item <?= $value->noti_seen ? '' : "bg-matcha-light" ?>"><?= $notificationIcon[$value->noti_type] ?>
										<small>
											<span class="text-matcha">
												<?= $value->noti_usr_login_from ?>
											</span>
											<?= $notificationText[$value->noti_type] ?>
										</small>
										<div class="notif_time text-muted">
											<?= \Matcha\Lib\Helper::time_elapsed_string($value->noti_date); ?>
										</div>
									</li>
								</a>
							<?php } 
							} else {?>
								<li class="dropdown-item"><small class="text-muted">You have no notifications.</small></li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<a class="btn btn-outline-matcha mr-3 position-relative d-inline-block" href="<?= URL; ?>messages" role="button">
					<i class="fas fa-comments fa-lg fa-fw"></i><span id="msgUnseen" class="badge badge-primary position-absolute" style="top: -5px;"></span>
				</a>
				<div class="btn-group">
					<a href="<?= URL; ?>profile" role="button" class="btn btn-outline-matcha">
						<i class="fas fa-user mr-2"></i><?= $_SESSION['user_username'] ?>
					</a>
					<button type="button" class="btn btn-outline-matcha dropdown-toggle dropdown-toggle-split" id="UserDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
					<div class="dropdown-menu dropdown-menu-lg-right" aria-labelledby="UserDropdown">
						<a class="dropdown-item" href="<?= URL; ?>"><i class="fa-fw fas fa-tachometer-alt text-matcha mr-2"></i>Dashboard</a>
						<a class="dropdown-item" href="<?= URL; ?>profile"><i class="fa-fw fas fa-id-badge text-matcha mr-2"></i>Profile</a>
						<a class="dropdown-item" href="<?= URL; ?>search"><i class="fa-fw fas fa-search text-matcha mr-2"></i>User Search</a>
						<a class="dropdown-item" href="<?= URL; ?>ranking"><i class="fa-fw fas fa-trophy text-matcha mr-2"></i>Global Ranking</a>
						<a class="dropdown-item" href="<?= URL; ?>account"><i class="fa-fw fas fa-cog text-matcha mr-2"></i>Account Settings</a>
						<div class="dropdown-divider"></div>
						<a class="nav-link text-matcha" href="<?= URL; ?>account/disconnect"><i class="fa-fw fas fa-sign-out-alt mr-2"></i>Logout</a>
					</div>
				</div>
			</div>
			<?php } else { ?>
			<div class="ml-auto py-3">
				<a class="btn btn-outline-matcha mr-3" href="<?= URL; ?>login" role="button">Log in</a>
				<a class="btn btn-outline-matcha" href="<?= URL; ?>signup" role="button">Sign up</a>
			</div>
			<?php } ?>
		</div>
	</nav>
		<main class="main-wrapper">
			<div class="matcha-alerts">
				<?php include('alerts.php'); ?>
			</div>
			<?= $content_for_layout; ?>
		</main>
	<footer class="navbar fixed-bottom navbar-light bg-light">
		<span class="navbar-text copyright">
		<img alt="footer logo" id="footer-logo" class="mx-2" src="<?= URL; ?>assets/images/logo.svg">Matcha © 2019<span class="mx-2">-</span><a href="https://juan.digital">Juan De la Mata</a>
		</span>
	</footer>
<?php
	$javascript = [
		"jquery-3.4.1.min.js",
		"bootstrap.bundle.min.js",
		"moment.js",
		"toast.js",
		"glider.min.js",
		"nouislider.min.js",
		"up-button.js",
		"dev.js",
		"validation.js",
		"notifications.js"];
	if ($route == "profile" || $route == "edit")
		$javascript[] = "map.js";
	Matcha\Lib\Helper::include_all("js", $route, $javascript);
?>
<script type="text/javascript">
if (isLoggedIn) {
	window.onload = unseenNotifications();
	window.setInterval(() => {
		$.get(matchaUrl+"account/userLastAction")
	}, 20000);
	window.setInterval(unseenNotifications, 500);
}
</script>
</body>
</html>
