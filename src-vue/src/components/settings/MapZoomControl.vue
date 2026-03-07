<script setup lang="ts">
import { useMarkers } from '@/composables/useMarkers';

const { zoom } = useMarkers();

const ZOOM_CONFIG = {
  MIN: 1,
  MAX: 18,
  STEP: 1
};
</script>

<template>
  <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 shadow-sm">
    <div class="flex justify-between items-center mb-4">
      <label
        class="text-[0.8125rem] font-bold text-slate-600 uppercase tracking-widest leading-none"
      >
        {{ $t('admin.map_zoom') }}
      </label>
      <span class="text-sm font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg">
        {{ zoom }}
      </span>
    </div>

    <input
      v-model="zoom"
      type="range"
      :min="ZOOM_CONFIG.MIN"
      :max="ZOOM_CONFIG.MAX"
      :step="ZOOM_CONFIG.STEP"
      class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600"
    />

    <div
      class="flex justify-between text-[0.625rem] text-slate-400 mt-2 font-bold uppercase tracking-tighter"
    >
      <span>{{ $t('admin.zoom_min') }}: {{ ZOOM_CONFIG.MIN }}</span>
      <span>{{ $t('admin.zoom_max') }}: {{ ZOOM_CONFIG.MAX }}</span>
    </div>
  </div>
</template>
