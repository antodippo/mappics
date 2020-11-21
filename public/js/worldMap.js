
var map = document.getElementById('worldmap');
var coordinatesArray = JSON.parse(map.dataset.coordinates);
var mapboxApiKey = map.dataset.mapboxapikey;

var mymap = L.map('worldmap').setView([0, 0], 1.5);
L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: '© <a href="https://www.mapbox.com/about/maps/">Mapbox</a> © <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> <strong><a href="https://www.mapbox.com/map-feedback/" target="_blank">Improve this map</a></strong>',
    tileSize: 512,
    maxZoom: 18,
    zoomOffset: -1,
    id: 'mapbox/streets-v11',
    accessToken: mapboxApiKey
}).addTo(mymap);

var i;
markers = [];
for (i = 0; i < coordinatesArray.length; i++) {
    var greenIcon = L.icon({
        iconUrl: '/galleries/' + coordinatesArray[i][2],
        iconSize: [35, 35],
        iconAnchor: [35, 35],
        popupAnchor: [-17, -17]
    });
    marker = L.marker( [ coordinatesArray[i][0], coordinatesArray[i][1] ], {icon: greenIcon} ).addTo(mymap);
    marker.bindPopup(coordinatesArray[i][3]);
    markers.push(marker);
}

var group = new L.featureGroup(markers);
mymap.fitBounds(group.getBounds());
