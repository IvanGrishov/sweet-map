<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import { useMarkers } from '@/composables/useMarkers';
import { MAP_LAYERS } from '@/constants';
import { MarkerData } from '@/types';

interface Props {
  draggable: boolean;
  mapStyle: string;
  mapHeight: number;
}

const props = defineProps<Props>();
let currentTileLayer: L.TileLayer | null = null;

const {
  markers,
  activeMarkerId,
  mapCenterTrigger,
  mapFlyTrigger,
  zoom,
  draftMarker,
  draftIsNew,
  openNewMarker,
  openEditMarker
} = useMarkers();

const mapContainer = ref<HTMLElement | null>(null);
let map: L.Map | null = null;
let clusterGroup: L.MarkerClusterGroup | null = null;
const leafletMarkers = new Map<string, L.Marker>();
let draftLeafletMarker: L.Marker | null = null;

const makePopupHtml = (data: MarkerData) => {
  let html = '';
  if (data.image) {
    html += `<img src="${data.image}" style="width:100%;max-height:120px;object-fit:cover;border-radius:6px;margin-bottom:6px;display:block" />`;
  }
  html += `<strong style="font-size:14px">${data.title || '...'}</strong>`;
  if (data.description) {
    html += `<div style="font-size:12px;color:#64748b;margin-top:3px">${data.description}</div>`;
  }
  if (data.link) {
    html += `<a href="${data.link}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:4px;margin-top:6px;font-size:12px;color:#4f46e5;text-decoration:none;font-weight:500">
      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      Подробнее
    </a>`;
  }
  return html;
};

const createMarkerIcon = (data: MarkerData): L.DivIcon => {
  if (data.icon) {
    return L.divIcon({
      html: `<img src="${data.icon}" style="width:40px;height:40px;object-fit:contain;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.3))" />`,
      className: 'mlm-custom-marker',
      iconSize: [40, 40],
      iconAnchor: [20, 40],
      popupAnchor: [0, -42]
    });
  }
  const color = data.color || '#4f46e5';
  return L.divIcon({
    html: `<svg width="30" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3))">
            <path d="M12 21C16 17 20 13.4183 20 9C20 4.58172 16.4183 1 12 1C7.58172 1 4 4.58172 4 9C4 13.4183 8 17 12 21Z" fill="${color}" stroke="white" stroke-width="2"/>
            <circle cx="12" cy="9" r="3" fill="white"/></svg>`,
    className: 'mlm-custom-marker',
    iconSize: [30, 42],
    iconAnchor: [15, 42],
    popupAnchor: [0, -40]
  });
};

const syncMarkers = () => {
  if (!map || !clusterGroup) return;

  const draftId = !draftIsNew.value && draftMarker.value ? draftMarker.value.id : null;

  // Remove deleted markers
  const storeIds = new Set(markers.value.map((m) => m.id));
  leafletMarkers.forEach((leafletMarker, id) => {
    if (!storeIds.has(id)) {
      clusterGroup!.removeLayer(leafletMarker);
      leafletMarkers.delete(id);
    }
  });

  markers.value.forEach((data) => {
    // Skip marker being edited — shown as draft
    if (data.id === draftId) {
      const existing = leafletMarkers.get(data.id);
      if (existing) {
        clusterGroup!.removeLayer(existing);
        leafletMarkers.delete(data.id);
      }
      return;
    }

    const position: L.LatLngExpression = [Number(data.lat), Number(data.lng)];

    if (!leafletMarkers.has(data.id)) {
      const newMarker = L.marker(position, {
        icon: createMarkerIcon(data),
        draggable: props.draggable
      });

      newMarker.bindPopup(makePopupHtml(data), { closeButton: false });

      newMarker.on('click', () => {
        openEditMarker(data.id);
      });

      newMarker.on('dragend', () => {
        const pos = newMarker.getLatLng();
        data.lat = pos.lat.toFixed(6);
        data.lng = pos.lng.toFixed(6);
      });

      clusterGroup!.addLayer(newMarker);
      leafletMarkers.set(data.id, newMarker);
    } else {
      const existingMarker = leafletMarkers.get(data.id);
      if (existingMarker) {
        const currentPos = existingMarker.getLatLng();
        if (currentPos.lat !== Number(data.lat) || currentPos.lng !== Number(data.lng)) {
          existingMarker.setLatLng(position);
        }
        existingMarker.setPopupContent(makePopupHtml(data));
        existingMarker.setIcon(createMarkerIcon(data));
      }
    }
  });
};

onMounted(async () => {
  await nextTick();
  if (!mapContainer.value) return;

  const startCoords: L.LatLngExpression =
    markers.value.length > 0
      ? [Number(markers.value[0].lat), Number(markers.value[0].lng)]
      : [55.7512, 37.6184];

  map = L.map(mapContainer.value, {
    tap: false,
    zoomControl: true
  }).setView(startCoords, zoom.value);

  updateMapStyle(props.mapStyle);

  // Cluster group for regular markers
  clusterGroup = L.markerClusterGroup({
    showCoverageOnHover: false,
    maxClusterRadius: 60,
    spiderfyOnMaxZoom: true,
    zoomToBoundsOnClick: true
  });
  map.addLayer(clusterGroup);

  map.on('click', (e: L.LeafletMouseEvent) => {
    if (!props.draggable) return;
    if (draftMarker.value) {
      draftMarker.value.lat = e.latlng.lat.toFixed(6);
      draftMarker.value.lng = e.latlng.lng.toFixed(6);
    } else {
      openNewMarker();
      nextTick(() => {
        if (draftMarker.value) {
          draftMarker.value.lat = e.latlng.lat.toFixed(6);
          draftMarker.value.lng = e.latlng.lng.toFixed(6);
        }
      });
    }
  });

  syncMarkers();

  // Draft marker — always on map directly (not in cluster)
  const initPos: L.LatLngExpression = [Number(draftMarker.value.lat), Number(draftMarker.value.lng)];
  draftLeafletMarker = L.marker(initPos, {
    icon: createMarkerIcon(draftMarker.value),
    draggable: true,
    opacity: 0.75
  }).addTo(map);
  draftLeafletMarker.bindPopup(makePopupHtml(draftMarker.value), { closeButton: false });
  draftLeafletMarker.on('dragend', () => {
    const p = draftLeafletMarker!.getLatLng();
    draftMarker.value.lat = p.lat.toFixed(6);
    draftMarker.value.lng = p.lng.toFixed(6);
  });

  setTimeout(() => map?.invalidateSize(), 400);
});

