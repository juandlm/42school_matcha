$(function() {
	$('.sticky-top .dropdown-menu').click(function(e) {
		e.stopPropagation();
	});
	
	var default_filter_params = {
		age: [18, 99],
		distance: 6000,
		rating: [1000, 1000],
		tags: 0
	};
	
	if (filter_params == null) {
		filter_params = default_filter_params;
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
	
	$('#resetSearch').click(() => {
		$('#searchInput').val('');
		sliders.each(function(i, e) {
			if (e.id != 'ratgSlider')
				e.noUiSlider.set(default_filter_params[Object.keys(default_filter_params)[i]]);
			else
				e.noUiSlider.set([user_rating - default_filter_params.rating[0], user_rating, user_rating + default_filter_params.rating[1]]);
		});
	});
	
	$('.matcha-search form').submit(function(e) {
		e.preventDefault();
		$.ajax({
			type: 'POST',
			url: matchaUrl+'search/doUserSearch',
			data: {
				s_age: ageSlider.noUiSlider.get(),
				s_distance: distSlider.noUiSlider.get(),
				s_rating: ratgSlider.noUiSlider.get(),
				s_tags: tagSlider.noUiSlider.get(),
				s_query: $('#searchInput').val()
			},
			dataType: 'json',
			// error: (xhr, textStatus, error) => {
			// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText);
			// },
			success: (data) => {
				$('#resultCount').html('');
				if (data.status == true && data.results.length) {
					$('.search-result').remove();
					$('h3.no-results').remove();
					$('#sortBtn').hide();
					$('#resultCount').html('<span class="text-matcha">' + data.results.length + '</span> results');
					data.results.forEach(function(e, i) {
						$('#searchResults').append(`
						<div class="search-result" data-age="` + e.usr_age + `" data-distance="` + Math.floor(e.usr_dist) + `" data-rating="` + e.usr_rating + `" data-ctags="` + e.common_tags + `">
							<a href="` + matchaUrl + 'profile/v/' + e.usr_login + `">
								<svg viewBox="0 0 1 1" width="100%" height="100%">
									<image xlink:href="` + (e.usr_ppic.indexOf("http") != -1 ? e.usr_ppic : matchaUrl + "assets/userphotos/" + e.usr_ppic) + `" width="100%" height="100%" preserveAspectRatio="xMidYMid slice"/>
								</svg>
								` + (e.isonline ? '<span class="position-absolute" style="bottom: 0; left: 5px; font-size: 10px;"><i class="fas fa-circle text-success"></i></span>' : '') +
								`<div class="result-overlay">
									<div class="w-100">
										<span class="font-weight-bold">` + e.usr_name + `</span>
										<ul class="list-unstyled fa-sm w-100">
											<li><i class="fas fa-birthday-cake mr-1"></i>` + e.usr_age + ` years old</li>
											<li><i class="fas fa-map-marker-alt mr-1"></i>` + e.usr_city + `</li>
											<li><i class="fas fa-star mr-1"></i>` + e.usr_rating + ` points</li>
											` + (e.common_tags ? '<li><i class="fas fa-user-check mr-1"></i>' + e.common_tags + ' shared tag(s)</li>' : '') + `
										</ul>
									</div>
								</div>
							</a>
						</div>`);
					});
					$('#searchResults')[0].scrollIntoView({behavior: 'smooth', block: 'start'});
					$('#sortBtn').show();
				} else if (data.status == true && data.results.length == 0) {
					$('.search-result').remove();
					$('h3.no-results').remove();
					$('#sortBtn').hide();
					$('#searchResults').append('<h3 class="no-results mx-auto text-matcha mt-n5">No results.</h3>');
				}
			},
		});
	});
	
	var sortorder = true;
		
	$('.sort-menu .dropdown-menu button').click(function(e) {
		let sorted;
	
		if (sortorder == true) {
			sorted = $('.search-result').sort((a, b) => $(b).data(this.value) - $(a).data(this.value));
			sortorder = false;
			$('.sort-menu .dropdown-menu button > i').hide();
			$(this).children('i').show().addClass('fa-chevron-down').removeClass('fa-chevron-up');
		} else if (sortorder == false) {
			sorted = $('.search-result').sort((a, b) => $(a).data(this.value) - $(b).data(this.value));
			sortorder = true;
			$('.sort-menu .dropdown-menu button > i').hide();
			$(this).children('i').show().addClass('fa-chevron-up').removeClass('fa-chevron-down');
		}
		$('#searchResults').html(sorted);
	});
});