<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import L from 'leaflet'; // Возвращаем глобальный объект L
import 'leaflet/dist/leaflet.css';

const props = defineProps({
  lat: [String, Number],
  lng: [String, Number],
  draggable: Boolean
});

const emit = defineEmits(['update:coords']);
const mapContainer = ref(null);
let map = null;
let marker = null;

onMounted(async () => {
  await nextTick();
  if (!mapContainer.value) return;

  // В 1.9.4 используем L.map и L.tileLayer
  map = L.map(mapContainer.value).setView([parseFloat(props.lat), parseFloat(props.lng)], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  // Используем L.divIcon для SVG
  const svgIcon = L.divIcon({
    html: `
      <svg width="30" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3))">
        <path d="M12 21C16 17 20 13.4183 20 9C20 4.58172 16.4183 1 12 1C7.58172 1 4 4.58172 4 9C4 13.4183 8 17 12 21Z" fill="#ef4444" stroke="white" stroke-width="2"/>
        <circle cx="12" cy="9" r="3" fill="white"/>
      </svg>`,
    className: 'mlm-custom-svg-marker',
    iconSize: [30, 42],
    iconAnchor: [15, 42],
  });

  marker = L.marker([parseFloat(props.lat), parseFloat(props.lng)], {
    icon: svgIcon,
    draggable: props.draggable
  }).addTo(map);

  // События
  marker.on('dragend', () => {
    const pos = marker.getLatLng();
    emit('update:coords', pos.lat, pos.lng);
  });

  map.on('click', (e) => {
    if (!props.draggable) return;
    marker.setLatLng(e.latlng);
    map.panTo(e.latlng);
    emit('update:coords', e.latlng.lat, e.latlng.lng);
  });

  setTimeout(() => map.invalidateSize(), 300);
});

// Следим за внешними изменениями (например, из инпутов)
watch(() => [props.lat, props.lng], ([newLat, newLng]) => {
  if (marker && map) {
    const pos = [parseFloat(newLat), parseFloat(newLng)];
    marker.setLatLng(pos);
    map.panTo(pos);
  }
});
</script>

<template>
  <div class="mlm-map-outer">
    <div ref="mapContainer" class="mlm-canvas"></div>
  </div>
</template>

<style lang="scss" scoped>
.mlm-map-outer {
  flex: 1;
  min-height: 500px;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #e2e8f0;

  .mlm-canvas {
    height: 500px;
    width: 100%;
    background: #f1f5f9;
  }
}

:deep(.mlm-custom-svg-marker) {
  background: transparent !important;
  border: none !important;
  display: flex !important;
  align-items: center;
  justify-content: center;
}
</style>
