<script setup>
import { ref, onMounted, computed, nextTick } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import AdminSettings from './components/AdminSettings.vue';

const defaultLat = '55.751244';
const defaultLng = '37.618423';

const lat = ref(window.wpData?.coords?.lat || defaultLat);
const lng = ref(window.wpData?.coords?.lng || defaultLng);

const mapContainer = ref(null);
const isSaving = ref(false);

let map = null;
let marker = null;

const isDev = import.meta.env.DEV;
const isWPAdmin = computed(() => window.location.pathname.includes('wp-admin'));
const showSettings = computed(() => isDev || isWPAdmin.value);

onMounted(async () => {
  await nextTick();
  if (!mapContainer.value) return;

  map = L.map(mapContainer.value).setView([parseFloat(lat.value), parseFloat(lng.value)], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  marker = L.marker([parseFloat(lat.value), parseFloat(lng.value)], {
    draggable: showSettings.value
  }).addTo(map);

  if (showSettings.value) {
    marker.on('dragend', () => {
      const position = marker.getLatLng();
      lat.value = position.lat.toFixed(6).toString();
      lng.value = position.lng.toFixed(6).toString();
    });
  }

  setTimeout(() => {
    map.invalidateSize();
  }, 200);
});

const updateMapFromInputs = () => {
  const nLat = parseFloat(lat.value);
  const nLng = parseFloat(lng.value);
  if (!isNaN(nLat) && !isNaN(nLng)) {
    marker.setLatLng([nLat, nLng]);
    map.panTo([nLat, nLng]);
  }
};

const saveToWP = async () => {
  if (isDev) {
    alert(`Имитация сохранения!\nLat: ${lat.value}\nLng: ${lng.value}`);
    return;
  }
  isSaving.value = true;
  try {
    const response = await fetch(`${window.wpData.rest_url}/save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.wpData.nonce
      },
      body: JSON.stringify({ lat: lat.value, lng: lng.value })
    });
    const result = await response.json();
    if (result.success) alert('Сохранено в WordPress!');
  } catch (e) {
    console.error(e);
    alert('Ошибка API');
  } finally {
    isSaving.value = false;
  }
};
</script>

<template>
  <div :class="['mlm-wrapper', { 'is-admin': showSettings }]">
    <AdminSettings
      v-if="showSettings"
      v-model:lat="lat"
      v-model:lng="lng"
      :is-saving="isSaving"
      :is-dev="isDev"
      @save="saveToWP"
      @input-change="updateMapFromInputs"
    />

    <div class="mlm-map-container">
      <div ref="mapContainer" class="admin-map-canvas"></div>
    </div>
  </div>
</template>

<style lang="scss">
:root {
  background-color: #ffffff !important;
  color: #2c3e50 !important;
}

body {
  margin: 0;
  display: block !important;
  place-items: unset !important;
}

.mlm-wrapper {
  display: flex;
  gap: 20px;
  width: 100%;
  padding: 20px;
  box-sizing: border-box;
  font-family: sans-serif;

  .mlm-map-container {
    flex: 1;
    min-height: 500px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);

    .admin-map-canvas {
      height: 500px;
      width: 100%;
      z-index: 1;
    }
  }
}

.leaflet-default-icon-path {
  background-image: url(https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png);
}
</style>
