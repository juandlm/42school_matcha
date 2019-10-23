<?php
if (!empty($_SESSION['success_alert'])) {
	echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
			. $_SESSION['success_alert'] .
			'<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    			<span aria-hidden="true">&times;</span>
 			</button>
    	</div>';
	$_SESSION['success_alert'] = null;
} elseif (!empty($_SESSION['warning_alert'])) {
	echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">'
			. $_SESSION['warning_alert'] .
			'<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    			<span aria-hidden="true">&times;</span>
 			</button>
    	</div>';
	$_SESSION['warning_alert'] = null;
} elseif (!empty($_SESSION['danger_alert'])) {
	echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
			. $_SESSION['danger_alert'] .
			'<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    			<span aria-hidden="true">&times;</span>
 			</button>
    	</div>';
	$_SESSION['danger_alert'] = null;
}
?>
<div id="alert_s" class="alert alert-success alert-dismissible fade show d-none" role="alert">
	<span class="alert-text">success</span>
  <button type="button" class="close" onclick="alert_S.addClass('d-none');" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div id="alert_w" class="alert alert-warning alert-dismissible fade show d-none" role="alert">
	<span class="alert-text">warning</span>
  <button type="button" class="close" onclick="alert_W.addClass('d-none');" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div id="alert_d" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
  <span class="alert-text">danger</span>
  <button type="button" class="close" onclick="alert_D.addClass('d-none');" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>