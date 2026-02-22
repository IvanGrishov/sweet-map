<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import LMap from './components/LMap.vue';
import AdminSettings from './components/AdminSettings.vue';

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
  // 1. Пробуем загрузить данные из WP
  const wpCoords = window.wpData?.coords;

  if (Array.isArray(wpCoords) && wpCoords.length > 0) {
    markers.value = [...wpCoords];
  } else {
    // 2. Если данных нет (первый запуск или localhost), добавляем тестовую точку
    markers.value = [
      {
        id: 'test-default',
        lat: '55.7512',
        lng: '37.6184',
        title: 'Тестовая локация'
      }
    ];
  }
});

const isAdmin = computed(() => isDev || window.wpData?.is_admin);

// Добавление маркера (используется и для клика по карте, и для кнопки)
const addMarker = (newMarker: MarkerData) => {
  markers.value.push(newMarker);
};

// Ручное добавление через кнопку в Сайдбаре
const handleManualAdd = () => {
  addMarker({
    id: Date.now().toString(),
    lat: '55.75',
    lng: '37.61',
    title: 'Новая точка'
  });
};

const updateMarker = (updated: MarkerData) => {
  const index = markers.value.findIndex((m) => m.id === updated.id);
  if (index !== -1) {
    markers.value[index] = updated;
  }
};

const removeMarker = (id: string) => {
  markers.value = markers.value.filter((m) => m.id !== id);
};

const handleSave = async () => {
  if (isDev) {
    console.log('Save triggered in Dev Mode:', markers.value);
    alert('В режиме разработки сохранение имитируется в консоль');
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
      body: JSON.stringify({ markers: markers.value })
    });

    if (!response.ok) throw new Error('Ошибка сервера');
    alert('Сохранено в WordPress!');
  } catch (e) {
    console.error('Save Error:', e);
    alert('Ошибка сохранения');
  } finally {
    isSaving.value = false;
  }
};
</script>

<template>
  <div class="mlm-plugin-root flex flex-col md:flex-row gap-5 font-sans">
    <AdminSettings
      v-if="isAdmin"
      :markers="markers"
      :is-saving="isSaving"
      :is-dev="isDev"
      @add="handleManualAdd"
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
