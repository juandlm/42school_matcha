//Tags
function searchTags() {
	if (tags_input.val()) {
		tags_block.show();
		$.ajax({
			type: 'POST',
			url: matchaUrl+'profile/processTagSearch',
			data: {
				tags: tags_input.val(),
			},
			dataType: 'json',
			// error: (xhr, textStatus, error) => {
			// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText);
			// },
			success: (data) => {
				$("#tag_search").children().remove();
				if (data.status == true) {
					if (data.tags) {
						data.tags.forEach((i) => {
							$("#tag_search").append("<li><button type=\"button\" class=\"btn btn-matcha\" data-id=" + i['tag_id'] +">#"+ i['tag_name'] +"</button></li>");
						});
					} else
						$("#tag_search").append('<li>No tags found. Press enter or space to create this one.</li>');
				} 
			},
		});
	}
}

function addTag(id = false, tagname) {
	let currtags = [];

	Array.from($('.tag_list').children('li'), function (item, i) {
		currtags[i] = item.innerText.replace('#', '');
	});
	
	if (tagname && currtags.includes(tagname) == false) {
		tagname = tagname.replace(/[^a-zA-Z]/g, "");
		tags_input.val('');
		tags_input.focus();
		$(".tag_list").append("<li class=\"p-1\"><a href=\"#\" role=\"button\" data-id=" + id +">#" + tagname +"<i class=\"fas fa-xs fa-times ml-1\"></i></a></li>");
	}
}

function delTag(e) {
	$(e).parent('li').remove();
	tags_input.focus();
}

function parseTags() {
	let tags = [];

	tag_list.children().each(function(i) {
		   tags[i] = {
			   id: $(this).children().data("id"),
			   name: $(this).children().text().replace('#', '').trim()
			};
	});
	return (tags);
}

tags_input.keydown((e) => {
	if ((e.which == 13 || e.which == 32) && tags_input.val()) {
		addTag(false, tags_input.val());
	} else if (e.which == 8 && !tags_input.val()) {
		$('.tag_list li:last').remove();
		tags_block.hide();
	}
}).keyup(searchTags);

tags_input.focusin(() => {
	tags_div.addClass('tags_div_focus');
});

tags_input.blur(() => {
	tags_div.removeClass('tags_div_focus');
});

$('div.needs-validation').click(() => {
	tags_block.hide();
})


//Real-time validation
input_displayname.blur(input_displayname_validation);
input_gender.blur(gender_validation);
input_sexual.blur(sexual_validation);
input_date_birth.blur(date_birth_validation);
input_bio.blur(bio_validation);
$('.tag_list').on("click", "a", function() {
	delTag(this);
	tags_div.removeClass('is-invalid is-valid');
	tags_div.nextAll('.form-feedback').text('');
	tag_list_validation();
});
$('#tag_search').on("click", "button", function() {
	addTag($(this).data("id"), $(this).text().replace('#', ''));
	tags_div.removeClass('is-invalid is-valid');
	tags_div.nextAll('.form-feedback').text('');
	tag_list_validation();
});


$('#profile').click(function() {
	if (!(input_displayname_validation())
		&& !(tag_list_validation())
		&& !(gender_validation())
		&& !(sexual_validation())
		&& !(date_birth_validation())
		&& !(bio_validation())) {
		$.ajax({
			type: 'POST',
			url: matchaUrl+'profile/processEdit',
			data: {
				displayname: input_displayname.val(),
				tags: parseTags(),
				gender: input_gender.val(),
				sexual: input_sexual.val(),
				date_birth: input_date_birth.val(),
				bio: input_bio.val()
			},
			dataType: 'json',
			// error: (xhr, textStatus, error) => {
			// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText);
			// },
			success: (data) => {
				if (data.status == true) {
					forms = $('.needs_validation');
					forms.addClass('was-validated');
					location.href = matchaUrl+'profile/v';
				}
			},
		});
	}
});

