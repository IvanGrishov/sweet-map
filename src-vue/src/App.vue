<script setup>
import { ref, computed, onMounted } from 'vue';
import LMap from './components/LMap.vue';

// Защищенная инициализация: всегда массив
const markers = ref([]);
const isSaving = ref(false);
const isDev = import.meta.env.DEV;

onMounted(() => {
  if (window.wpData?.coords && Array.isArray(window.wpData.coords)) {
    markers.value = [...window.wpData.coords];
  }
});

const isAdmin = computed(() => isDev || window.wpData?.is_admin);

const addMarker = (newMarker) => {
  markers.value.push(newMarker);
};

const updateMarker = (updated) => {
  const index = markers.value.findIndex(m => m.id === updated.id);
  if (index !== -1) markers.value[index] = updated;
};

const removeMarker = (id) => {
  markers.value = markers.value.filter(m => m.id !== id);
};

const saveAll = async () => {
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
    if (!response.ok) throw new Error('Server Error');
    alert('Сохранено!');
  } catch (e) {
    alert('Ошибка сохранения. Проверьте консоль.');
    console.error(e);
  } finally {
    isSaving.value = false;
  }
};
</script>

<template>
  <div class="mlm-plugin-root p-4 flex flex-col md:flex-row gap-5 font-sans bg-slate-50 rounded-xl border border-slate-200">
    <div v-if="isAdmin" class="w-full md:w-80 bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-col">
      <h2 class="text-lg font-bold mb-4 text-slate-800">Локации ({{ markers.length }})</h2>

      <div class="flex-1 overflow-y-auto max-h-[400px] mb-4 space-y-3 pr-2">
        <div v-for="m in markers" :key="m.id" class="p-3 bg-slate-50 rounded-lg border border-slate-200 transition-all hover:border-blue-300">
          <input v-model="m.title" class="w-full mb-2 p-1 text-sm border-none bg-transparent font-semibold focus:ring-0" placeholder="Название...">
          <div class="flex justify-between items-center text-[10px] text-slate-400">
            <span>{{ Number(m.lat).toFixed(4) }}, {{ Number(m.lng).toFixed(4) }}</span>
            <button @click="removeMarker(m.id)" class="text-red-400 hover:text-red-600 uppercase font-bold">Удалить</button>
          </div>
        </div>
        <div v-if="markers.length === 0" class="text-center py-10 text-slate-400 text-sm italic">
          Кликните на карту, чтобы добавить точку
        </div>
      </div>

      <button @click="saveAll" :disabled="isSaving" class="w-full py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 disabled:bg-slate-300 transition-all shadow-lg shadow-blue-200">
        {{ isSaving ? 'Запись...' : 'Сохранить всё' }}
      </button>
    </div>

    <LMap :markers="markers" :draggable="isAdmin" @add-marker="addMarker" @update-marker="updateMarker" />
  </div>
</template>
