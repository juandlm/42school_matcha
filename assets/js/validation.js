var pattern, input_email, input_username, input_displayname, input_password, input_login,
	errors, input, tag_list, input_gender, input_sexual, input_date_birth;

pattern				= [];
pattern['username']	= /^[a-z\d_-]{3,20}$/i;
pattern['name']		= /^[a-z]{2,32}$/i;
pattern['email']	= /^([a-zA-Z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
pattern['password']	= /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\s\S]{6,16}$/;

input_login			= $('#login');
input_email			= $('#email');
input_username 		= $('#username');
input_displayname	= $('#displayname');
input_password		= $('#password');
input_cpassword		= $('#cpassword');
input_toscheck		= $('#toscheck');
input_gender		= $('#gender');
input_sexual		= $('#sexual');
input_date_birth	= $('#date_birth_input');
input_bio			= $('#bio');
tag_list			= $('.tag_list');

var tags_input	= $("#tags_input"),
	tags_div 	= $("#tags_div"),
	tags_block 	= $(".tags_block");

$(function() {
	$('.needs-validation input, .needs-validation select, .needs-validation textarea').click(function() {
		$(this).removeClass('is-invalid is-valid');
		$(this).nextAll('.form-feedback').text('');
	});
})

function validation_good(object) {
	object.addClass('is-valid');
	object.nextAll('.form-feedback').addClass('valid-feedback').show();
}

function validation_error(object, error) {
	object.addClass('is-invalid');
	object.nextAll('.form-feedback').addClass('invalid-feedback').text(error).show();
}

//Sign-up validation
function input_username_validation() {
	var er = [];

	if (input_username.val() == '') {
		validation_error(input_username, 'Username field cannot be empty');
		er = add_element_array(er, 'Username field cannot be empty');
	} else if (!(pattern['username'].test(input_username.val()))) {
		validation_error(input_username, 'Usernames must be between 3 and 20 characters long and can only contain alphanumeric, underscore and hyphen characters');
		er = add_element_array(er, 'Usernames must be between 3 and 20 characters long and can only contain alphanumeric, underscore and hyphen characters');
	}

	if (er.length == 0) {
		$.ajax({
			type: 'POST',
			url: matchaUrl+'signup/processSignup',
			data: {
				checkexisting: true,
				username: input_username.val(),
			},
			dataType: 'json',
			// error: (xhr, textStatus, error) => {
			// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText);
			// },
			success: (data) => {
				if (data.exists == 1) {
					validation_error(input_username, 'This username is already in use');
					er = add_element_array(er, 'This username is already in use.');
				} else if (data.exists == 0)
					validation_good(input_username);
			},
		});
	}
	return (er.length);
}

function input_email_validation() {
	var er = [];

	if (input_email.val() == '') {
		validation_error(input_email, 'Email field cannot be empty');
		er = add_element_array(er, 'Email field cannot be empty');
	}
	else if (!(pattern['email'].test(input_email.val()))) {
		er = add_element_array(er, 'This doesn\'t look like a valid email');
		validation_error(input_email, 'This doesn\'t look like a valid email');
	}

	if (er.length == 0) {
		$.ajax({
			type: 'POST',
			url: matchaUrl+'signup/processSignup',
			data: {
				checkexisting: true,
				email: input_email.val(),
			},
			dataType: 'json',
			success: (data) => {
				if (data.exists == 1) {
					er = add_element_array(er, 'This email address is already in use');
					validation_error(input_email, 'This email address is already in use');
				} else if (data.exists == 0)
					validation_good(input_email);
			},
		});
	}
	return (er.length);
}

function input_password_validation() {
	var er = [];

	if (input_password.val() == '') {
		validation_error(input_password, 'Password field cannot be empty');
		er = add_element_array(er, 'Password field cannot be empty');
	} else if (!(pattern['password'].test(input_password.val()))) {
		validation_error(input_password, 'Passwords must be between 6 and 16 characters long and contain at least one uppercase letter, one lowercase letter and one digit');
		er = add_element_array(er, 'Passwords must be between 6 and 16 characters long and contain at least one uppercase letter, one lowercase letter and one digit');
	} else
		validation_good(input_password);
	return (er.length);
}

function input_cpassword_validation() {
	var er = [];

	if (input_cpassword.val() == '') {
		validation_error(input_cpassword, 'Password field cannot be empty');
		er = add_element_array(er, 'Password field cannot be empty');
	} else if (input_cpassword.val() !== input_password.val()) {
		validation_error(input_cpassword, 'The passwords don\'t match');
		er = add_element_array(er, 'The passwords don\'t match');
	} else
		validation_good(input_cpassword);
	return (er.length);
}

function input_toscheck_validation() {
	var er = [];

	if (input_toscheck.prop("checked") == false) {
		validation_error(input_toscheck, 'You have to accept the Terms of Service in order to register');
		er = add_element_array(er, 'You have to accept the Terms of Service in order to register');
	}
	return (er.length);
}


//Edit profile validation
function input_displayname_validation() {
	var er = [];

	if (input_displayname.val() == '') {
		validation_error(input_displayname, 'Display name field cannot be empty');
		er = add_element_array(er, 'Display name field cannot be empty');
	} else if (!(pattern['name'].test(input_displayname.val()))) {
		validation_error(input_displayname, 'Your display name must be between 2 and 32 characters long and can only contain letters (A–Z or a–z)');
		er = add_element_array(er, 'Your display name must be between 2 and 32 characters long and can only contain letters (A–Z or a–z)');
	} else
		validation_good(input_displayname);
	return (er.length);
}

function tag_list_validation() {
	var er = [];

	if (tag_list.children().length == 0) {
		validation_error(tags_div, 'Tags field cannot be empty');
		er = add_element_array(er, 'Tags field cannot be empty');
	} else if (tag_list.children().length < 2 || tag_list.children().length > 20) {
		validation_error(tags_div, 'Minimum 2 tags, maximum 20 tags');
		er = add_element_array(er, 'Minimum 2 tags, maximum 20 tags');
	} else
		validation_good(tags_div);
	return (er.length);
}

function date_birth_validation() {
	var er = [];
	var ar = [];

	ar = input_date_birth.val().split("-");
	if (!(input_date_birth.val())) {
		validation_error(input_date_birth, 'Date of birth field cannot be empty');
		er = add_element_array(er, 'Date of birth field cannot be empty');
	} else if (ar[0] < 1900 || ar[0] > 2001) {
		validation_error(input_date_birth, 'Enter your current age. You need to be 18 or over to use this website.');
		er = add_element_array(er, 'Enter your current age. You need to be 18 or over to use this website.');
	} else
		validation_good(input_date_birth);
	return (er.length);
}

function gender_validation() {
	var er = [];

	if (input_gender.val() == 0 || input_gender.val() == 1) {
		validation_good(input_gender);
	} else {
		validation_error(input_gender, 'Choose a gender');
		er = add_element_array(er, 'Choose a gender');
	}
	return (er.length);
}

function sexual_validation() {
	var er = [];

	if (input_sexual.val() == 0 || input_sexual.val() == 1 || input_sexual.val() == 2)
		validation_good(input_sexual);
	else {
		validation_error(input_sexual, 'Choose your sexual orientation');
		er = add_element_array(er, 'Choose your sexual orientation');
	}
	return (er.length);
}

function bio_validation() {
	var er = [];

	if (input_bio.val() == '') {
		validation_error(input_bio, 'Bio field cannot be empty');
		er = add_element_array(er, 'Bio field cannot be empty');
	} else if (input_bio.val().length < 20 || input_bio.val().length > 3000) {
		validation_error(input_bio, 'Minimum 20 length, maximum 3000 length');
		er = add_element_array(er, 'Minimum 20 length, maximum 3000 length');
	} else
		validation_good(input_bio);
	return (er.length);
}