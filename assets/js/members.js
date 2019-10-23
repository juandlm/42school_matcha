//User rating animations
$(function() {
	$('#ratinginfo').tooltip();
	$('.fire').popover({placement: "bottom"});
	
	
	$('.rating-count').each(function () {
		$(this).prop('Counter', 0).animate({
			Counter: $(this).text()
		}, {
			duration: 3000,
			easing: 'swing',
			step: function (now) {
				$(this).text(Math.ceil(now));
			},
			complete: function() {
				$('.fire').fadeIn("normal");
			}
		});
	});
});

//Filter and search for match suggestions
$(function() {
	$('.sticky-top .dropdown-menu').click(function(e) {
		e.stopPropagation();
	});
	
	if (filter_params == null) {
		filter_params = {
			age: [18, 99],
			distance: 500,
			rating: [200, 200],
			tags: 0
		};
	}
	
	var ageSlider = $('#ageSlider')[0],
		distSlider = $('#distSlider')[0],
		ratgSlider = $('#ratgSlider')[0],
		tagSlider = $('#tagSlider')[0];
	
	noUiSlider.create(ageSlider, {
		start: [filter_params.age[0], filter_params.age[1]],
		connect: true,
		range: {
			'min': 18,
			'max': 99
		}
	});
	
	noUiSlider.create(distSlider, {
		start: [filter_params.distance],
		connect: 'lower',
		range: {
			'min': [0],
			'10%': [1000, 500],
			'50%': [4000, 1000],
			'max': [6000]
		}
	});
	
	noUiSlider.create(ratgSlider, {
		start: [user_rating - filter_params.rating[0], user_rating, user_rating + filter_params.rating[1]],
		connect: [false, true, true, false],
		tooltips: [false, true, false],
		range: {
			'min': user_rating - 1000,
			'25%': user_rating - 200,
			'50%': user_rating,
			'75%': user_rating + 200,
			'max': user_rating + 1000
		}
	});
	
	noUiSlider.create(tagSlider, {
		start: [filter_params.tags],
		connect: 'lower',
		range: {
			'min': [0],
			'max': [20]
		}
	});
	
	var	ageValues = [$('#slider-age-value-lower')[0], $('#slider-age-value-upper')[0]],
		distValues = [$('#slider-dist-value-upper')[0]],
		ratgValues = [$('#slider-ratg-value-lower')[0], $('#ratgSlider .noUi-tooltip')[0], $('#slider-ratg-value-upper')[0]],
		tagValues = [$('#slider-tag-value-upper')[0]];
	
	ageSlider.noUiSlider.on('update', function (values, handle) {
		ageValues[handle].innerHTML = Math.trunc(values[handle]);
	});
	
	distSlider.noUiSlider.on('update', function (values, handle) {
		distValues[handle].innerHTML = values[handle] >= 6000 ? '&#8734;' : Math.trunc(values[handle]);
		if (values[handle] >= 1000 && values[handle] != 6000)
			$(distValues[handle]).css('right', '10px');
		else
			$(distValues[handle]).css('right', '20px');
	});
	
	$(ratgValues[1]).tooltip({title: "Your rating"});
	ratgSlider.noUiSlider.on('update', function (values, handle) {
		$(ratgValues[1]).text(Math.trunc(values[1]));
		$(ratgValues).first().text(values[0] - values[1] == -1000 ? 'any' : Math.trunc(values[0] - values[1]));
		$(ratgValues).last().text(values[2] - values[1] == 1000 ? 'any' : Math.trunc(values[2] - values[1]));
		$('#ratgSlider .noUi-origin')[1].setAttribute('disabled', true);
		if (Math.trunc(values[0] - values[1]) <= -850)
			$(ratgValues[0]).css('left', '10px');
		else
			$(ratgValues[0]).css('left', '20px');
		if (Math.trunc(values[2] - values[1]) >= 900)
			$(ratgValues[2]).css('right', '10px');
		else
			$(ratgValues[2]).css('right', '20px');
	});
	
	tagSlider.noUiSlider.on('update', function (values, handle) {
		tagValues[handle].innerHTML = Math.trunc(values[handle]);
	});
	
	var sliders = $('#ageSlider, #distSlider, #ratgSlider, #tagSlider');
	
	if ($('#smartFilterSwitch:checked').length) {
		sliders.each(function(i, e) {
			e.setAttribute('disabled', true);
		});
		$('#applyFilter').attr('disabled', true).css("pointer-events", "none");
	}
	
	$('#smartFilterSwitch').change(function() {
		$.get(matchaUrl+"home/userSmartFilterToggle");
		if (this.checked) {
			$('.sort-menu .dropdown-menu button > i').hide();
			sliders.each(function(i, e) {
				e.setAttribute('disabled', true);
			});
			$('#applyFilter').attr('disabled', true).css("pointer-events", "none");
			$('.suggestions').html(`
			<div class="loader-wrapper">
				<span class="loader">
					<span class="loader-inner text-center">
						<i class="fas fa-heart text-matcha"></i>
					</span>
				</span>
			</div>`);
			$('#suggcount').text("Searching for love...");
			setTimeout(() => {
				$('.suggestions').load(document.URL + ' .suggestions>*', function() {
				if (this.childElementCount == 1)
					$('#suggcount').text("We found " + this.childElementCount + " potential match");
				else if (this.childElementCount > 1)
					$('#suggcount').text("We found " + this.childElementCount + " potential matches");
				else
					$('#suggcount').text("We found nobody 😔, try changing your filter settings");
			})}, 1000);		
		} else {
			sliders.each(function(i, e) {
				e.removeAttribute('disabled');
			});
			$('#applyFilter').attr('disabled', false).css("pointer-events", "auto");
		}
	});
	
	$('#applyFilter').click(() => {
		$('.sort-menu .dropdown-menu button > i').hide();
		$('.suggestions').html(`
		<div class="loader-wrapper">
			<span class="loader">
				<span class="loader-inner text-center">
					<i class="fas fa-heart text-matcha"></i>
				</span>
			</span>
		</div>`);
		$('#suggcount').text("Searching for love...");
		setTimeout(() => {
			$('.suggestions').load(document.URL + ' .suggestions>*', {
			f_age: ageSlider.noUiSlider.get(),
			f_distance: distSlider.noUiSlider.get(),
			f_rating: ratgSlider.noUiSlider.get(),
			f_tags: tagSlider.noUiSlider.get()
		}, function() {
			if (this.childElementCount == 1)
				$('#suggcount').text("We found " + this.childElementCount + " potential match");
			else if (this.childElementCount > 1)
				$('#suggcount').text("We found " + this.childElementCount + " potential matches");
			else
				$('#suggcount').text("We found nobody 😔, try changing your filter settings");
		})}, 1800);
	});
	
	var sortorder = true;
	
	$('.sort-menu .dropdown-menu button').click(function(e) {
		let sorted;
	
		if (sortorder == true) {
			sorted = $('.suggestion-item').sort((a, b) => $(b).data(this.value) - $(a).data(this.value));
			sortorder = false;
			$('.sort-menu .dropdown-menu button > i').hide();
			$(this).children('i').show().addClass('fa-chevron-down').removeClass('fa-chevron-up');
		} else if (sortorder == false) {
			sorted = $('.suggestion-item').sort((a, b) => $(a).data(this.value) - $(b).data(this.value));
			sortorder = true;
			$('.sort-menu .dropdown-menu button > i').hide();
			$(this).children('i').show().addClass('fa-chevron-up').removeClass('fa-chevron-down');
		}
		$('.suggestions').html(sorted);
	});	
});
