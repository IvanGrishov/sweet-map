document.addEventListener('DOMContentLoaded', function () {
  // Данные придут из PHP через wp_localize_script
  const lat = parseFloat(mlm_settings.lat);
  const lng = parseFloat(mlm_settings.lng);

  const map = L.map('map').setView([lat, lng], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OSM'
  }).addTo(map);

  const marker = L.marker([lat, lng], {draggable: true}).addTo(map);

  marker.on('dragend', function (e) {
    const pos = marker.getLatLng();
    document.getElementById('lat_input').value = pos.lat.toFixed(6);
    document.getElementById('lng_input').value = pos.lng.toFixed(6);
  });
});
