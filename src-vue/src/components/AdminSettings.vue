<script setup lang="ts">
import { USkeleton } from '#components'; // Если используешь Nuxt UI или аналоги, иначе убери

interface Marker {
  id: string;
  lat: string;
  lng: string;
  title: string;
}

interface Props {
  markers: Marker[];
  isSaving?: boolean;
  isDev?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  markers: () => [],
  isSaving: false,
  isDev: false
});

const emit = defineEmits<{
  (e: 'remove', id: string): void;
  (e: 'save'): void;
}>();
</script>

<template>
  <div class="mlm-sidebar flex flex-col gap-4 w-80 p-6 rounded-2xl bg-white border border-slate-200 shadow-xl h-[600px]">

    <div class="flex items-center justify-between mb-2">
      <h3 class="m-0 text-slate-800 text-xl font-black tracking-tight">Локации</h3>
      <span class="px-2 py-0.5 bg-indigo-100 text-indigo-600 rounded-full text-xs font-bold">
        {{ markers.length }}
      </span>
    </div>

    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar space-y-3">
      <div
        v-for="marker in markers"
        :key="marker.id"
        class="group p-4 rounded-xl border border-slate-100 bg-slate-50 transition-all hover:border-indigo-200 hover:shadow-sm"
      >
        <div class="flex flex-col gap-2">
          <input
            v-model="marker.title"
            type="text"
            placeholder="Название места"
            class="bg-transparent font-bold text-slate-700 border-none p-0 focus:ring-0 placeholder:text-slate-400"
          />

          <div class="flex items-center justify-between">
            <div class="flex gap-3 text-[10px] font-mono text-slate-400">
              <span class="bg-white px-1.5 py-0.5 rounded border border-slate-100">LAT: {{ Number(marker.lat).toFixed(4) }}</span>
              <span class="bg-white px-1.5 py-0.5 rounded border border-slate-100">LNG: {{ Number(marker.lng).toFixed(4) }}</span>
            </div>

            <button
              @click="emit('remove', marker.id)"
              class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 transition-all p-1"
              title="Удалить точку"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
            </button>
          </div>
        </div>
      </div>

      <div v-if="markers.length === 0" class="flex flex-col gap-3">
        <USkeleton class="h-20 w-full rounded-xl" :style="{ height: '5rem' }" />
        <p class="text-center text-sm text-slate-400 italic py-4">
          Кликните по карте, чтобы <br> добавить первую метку
        </p>
      </div>
    </div>

    <button
      :disabled="isSaving"
      class="relative w-full py-4 px-4 bg-indigo-600 text-white border-none rounded-xl cursor-pointer font-bold transition-all hover:bg-indigo-700 active:scale-[0.98] disabled:bg-slate-300 shadow-lg shadow-indigo-100"
      @click="emit('save')"
    >
      <span v-if="!isSaving" class="flex items-center justify-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v13a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Сохранить всё
      </span>
      <span v-else class="flex items-center justify-center gap-2">
        <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
        Запись в базу...
      </span>
    </button>

    <div v-if="isDev" class="text-[9px] tracking-widest font-black text-slate-300 text-center uppercase">
      Development Mode
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
</style>
