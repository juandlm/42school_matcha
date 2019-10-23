function unseenNotifications() {
	$.ajax({
		url: matchaUrl+"account/fetchUserNotifications",
		method:"POST",
		dataType:"json",
		// error: (xhr, textStatus, error) => {
		// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText);
		// },
		success: (data) => {
			if (data.status == true) {
				$(".notifications").html(data.notifications);
				if (data.unseen_count > 0)
					$("#notifUnseen").html(data.unseen_count);
				if (data.unseen_conv > 0)
					$("#msgUnseen").html(data.unseen_conv);
			}
		}
	});
}

$("#notificationsDropdown").click(() => {
	$.ajax({
		type: 'POST',
		url: matchaUrl+'account/clearNotifications',
		dataType: 'json',
		// error: (xhr, textStatus, error) => {
		// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText);
		// },
		success: (data) => {
			if (data.status == true) {
				$("#notifUnseen").html('');
				setTimeout(() => $(".notification-item").removeClass("bg-matcha-light"), 2500);
			} else if (data.status == false && data.message) {
				alert_D.children(".alert-text").text(data.message);
				alert_D.toggleClass("d-none");
			}
		},
	});
});

