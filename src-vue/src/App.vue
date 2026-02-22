<script setup>
import { ref, computed } from 'vue';
import AdminSettings from './components/AdminSettings.vue';
import LMap from './components/LMap.vue';

const DEFAULT_COORDS = { lat: '55.751244', lng: '37.618423' };
const isDev = import.meta.env.DEV;

const lat = ref(window.wpData?.coords?.lat || DEFAULT_COORDS.lat);
const lng = ref(window.wpData?.coords?.lng || DEFAULT_COORDS.lng);
const isSaving = ref(false);

const isWPAdmin = computed(() => window.location.pathname.includes('wp-admin'));
const showSettings = computed(() => isDev || isWPAdmin.value);

const handleCoordsUpdate = (newLat, newLng) => {
  lat.value = newLat.toFixed(6).toString();
  lng.value = newLng.toFixed(6).toString();
};

const saveToWP = async () => {
  if (isDev) {
    alert(`Сохранено (Dev): ${lat.value}, ${lng.value}`);
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
    if (result.success) alert('Успешно сохранено!');
  } catch (e) {
    alert('Ошибка API');
  } finally {
    isSaving.value = false;
  }
};
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
    />

    <LMap
      :lat="lat"
      :lng="lng"
      :draggable="showSettings"
      @update:coords="handleCoordsUpdate"
    />
  </div>
</template>

<style lang="scss">
.mlm-plugin-root {
  display: flex;
  gap: 1.25rem;
  width: 100%;
  box-sizing: border-box;
  font-family: sans-serif;
  padding: 1rem;
}

@media (max-width: 768px) {
  .mlm-plugin-root { flex-direction: column; }
}
</style>
