
var msgTime = new Date().toLocaleTimeString('fr-FR', { hour: "numeric", minute: "numeric"}),
	msgUrl = window.location.pathname.substring(window.location.pathname.lastIndexOf('/') + 1),
	con_login = msgUrl !== "messages" ? msgUrl : false,
	con_list, msg_list;

function fetchMessages() {
	$.ajax({
		url: matchaUrl+"messages/t/"+(con_login || ''),
		method: "GET",
		dataType:"json",
		// error: (xhr, textStatus, error) => {
		// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText + xhr);
		// },
		success: (data) => {
			if (data.status == true) {
				if (data.messages) {
					let msg_log = $('.message-log'),
						isScrolledToBottom = msg_log[0].scrollHeight - msg_log[0].clientHeight <= msg_log[0].scrollTop + 1,
						change = Boolean(msg_list != data.messages);
					
					if (change) {
						msg_log.html(data.messages);
						if (isScrolledToBottom)
							msg_log[0].scrollTop = msg_log[0].scrollHeight - msg_log[0].clientHeight;
					}
				}
				$.each(con_list, function (i, e) {
					let change = Boolean(e != data.conversations[i]);
					if (change) {
						$('.contact-popover').popover('dispose');
						$('#conList').html(function (i, d) {
							return $.each(data.conversations, (i, e) => e);
						});
						$('.contact-popover').popover({
							trigger: 'focus',
							placement: 'bottom',
							html: true,
							template: `<div class="popover" role="tooltip">
											<div class="arrow"></div>
											<h3 class="popover-header"></h3>
											<div class="popover-body p-0 m-0">
											</div>
										</div>`
						});
					}
				});
				con_list = data.conversations;
				if (data.group_date == "Today") {
					msgToday = true;
				}
			}
		}
	});
};

function addMessage(message) {
	let msg_log = $('.message-log');

	if (msgToday == false)
		msg_log.append('<div class="d-inline-block w-100 text-center my-2"><span class="badge badge-secondary">Today</span></div>');
	msg_log.append('<div class="sent"><p></p></div>');
	$('.c_active .last-message').text(message.msg_body);
	$('.message-log div:last p').text(message.msg_body);
	$('.message-log div:last p').append('<span class="message-time">' + msgTime + '</span>');

	let height = msg_log[0].scrollHeight;
	msg_log.scrollTop(height);
}

function sendMessage() {
	let message = $("#messageInput").val();
	if (message) {
		$.ajax({
			type: 'POST',
			url: matchaUrl+"messages/addMessage",
			data: {
				con_login: con_login,
				msg_body: message,
			},
			dataType: "json",
			beforeSend: (xhr) => {
				xhr.overrideMimeType("text/plain");
			},
			// error: (xhr, textStatus, error) => {
			// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText);
			// },
			success: (data) => {
				if (data.status == true) {
					addMessage(data.input_result);
					$('.match-info').fadeTo(300, 0);
					$("#messageInput").val('');
				}
			}
		});
	}
}



$(function() {
	$("#matchesSd").click(() => {
		$(".glider-contain").slideToggle("normal", () => {
		  $("#matchesSd").toggleClass("hvr-icon-hang").toggleClass("hvr-icon-bob");
		  $("#matchesSd i").toggleClass("fa-chevron-down").toggleClass("fa-chevron-up");
		  $.get(matchaUrl+"messages/userShowMatchesToggle");
		});
	});
	$("#sendMsg").click(sendMessage);
	$("#messageInput").keypress((e) => {
		if (e.which == 13) {
			sendMessage();
			return false;
		}
	});
	$('.contact-popover').popover({
		trigger: 'focus',
		placement: 'bottom',
		html: true,
		template: `<div class="popover" role="tooltip">
						<div class="arrow"></div>
						<h3 class="popover-header"></h3>
						<div class="popover-body p-0 m-0">
						</div>
					</div>`
	});

	let glider = $(".glider")[0];
	if (glider) {
		new Glider(glider, {
			slidesToShow: 'auto',
			slidesToScroll: 'auto',
			itemWidth: 80,
			exactWidth: 80,
			arrows: {
			prev: '.glider-prev',
			next: '.glider-next'
			}
		});
	}
	if (con_login) {
		let msg_log = $('.message-log');
		if (msg_log[0]) {
			let height = msg_log[0].scrollHeight;
			msg_log.scrollTop(height);
		}
	}
	
	setInterval(fetchMessages, 500);

	let r_text = new Array();
	r_text[0] = "Don't be shy, start a conversation!";
	r_text[1] = "Your matches aren't going to talk to themselves!";
	r_text[2] = "What are you waiting for? Start typing!";
	r_text[3] = "It's time to try out your best pick-up line!";
	r_text[4] = "Out of ideas? Start by saying \"Hi\"!";

	$("#msgHint").text(r_text[Math.floor(r_text.length * Math.random())]);
});



