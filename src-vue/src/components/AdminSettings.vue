<script setup lang="ts">
interface Marker {
  id: string;
  lat: string | number;
  lng: string | number;
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
  <div class="mlm-sidebar flex flex-col gap-[1rem] w-[20rem] p-[1.5rem] rounded-[1.25rem] bg-white border border-slate-200 shadow-xl h-[37.5rem]">

    <div class="flex items-center justify-between mb-[0.5rem]">
      <h3 class="m-0 text-slate-800 text-[1.25rem] font-black tracking-tight">Локации</h3>
      <span class="px-[0.5rem] py-[0.125rem] bg-indigo-100 text-indigo-600 rounded-full text-[0.75rem] font-bold">
        {{ markers.length }}
      </span>
    </div>

    <div class="flex-1 overflow-y-auto pr-[0.5rem] custom-scrollbar space-y-[0.75rem]">
      <div
        v-for="marker in markers"
        :key="marker.id"
        class="group p-[1rem] rounded-[0.75rem] border border-slate-100 bg-slate-50 transition-all hover:border-indigo-200 hover:shadow-sm"
      >
        <div class="flex flex-col gap-[0.5rem]">
          <input
            v-model="marker.title"
            type="text"
            placeholder="Название места"
            class="bg-transparent font-bold text-slate-700 border-none p-0 focus:ring-0 placeholder:text-slate-400 text-[0.875rem]"
          />

          <div class="flex items-end justify-between gap-[0.5rem]">
            <div class="flex flex-col gap-[0.25rem] flex-1">
              <div class="flex items-center gap-[0.375rem]">
                <span class="text-[0.625rem] font-bold text-slate-400 w-[1.5rem]">LAT</span>
                <input
                  v-model="marker.lat"
                  type="text"
                  class="bg-white px-[0.375rem] py-[0.125rem] rounded border border-slate-100 text-[0.75rem] font-mono text-slate-600 w-full focus:border-indigo-300 focus:outline-none transition-colors"
                />
              </div>
              <div class="flex items-center gap-[0.375rem]">
                <span class="text-[0.625rem] font-bold text-slate-400 w-[1.5rem]">LNG</span>
                <input
                  v-model="marker.lng"
                  type="text"
                  class="bg-white px-[0.375rem] py-[0.125rem] rounded border border-slate-100 text-[0.75rem] font-mono text-slate-600 w-full focus:border-indigo-300 focus:outline-none transition-colors"
                />
              </div>
            </div>

            <button
              type="button"
              @click="emit('remove', marker.id)"
              class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 transition-all p-[0.25rem] mb-[0.125rem]"
              title="Удалить точку"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
            </button>
          </div>
        </div>
      </div>

      <div v-if="markers.length === 0" class="text-center text-[0.875rem] text-slate-400 italic py-[2rem]">
        Кликните по карте, чтобы <br> добавить первую метку
      </div>
    </div>

    <button
      :disabled="isSaving"
      class="relative w-full py-[1rem] px-[1rem] bg-indigo-600 text-white border-none rounded-[0.75rem] font-bold transition-all hover:bg-indigo-700 active:scale-[0.98] disabled:bg-slate-300 shadow-lg shadow-indigo-100"
      @click="emit('save')"
    >
      <span v-if="!isSaving" class="flex items-center justify-center gap-[0.5rem]">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v13a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Сохранить всё
      </span>
      <span v-else class="flex items-center justify-center gap-[0.5rem]">
        <div class="w-[1rem] h-[1rem] border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
        Запись...
      </span>
    </button>

    <div v-if="isDev" class="text-[0.56rem] tracking-[0.1em] font-black text-slate-300 text-center uppercase">
      Development Mode
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 0.25rem;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 0.625rem;
}
</style>
