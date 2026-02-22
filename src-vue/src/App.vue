<script setup>
import { ref, onMounted, computed, nextTick } from 'vue';
// В Leaflet 2.0+ используем именованные импорты.
// divIcon необходим для отрисовки SVG вместо PNG-картинок.
import { Map, TileLayer, Marker, divIcon } from 'leaflet';
import 'leaflet/dist/leaflet.css';
import AdminSettings from './components/AdminSettings.vue';

/**
 * CONFIG & CONSTANTS
 */
const DEFAULT_COORDS = { lat: '55.751244', lng: '37.618423' };
const isDev = import.meta.env.DEV;

// Состояние координат
const lat = ref(window.wpData?.coords?.lat || DEFAULT_COORDS.lat);
const lng = ref(window.wpData?.coords?.lng || DEFAULT_COORDS.lng);
const isSaving = ref(false);
const mapContainer = ref(null);

// Ссылки на объекты Leaflet (нереактивные, чтобы избежать конфликтов с Proxy)
let map = null;
let marker = null;

/**
 * COMPUTED
 */
const isWPAdmin = computed(() => window.location.pathname.includes('wp-admin'));
const showSettings = computed(() => isDev || isWPAdmin.value);

/**
 * HELPER FUNCTIONS
 */
const updateCoords = (newLat, newLng) => {
  lat.value = newLat.toFixed(6).toString();
  lng.value = newLng.toFixed(6).toString();
};

const updateMapFromInputs = () => {
  const nLat = parseFloat(lat.value);
  const nLng = parseFloat(lng.value);

  if (!isNaN(nLat) && !isNaN(nLng) && marker && map) {
    marker.setLatLng([nLat, nLng]);
    map.panTo([nLat, nLng]);
  }
};

/**
 * API ACTIONS (WordPress REST API)
 */
const saveToWP = async () => {
  if (isDev) {
    console.log('Dev Save Logic:', { lat: lat.value, lng: lng.value });
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

    if (!response.ok) throw new Error('Ошибка сервера');

    const result = await response.json();
    if (result.success) alert('Координаты успешно сохранены!');
  } catch (e) {
    console.error('Save error:', e);
    alert('Не удалось сохранить данные');
  } finally {
    isSaving.value = false;
  }
};

/**
 * LIFECYCLE
 */
onMounted(async () => {
  await nextTick();
  if (!mapContainer.value) return;

  // 1. Инициализация карты (Leaflet 2.0 использует конструктор new Map)
  map = new Map(mapContainer.value).setView([parseFloat(lat.value), parseFloat(lng.value)], 13);

  new TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  // 2. Создание SVG-иконки (решает проблему исчезающих картинок в Vite)
  const svgIcon = divIcon({
    html: `
      <svg width="30" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3))">
        <path d="M12 21C16 17 20 13.4183 20 9C20 4.58172 16.4183 1 12 1C7.58172 1 4 4.58172 4 9C4 13.4183 8 17 12 21Z" fill="#ef4444" stroke="white" stroke-width="2"/>
        <circle cx="12" cy="9" r="3" fill="white"/>
      </svg>
    `,
    className: 'mlm-custom-svg-marker',
    iconSize: [30, 42],
    iconAnchor: [15, 42],
  });

  // 3. Добавление маркера
  marker = new Marker([parseFloat(lat.value), parseFloat(lng.value)], {
    icon: svgIcon,
    draggable: showSettings.value
  }).addTo(map);

  // 4. Обработка событий в режиме админки
  if (showSettings.value) {
    // Перетаскивание маркера
    marker.on('dragend', () => {
      const pos = marker.getLatLng();
      updateCoords(pos.lat, pos.lng);
    });

    // Клик по карте (маркер прыгает в место клика)
    map.on('click', (e) => {
      const { lat: clickLat, lng: clickLng } = e.latlng;
      marker.setLatLng([clickLat, clickLng]);
      updateCoords(clickLat, clickLng);
      map.panTo([clickLat, clickLng]);
    });
  }

  // Исправляем баг с серой картой при загрузке в скрытых контейнерах
  setTimeout(() => map.invalidateSize(), 300);
});
</script>

<template>
  <div :class="['mlm-plugin-root', { 'is-admin-view': showSettings }]">
    <AdminSettings
      v-if="showSettings"
      v-model:lat="lat"
      v-model:lng="lng"
      :is-saving="isSaving"
      :is-dev="isDev"
      @save="saveToWP"
      @input-change="updateMapFromInputs"
    />

    <div class="mlm-map-outer !max-w-full !w-full !mx-0">
      <div ref="mapContainer" class="mlm-canvas"></div>
    </div>
  </div>
</template>

<style lang="scss">
.mlm-plugin-root {
  display: flex;
  gap: 1.25rem;
  width: 100%;
  box-sizing: border-box;
  font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
  padding: 1rem;

  .mlm-map-outer {
    flex: 1;
    min-height: 31.25rem;
    border-radius: 0.9375rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;

    .mlm-canvas {
      height: 31.25rem;
      width: 100%;
      z-index: 1;
      background: #f1f5f9;
    }
  }

  // Стилизация SVG-маркера (убираем дефолтные стили Leaflet)
  .mlm-custom-svg-marker {
    background: transparent !important;
    border: none !important;
    display: flex !important;
    align-items: center;
    justify-content: center;
  }
}

// Адаптация для мобильных устройств (админка WP)
@media (max-width: 768px) {
  .mlm-plugin-root {
    flex-direction: column;
    padding: 0.5rem;
  }
}
</style>
