
var map = document.getElementById('mapid');
var coordinatesArray = JSON.parse(map.dataset.coordinates);
var mapboxApiKey = map.dataset.mapboxapikey;

var mymap = L.map('mapid').setView([0, 0], 1.5);
L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.jpg?access_token={accessToken}', {
    id: 'mapbox.light',
    maxZoom: 18,
    accessToken: mapboxApiKey,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(mymap);

var i;
markers = [];
for (i = 0; i < coordinatesArray.length; i++) {
    var greenIcon = L.icon({
        iconUrl: '../images/marker.png',
        iconSize: [22, 36],
        iconAnchor: [11, 36],
        popupAnchor: [0, -36]
    });
    marker = L.marker( [ coordinatesArray[i][0], coordinatesArray[i][1] ], {icon: greenIcon} ).addTo(mymap);
    marker.bindPopup(coordinatesArray[i][2]);
    markers.push(marker);
}

var group = new L.featureGroup(markers);
mymap.fitBounds(group.getBounds());
