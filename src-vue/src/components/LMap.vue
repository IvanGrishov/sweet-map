<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
  markers: Array,
  draggable: Boolean
});

const emit = defineEmits(['add-marker', 'update-marker']);
const mapContainer = ref(null);
let map = null;
const leafletMarkers = new Map();

const icon = L.divIcon({
  html: `<svg width="30" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3))">
          <path d="M12 21C16 17 20 13.4183 20 9C20 4.58172 16.4183 1 12 1C7.58172 1 4 4.58172 4 9C4 13.4183 8 17 12 21Z" fill="#ef4444" stroke="white" stroke-width="2"/>
          <circle cx="12" cy="9" r="3" fill="white"/></svg>`,
  className: 'mlm-custom-marker',
  iconSize: [30, 42],
  iconAnchor: [15, 42],
});

onMounted(async () => {
  await nextTick();
  if (!mapContainer.value) return;

  const startPos = props.markers?.length > 0 ? [props.markers[0].lat, props.markers[0].lng] : [55.75, 37.61];

  map = L.map(mapContainer.value, { tap: false }).setView(startPos, 11);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

  render();

  map.on('click', (e) => {
    if (!props.draggable) return;
    emit('add-marker', {
      id: Date.now().toString(),
      lat: e.latlng.lat.toFixed(6),
      lng: e.latlng.lng.toFixed(6),
      title: 'Новая точка'
    });
  });

  setTimeout(() => map.invalidateSize(), 500);
});

const render = () => {
  if (!map || !Array.isArray(props.markers)) return;

  // Удаляем старые
  leafletMarkers.forEach((m, id) => {
    if (!props.markers.find(x => x.id === id)) {
      map.removeLayer(m);
      leafletMarkers.delete(id);
    }
  });

  // Добавляем новые
  props.markers.forEach(data => {
    if (!leafletMarkers.has(data.id)) {
      const m = L.marker([data.lat, data.lng], { icon, draggable: props.draggable }).addTo(map);
      m.on('dragend', () => {
        const p = m.getLatLng();
        emit('update-marker', { ...data, lat: p.lat.toFixed(6), lng: p.lng.toFixed(6) });
      });
      leafletMarkers.set(data.id, m);
    } else {
      leafletMarkers.get(data.id).setLatLng([data.lat, data.lng]);
    }
  });
};

watch(() => props.markers, render, { deep: true });
</script>

<template>
  <div class="flex-1 min-h-[500px] rounded-xl overflow-hidden border border-slate-200 shadow-inner relative z-10">
    <div ref="mapContainer" class="h-full w-full bg-slate-100"></div>
  </div>
</template>

<style scoped>
:deep(.mlm-custom-marker) {
  background: transparent !important;
  border: none !important;
}
</style>
