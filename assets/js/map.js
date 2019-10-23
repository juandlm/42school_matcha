const profilePage = Boolean((window.location.pathname.split('/').indexOf("edit")) == -1);

var mymap = L.map('mapid', {
	zoomControl: profilePage ? false : true
}).setView([usr_lat, usr_long], profilePage ? 5 : 13),
	matchaMarker = L.icon({
		iconUrl: matchaUrl+'assets/images/marker.png',
		iconSize: [27, 40],
	}),
	newMarker = L.icon({
		iconUrl: matchaUrl+'assets/images/markernew.png',
		iconSize: [27, 40],
	}),
	marker = L.marker([usr_lat, usr_long], {icon: matchaMarker});

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
	maxZoom: 18,
	id: 'mapbox.light',
	accessToken: 'pk.eyJ1IjoianVhbmRsbSIsImEiOiJjazBrb213N3cwbG84M21wbmhuMG5vMnE1In0.5EV-gQqQPMeQoySr9rXBFg'
}).addTo(mymap);

marker.addTo(mymap);

if (profilePage) {
	mymap.dragging.disable();
	mymap.touchZoom.disable();
	mymap.doubleClickZoom.disable();
	mymap.scrollWheelZoom.disable();
	mymap.boxZoom.disable();
	mymap.keyboard.disable();
	if (mymap.tap)
		mymap.tap.disable();
	$('img.leaflet-marker-icon').popover({content: usr_city+", "+usr_country, placement: "top", trigger: "hover"});
}