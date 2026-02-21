<script setup lang="ts">
import { computed } from 'vue';

// Описываем интерфейс пропсов
interface Props {
  lat?: string;
  lng?: string;
  isSaving?: boolean;
  isDev?: boolean;
}

// Используем withDefaults для задания начальных значений
const props = withDefaults(defineProps<Props>(), {
  lat: '',
  lng: '',
  isSaving: false,
  isDev: false
});

const emit = defineEmits<{
  (e: 'update:lat', value: string): void;
  (e: 'update:lng', value: string): void;
  (e: 'save'): void;
  (e: 'input-change'): void;
}>();

// Прокси для v-model
const latValue = computed({
  get: () => props.lat ?? '',
  set: (v) => emit('update:lat', v)
});

const lngValue = computed({
  get: () => props.lng ?? '',
  set: (v) => emit('update:lng', v)
});
</script>

<template>
  <div class="mlm-sidebar flex flex-col gap-4 w-72 p-6 rounded-2xl bg-white border border-slate-200 shadow-lg h-fit">

    <h3 class="m-0 text-slate-800 text-lg font-bold">Настройки карты</h3>

    <label class="flex flex-col gap-1.5 mb-3.5">
      <span class="text-[11px] font-bold text-slate-500 uppercase">Широта (Lat)</span>
      <input
        v-model="latValue"
        type="text"
        class="w-full p-3 border border-slate-300 rounded-lg bg-slate-100 text-slate-800 transition-colors focus:border-indigo-500 focus:bg-white focus:outline-none"
        @input="$emit('input-change')"
      />
    </label>

    <label class="flex flex-col gap-1.5 mb-3.5">
      <span class="text-[11px] font-bold text-slate-500 uppercase">Долгота (Lng)</span>
      <input
        v-model="lngValue"
        type="text"
        class="w-full p-3 border border-slate-300 rounded-lg bg-slate-100 text-slate-800 transition-colors focus:border-indigo-500 focus:bg-white focus:outline-none"
        @input="$emit('input-change')"
      />
    </label>

    <button
      :disabled="isSaving"
      class="w-full py-3.5 px-4 bg-indigo-600 text-white border-none rounded-xl cursor-pointer font-semibold transition-colors hover:bg-indigo-700 disabled:bg-slate-400 disabled:cursor-not-allowed"
      @click="$emit('save')"
    >
      {{ isSaving ? 'Сохранение...' : 'Сохранить в WordPress' }}
    </button>

    <div v-if="isDev" class="text-[10px] text-slate-400 text-center border border-dashed border-slate-300 p-1.5">
      DEVELOPMENT MODE
    </div>
  </div>
</template>

<style scoped>
/* Теперь здесь пусто, всё управляется через Tailwind классы */
</style>
