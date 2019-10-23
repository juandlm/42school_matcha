(function showHints() {
	$('#username').focus(() => $('#collapseUHint').slideDown("collapse"));
	$('#username').focusout(() => $('#collapseUHint').slideUp("collapse"));
	$('#password').focus(() => $('#collapsePHint').slideDown());
	$('#password').focusout(() => $('#collapsePHint').slideUp("collapse"));
})();

input_username.blur(() => input_username_validation());
input_email.blur(() => input_email_validation());
input_password.blur(input_password_validation);
input_cpassword.blur(input_cpassword_validation);
input_toscheck.blur(input_toscheck_validation).change((e) => {
	if (e.target.checked == false)
		input_toscheck_validation();
});

$('#signup').click(() => {
	if (!(input_email_validation())
		&& !(input_username_validation())
		&& !(input_password_validation())
		&& !(input_cpassword_validation())
		&& !(input_toscheck_validation())) {
		$.ajax({
		    type: 'POST',
		    url: matchaUrl+'signup/processSignup',
		    data: {
		    	email 	 : input_email.val(),
		    	username : input_username.val(),
				password : input_password.val(),
				cpassword: input_cpassword.val(),
				toscheck: input_toscheck.val()
		    },
		    dataType: 'json',
			// error: (xhr, textStatus, error) => {
			// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText);
			// },
		    success: (data) => {
				result = data[0];
		    	message = data[1];
		    	if (result === true && !message) {
		    		forms = $('.needs_validation');
					forms.children('.form-control').val('');
					forms.addClass('was-validated');
					location.href = matchaUrl+'signup/success';
		    	}
		    	else if (!data && message) {
					alert_D.children(".alert-text").text(message);
					alert_D.toggleClass("d-none");
		    	}
		    },
		});
	}
});