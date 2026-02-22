<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import LMap from './components/LMap.vue';
import AdminSettings from './components/AdminSettings.vue';

// Типизация для маркера
interface MarkerData {
  id: string;
  lat: string | number;
  lng: string | number;
  title: string;
}

const markers = ref<MarkerData[]>([]);
const isSaving = ref(false);
const isDev = import.meta.env.DEV;

onMounted(() => {
  // Защищенная загрузка данных из WordPress
  if (window.wpData?.coords && Array.isArray(window.wpData.coords)) {
    markers.value = [...window.wpData.coords];
  }
});

const isAdmin = computed(() => isDev || window.wpData?.is_admin);

// --- Методы управления списком ---

const addMarker = (newMarker: MarkerData) => {
  markers.value.push(newMarker);
};

const updateMarker = (updated: MarkerData) => {
  const index = markers.value.findIndex(m => m.id === updated.id);
  if (index !== -1) {
    markers.value[index] = updated;
  }
};

const removeMarker = (id: string) => {
  markers.value = markers.value.filter(m => m.id !== id);
};

const handleSave = async () => {
  isSaving.value = true;
  try {
    const response = await fetch(`${window.wpData.rest_url}/save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.wpData.nonce
      },
      body: JSON.stringify({ markers: markers.value })
    });

    if (!response.ok) throw new Error('Ошибка сервера');

    // Можно добавить уведомление от WordPress, если захочешь
    alert('Настройки успешно сохранены в базе данных!');
  } catch (e) {
    console.error('Save Error:', e);
    alert('Не удалось сохранить данные.');
  } finally {
    isSaving.value = false;
  }
};
</script>

<template>
  <div class="mlm-plugin-root p-4 flex flex-col md:flex-row gap-5 font-sans bg-slate-50 rounded-2xl border border-slate-200 shadow-sm">

    <AdminSettings
      v-if="isAdmin"
      :markers="markers"
      :is-saving="isSaving"
      :is-dev="isDev"
      @remove="removeMarker"
      @save="handleSave"
    />

    <LMap
      :markers="markers"
      :draggable="isAdmin"
      @add-marker="addMarker"
      @update-marker="updateMarker"
    />

  </div>
</template>

<style>
/* Глобальные сбросы для админки WordPress, чтобы стили темы не ломали Tailwind */
.mlm-plugin-root * {
  box-sizing: border-box;
}
</style>
