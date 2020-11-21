
var imageMap = document.getElementById('imageMap');
var map = L.map('imageMap').setView([imageMap.dataset.latitude, imageMap.dataset.longitude], 17);
var mapboxApiKey = imageMap.dataset.mapboxapikey;

L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: '© <a href="https://www.mapbox.com/about/maps/">Mapbox</a> © <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    tileSize: 512,
    maxZoom: 18,
    zoomOffset: -1,
    id: 'mapbox/streets-v10',
    accessToken: mapboxApiKey
}).addTo(map);

L.marker([imageMap.dataset.latitude, imageMap.dataset.longitude]).addTo(map);