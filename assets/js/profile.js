var profile_username = window.location.pathname.substring(window.location.pathname.lastIndexOf('/') + 1);

// Tooltips and popovers
$(() => $('[data-toggle="popover"]').popover());
$('#picLimitData').tooltip();
$('.profile-tooltip').tooltip();


// Picture upload
var mimeTypes = ["image/jpg", "image/jpeg", "image/png"],
	validFiles = [],
	formData = new FormData();

function startUpload(argfiles) {
	if (!argfiles)
		return alert("No file was selected.");
	if (argfiles.length > 1)
		return alert("You can only upload one file at a time.");
	for (let i = 0; i < argfiles.length; i++) {
		if (mimeTypes.indexOf(argfiles[i].type) == -1)
			return alert("Only .jpeg and .png files are allowed.");
		else if (argfiles[i].size > 2000000)
			return alert("Maximum image size is 2MB.");
		else {
			validFiles.push(argfiles[i]);
			formData.append("image", argfiles[i]);
		}
	}
	$.ajax({
		type: 'POST',
		url: matchaUrl+"profile/uploadUserPicture",
		data: formData,
		dataType: 'json',
		cache: false,
		contentType: false,
        processData: false,
		// error: (xhr, textStatus, error) => {
		// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText);
		// },
		success: (data) => {
			if (data.status == true) {
				formData = new FormData();
				validFiles = [];
				$('#imgUl').value = '';
				return location.reload();
			}
			else if (!data || data.status == false) {
				alert(data.error || 'Something went wrong');
				return location.reload();
			}
		},
	});
}

$('#imgUl').on("change", function(e) {
	e.preventDefault();
	startUpload(this.files);
});


// Like
function processLike(e) {
	$.ajax({
		type: 'POST',
		url: matchaUrl+'profile/processLike',
		data: {
			receiver_username: profile_username
		},
		dataType: 'json',
		// error: (xhr, textStatus, error) => {
		// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText);
		// },
		success: (data) => {
			if (data.status == true) {
				if (e.target.name == "like_first") {
					$.toast({
						title: '💖 Love is in the air',
						subtitle: moment().format('H:mm'),
						content: 'You have liked <b>'+like_to_name+'</b>!',
						type: 'matcha',
						delay: 5000
					});
				} else if (e.target.name == "like_again") {
					$.toast({
						title: '💝 Persistence is key',
						subtitle: moment().format('H:mm'),
						content: 'You sent <b>'+like_to_name+'</b> another like!',
						type: 'matcha',
						delay: 5000
					});
				} else if (e.target.name == "match") {
					$.toast({
						title: '💘 You like each other',
						subtitle: moment().format('H:mm'),
						content: 'You matched with <b>'+like_to_name+'</b>. Congratulations!',
						type: 'matcha',
						delay: 5000
					});
				} else if (e.target.name == "unmatch") {
					$.toast({
						title: '🤔 It was never meant to be',
						subtitle: moment().format('H:mm'),
						content: 'You unmatched <b>'+like_to_name+'</b>.',
						type: 'info',
						delay: 5000
					});
				}
				$('.prf-main').load(document.URL + ' .prf-main>*', () => $('[data-toggle="popover"]').popover('update'));
			}
		},
	});
}

$("#actionBtn").click((e) => {
	e.preventDefault();
	if (e.target.name == "unmatch_ask") {
		$('#confirmUnmatchModal').modal();
		return false;
	} else
		processLike(e);
	$(this).off(e);
});
$('#confirmUnmatch').one("click", (e) => {
	processLike(e);
	$('#confirmUnmatchModal').modal("hide");
});
