
var imageMap = document.getElementById('imageMap');
var map = L.map('imageMap').setView([imageMap.dataset.latitude, imageMap.dataset.longitude], 17);
var mapboxApiKey = imageMap.dataset.mapboxapikey;

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.jpg?access_token={accessToken}', {
    id: 'mapbox.streets',
    maxZoom: 18,
    accessToken: mapboxApiKey,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

L.marker([imageMap.dataset.latitude, imageMap.dataset.longitude]).addTo(map);