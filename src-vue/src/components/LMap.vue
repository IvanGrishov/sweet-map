<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import type { MarkerData } from '../types';

interface Props {
  markers: MarkerData[];
  draggable: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'add-marker', value: MarkerData): void;
  (e: 'update-marker', value: MarkerData): void;
}>();

const mapContainer = ref<HTMLElement | null>(null);
let map: L.Map | null = null;
const leafletMarkers = new Map<string, L.Marker>();

const customIcon = L.divIcon({
  html: `<svg width="30" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3))">
          <path d="M12 21C16 17 20 13.4183 20 9C20 4.58172 16.4183 1 12 1C7.58172 1 4 4.58172 4 9C4 13.4183 8 17 12 21Z" fill="#4f46e5" stroke="white" stroke-width="2"/>
          <circle cx="12" cy="9" r="3" fill="white"/></svg>`,
  className: 'mlm-custom-marker',
  iconSize: [30, 42],
  iconAnchor: [15, 42],
  popupAnchor: [0, -40]
});

const renderMarkers = () => {
  if (!map || !Array.isArray(props.markers)) return;

  leafletMarkers.forEach((leafletMarker, id) => {
    if (!props.markers.find((m) => m.id === id)) {
      map?.removeLayer(leafletMarker);
      leafletMarkers.delete(id);
    }
  });

  props.markers.forEach((data) => {
    const position: L.LatLngExpression = [Number(data.lat), Number(data.lng)];

    if (!leafletMarkers.has(data.id)) {
      const newMarker = L.marker(position, {
        icon: customIcon,
        draggable: props.draggable
      }).addTo(map!);

      newMarker.bindPopup(`<strong>${data.title || 'Без названия'}</strong>`, {
        closeButton: false
      });

      newMarker.on('dragend', () => {
        const pos = newMarker.getLatLng();
        emit('update-marker', {
          ...data,
          lat: pos.lat.toFixed(6),
          lng: pos.lng.toFixed(6)
        });
      });

      leafletMarkers.set(data.id, newMarker);
    } else {
      const existingMarker = leafletMarkers.get(data.id);
      if (existingMarker) {
        existingMarker.setLatLng(position);
        const currentPopupContent = `<strong>${data.title || 'Без названия'}</strong>`;
        if (existingMarker.getPopup()?.getContent() !== currentPopupContent) {
          existingMarker.setPopupContent(currentPopupContent);
        }
      }
    }
  });
};

onMounted(async () => {
  await nextTick();
  if (!mapContainer.value) return;

  const startCoords: L.LatLngExpression =
    props.markers?.length > 0
      ? [Number(props.markers[0].lat), Number(props.markers[0].lng)]
      : [55.7512, 37.6184];

  map = L.map(mapContainer.value, {
    tap: false,
    zoomControl: true
  }).setView(startCoords, 11);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  map.on('click', (e: L.LeafletMouseEvent) => {
    if (!props.draggable) return;

    emit('add-marker', {
      id: Date.now().toString(),
      lat: e.latlng.lat.toFixed(6),
      lng: e.latlng.lng.toFixed(6),
      title: 'Новая точка'
    });
  });

  renderMarkers();
  setTimeout(() => map?.invalidateSize(), 500);
});

watch(
  () => props.markers,
  () => {
    renderMarkers();
  },
  { deep: true }
);

onUnmounted(() => {
  if (map) {
    map.remove();
    map = null;
  }
});
</script>

<template>
  <div
    class="flex-1 min-h-[31.25rem] rounded-2xl overflow-hidden border border-slate-200 shadow-xl relative z-10 bg-slate-50"
  >
    <div ref="mapContainer" class="h-full w-full"></div>

    <div v-if="draggable" class="absolute bottom-1 left-1 z-[1000] pointer-events-none">
      <div
        class="bg-white/90 backdrop-blur px-3 py-1.5 rounded-lg border border-slate-200 text-[0.6875rem] font-bold text-slate-600 shadow-sm uppercase tracking-wider"
      >
        Кликните на карту для новой точки
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Убираем @apply, чтобы избежать конфликтов с Tailwind 4 в scoped-стилях */
:deep(.mlm-custom-marker) {
  background: transparent !important;
  border: none !important;
}

:deep(.leaflet-popup-content-wrapper) {
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
  padding: 0.25rem;
  font-family: inherit;
}

:deep(.leaflet-popup-content) {
  margin: 0.5rem 0.75rem;
  font-size: 0.875rem;
  color: #1e293b;
}

:deep(.leaflet-popup-tip-container) {
  display: none;
}
</style>