//Geolocation
var	cuslocmarker,
	cuslocdata,
	geocoder = L.Control.Geocoder.mapbox('pk.eyJ1IjoianVhbmRsbSIsImEiOiJjazBrb213N3cwbG84M21wbmhuMG5vMnE1In0.5EV-gQqQPMeQoySr9rXBFg', {geocodingQueryParams: {types: "locality,place,district,region,country", language: "en"}, reverseQueryParams: {types: "locality,place,district,region,country", language: "en"}}),
	locate = () => {
		mymap.locate({
			setView: true,
			maxZoom: 18,
			enableHighAccuracy: true
		});
	},
	cuslocgeocode = (e) => {
		geocoder.reverse(e.latlng, mymap.options.crs.scale(mymap.getZoom()), function(results) {
			let r = results[0];
	
			if (r) {
				if (cuslocmarker) {
					cuslocmarker.setLatLng(r.center).setPopupContent(r.html || r.name).openPopup();
				} else {
					cuslocmarker = L.marker(r.center, {icon: newMarker}).bindPopup(r.name).addTo(mymap).openPopup();
				}
				cuslocdata = parseFoundLocation(results);
				cuslocdata.push([results[0].center.lat, results[0].center.lng]);
				$('.notyetsave').tooltip('disable');
				$('#location').prop("disabled", false).css("pointer-events", "auto");
			}
		});
	};

$(function() {
	$('.disabledsave').tooltip({title: "Your location is being automatically set, no need to save it"});
	$('.notyetsave').tooltip({title: "You haven't changed your location"});
	$('.marker-hint').tooltip({title: `	<ul class="p-0 m-0 list-unstyled fa-sm text-left">
											<li><i class="fas fa-map-marker-alt text-matcha mr-1"></i>Your previously saved location<li>
											<li><i class="fas fa-map-marker-alt text-success mr-1"></i>Your new location</li>
										</ul>`});
	$('.customloc-hint').hover(() => {
		$('.leaflet-marker-pane').addClass("customloc-anim");
		$('.leaflet-marker-pane img').first().attr("src", "../assets/images/markernew.png");
	}, () => {
		$('.leaflet-marker-pane').removeClass("customloc-anim");
		$('.leaflet-marker-pane img').first().attr("src", "../assets/images/marker.png");
	}).tooltip({title: "Choose your location by clicking it on the map"});
	
	navigator.permissions.query({
		name: 'geolocation'
	})
	.then((value) => {
		if (value.state == 'denied') {
			$('#geolocSwitch').prop("disabled", true);
			$('#geolocSwitch + label').tooltip({title: "Denied permission for geolocation"}).mouseenter(function() {
				$(this).css("cursor", "not-allowed");
			});
			geobool = false;
		} else if ((value.state == 'granted' || value.state == 'prompt') && geobool) {
			locate();
		}
	});
});

$('#geolocSwitch').click(function(e) {
	if (e.target.checked == true) {
		geobool = true;
		locate();
		if (cuslocmarker) {
			cuslocmarker.remove();
			cuslocmarker = null;
		}
	} else if (e.target.checked == false) {
		geobool = false;
		mymap.setZoom(3);
		$.post(matchaUrl+"profile/processGeolocation", false)
		// .always((d) => console.log(d));
	}
	handleLocationPreference();
	$('.notyetsave, .disabledsave, .marker-hint').toggleClass("d-none");
});

if (geobool == false) {
	$('#location').click(function() {
		if (cuslocdata) {
			$.ajax({
				type: 'POST',
				url: matchaUrl+'profile/processGeolocation',
				data: {
					usercity: cuslocdata[0],
					usercountry: cuslocdata[1],
					userpos: cuslocdata[2],
					geo: +geobool
				},
				// error: (xhr, textStatus, error) => {
				// 	console.log(error + " (" + textStatus + ") \n" + xhr.responseText);
				// },
				success: () => {
					alert_S.children(".alert-text").text("Your location has been saved.");
					alert_S.removeClass("d-none");
				}
			});
		}
	});
}

function handleLocationPreference() {
	if (geobool == false) {
		mymap.on('click', cuslocgeocode);
	} else if (geobool == true) {
		mymap.off('click', cuslocgeocode);
	}
}handleLocationPreference();

function parseFoundLocation(results) {
	let place = results[(results.length <= 3) === true ? 0 : 1].name.split(",");
	return ([place[0].trim(), place[place.length-1].trim()]);
}

function onLocationFound(e) {
	let userpos = [e.latitude, e.longitude],
		usercity,
		usercountry;
	
	marker.setLatLng(e.latlng);
	geocoder.reverse(e.latlng, 1, (e) => {
		let res = parseFoundLocation(e);
		usercity = res[0];
		usercountry = res[1];
		$.post(matchaUrl+"profile/processGeolocation", {userpos, usercity, usercountry, geo: +geobool})
		// .always((d) => console.log(d));
	});
	this.setZoom(14);
}

function onLocationError(e) {
	$.post(matchaUrl+"profile/processGeolocation", false)
	document.location.reload(true);
}

mymap.on('locationfound', onLocationFound);
mymap.on('locationerror', onLocationError);