<script setup lang="ts">
import type { MarkerData } from '../types';

interface Props {
  markers?: MarkerData[];
  isSaving?: boolean;
  isDev?: boolean;
}

withDefaults(defineProps<Props>(), {
  markers: () => [],
  isSaving: false,
  isDev: false
});

const emit = defineEmits<{
  (e: 'remove', id: string): void;
  (e: 'save'): void;
  (e: 'add'): void;
}>();
</script>

<template>
  <div
    class="mlm-sidebar flex flex-col gap-4 w-80 p-6 rounded-2xl bg-white border border-slate-200 shadow-xl h-[600px]"
  >
    <div class="flex items-center justify-between mb-1">
      <div class="flex flex-col">
        <h3 class="m-0 text-slate-800 text-xl font-black tracking-tight leading-tight">Локации</h3>
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"
          >Всего: {{ markers.length }}</span
        >
      </div>

      <button
        type="button"
        class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-bold hover:bg-slate-700 transition-colors shadow-sm"
        @click="emit('add')"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="14"
          height="14"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="3"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Добавить
      </button>
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
            class="bg-transparent font-bold text-slate-700 border-none p-0 focus:ring-0 placeholder:text-slate-400 text-sm"
          />

          <div class="flex items-end justify-between gap-2">
            <div class="flex flex-col gap-1 flex-1">
              <div class="flex items-center gap-1.5">
                <span class="text-[10px] font-bold text-slate-400 w-6">LAT</span>
                <input
                  v-model="marker.lat"
                  type="text"
                  class="bg-white px-1.5 py-0.5 rounded border border-slate-100 text-[12px] font-mono text-slate-600 w-full focus:border-indigo-300 focus:outline-none transition-colors"
                />
              </div>
              <div class="flex items-center gap-1.5">
                <span class="text-[10px] font-bold text-slate-400 w-6">LNG</span>
                <input
                  v-model="marker.lng"
                  type="text"
                  class="bg-white px-1.5 py-0.5 rounded border border-slate-100 text-[12px] font-mono text-slate-600 w-full focus:border-indigo-300 focus:outline-none transition-colors"
                />
              </div>
            </div>

            <button
              type="button"
              class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 transition-all p-1 mb-0.5"
              title="Удалить точку"
              @click="emit('remove', marker.id)"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 6h18" />
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                <line x1="10" y1="11" x2="10" y2="17" />
                <line x1="14" y1="11" x2="14" y2="17" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <div v-if="markers.length === 0" class="text-center text-sm text-slate-400 italic py-8">
        Нажмите кнопку «Добавить» или <br />
        кликните по карте
      </div>
    </div>

    <button
      :disabled="isSaving"
      class="relative w-full py-4 px-4 bg-indigo-600 text-white border-none rounded-xl font-bold transition-all hover:bg-indigo-700 active:scale-95 disabled:bg-slate-300 shadow-lg shadow-indigo-100"
      @click="emit('save')"
    >
      <span v-if="!isSaving" class="flex items-center justify-center gap-2">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v13a2 2 0 0 1-2 2z" />
          <polyline points="17 21 17 13 7 13 7 21" />
          <polyline points="7 3 7 8 15 8" />
        </svg>
        Сохранить всё
      </span>
      <span v-else class="flex items-center justify-center gap-2">
        <div
          class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"
        ></div>
        Запись...
      </span>
    </button>

    <div
      v-if="isDev"
      class="text-[9px] tracking-widest font-black text-slate-300 text-center uppercase"
    >
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