// Draft marker watcher
watch(
  draftMarker,
  (draft) => {
    if (!map || !draftLeafletMarker) return;
    const pos: L.LatLngExpression = [Number(draft.lat), Number(draft.lng)];
    const currentPos = draftLeafletMarker.getLatLng();
    if (currentPos.lat !== Number(draft.lat) || currentPos.lng !== Number(draft.lng)) {
      draftLeafletMarker.setLatLng(pos);
    }
    draftLeafletMarker.setIcon(createMarkerIcon(draft));
    draftLeafletMarker.setPopupContent(makePopupHtml(draft));
    syncMarkers();
  },
  { deep: true }
);

// Center + open popup on active marker
watch(mapCenterTrigger, (coords) => {
  if (!coords || !map) return;
  const lat = Number(coords.lat);
  const lng = Number(coords.lng);
  if (isNaN(lat) || isNaN(lng)) return;

  map.flyTo([lat, lng], map.getZoom(), { animate: true, duration: 0.3 });

  nextTick(() => {
    const regular = leafletMarkers.get(activeMarkerId.value || '');
    if (regular) {
      // Unspiderfy / zoom out of cluster so popup is visible
      clusterGroup?.zoomToShowLayer(regular, () => regular.openPopup());
    } else if (draftLeafletMarker && !draftIsNew.value) {
      draftLeafletMarker.openPopup();
    }
  });
});

watch(markers, () => syncMarkers(), { deep: true });

onUnmounted(() => {
  if (map) {
    map.remove();
    map = null;
    clusterGroup = null;
    draftLeafletMarker = null;
  }
});

watch(zoom, (newZoom) => {
  if (map) map.setZoom(Number(newZoom));
});

const updateMapStyle = (style: string) => {
  if (!map) return;
  if (currentTileLayer) map.removeLayer(currentTileLayer);
  const url = MAP_LAYERS[style as keyof typeof MAP_LAYERS] || MAP_LAYERS.osm;
  currentTileLayer = L.tileLayer(url, { attribution: '&copy; OpenStreetMap' }).addTo(map);
};

watch(() => props.mapStyle, updateMapStyle);

watch(() => props.mapHeight, () => {
  nextTick(() => map?.invalidateSize());
});

watch(mapFlyTrigger, (coords) => {
  if (!coords || !map) return;
  map.flyTo([coords.lat, coords.lng], coords.zoom ?? 15, { animate: true, duration: 0.5 });
});
</script>

<template>
  <div
    :style="{ height: `${props.mapHeight}px` }"
    class="rounded-2xl overflow-hidden border border-slate-200 shadow-xl relative z-10 bg-slate-50 sticky top-15"
  >
    <div ref="mapContainer" class="h-full w-full"></div>

    <div v-if="draggable" class="absolute bottom-4 left-4 z-1000 pointer-events-none">
      <div
        class="bg-white/90 backdrop-blur px-3 py-1.5 rounded-lg border border-slate-200 text-[0.6875rem] font-bold text-slate-600 shadow-sm uppercase tracking-wider"
      >
        {{ $t('admin.click_map_hint') }}
      </div>
    </div>
  </div>
</template>

<style scoped>
:deep(.mlm-custom-marker) {
  background: transparent !important;
  border: none !important;
}

/* Cluster styling */
:deep(.marker-cluster) {
  background-clip: padding-box;
}
:deep(.marker-cluster div) {
  width: 34px;
  height: 34px;
  margin: 3px;
  border-radius: 50%;
  text-align: center;
  font-size: 13px;
  font-weight: 700;
  font-family: inherit;
  display: flex;
  align-items: center;
  justify-content: center;
}
:deep(.marker-cluster-small) {
  background-color: rgba(99, 102, 241, 0.15);
}
:deep(.marker-cluster-small div) {
  background-color: rgba(99, 102, 241, 0.75);
  color: #fff;
}
:deep(.marker-cluster-medium) {
  background-color: rgba(79, 70, 229, 0.15);
}
:deep(.marker-cluster-medium div) {
  background-color: rgba(79, 70, 229, 0.8);
  color: #fff;
}
:deep(.marker-cluster-large) {
  background-color: rgba(67, 56, 202, 0.15);
}
:deep(.marker-cluster-large div) {
  background-color: rgba(67, 56, 202, 0.85);
  color: #fff;
}

:deep(.leaflet-popup-content-wrapper) {
  border-radius: 8px;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
  padding: 4px;
  font-family: inherit;
}
:deep(.leaflet-popup-content) {
  margin: 8px 12px;
  font-size: 14px;
  color: #1e293b;
}
:deep(.leaflet-popup-tip-container) {
  display: none;
}
</style>
