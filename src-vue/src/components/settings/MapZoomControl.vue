<script setup lang="ts">
import { useMarkers } from '@/composables/useMarkers';

const { zoom } = useMarkers();

const ZOOM_CONFIG = { MIN: 1, MAX: 18, STEP: 1 };
</script>

<template>
  <div class="pt-4 border-t border-slate-100">
    <div class="flex justify-between items-center mb-3">
      <span class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">
        {{ $t('admin.map_zoom') }}
      </span>
      <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md tabular-nums">
        {{ zoom }}
      </span>
    </div>

    <input
      v-model="zoom"
      type="range"
      :min="ZOOM_CONFIG.MIN"
      :max="ZOOM_CONFIG.MAX"
      :step="ZOOM_CONFIG.STEP"
      class="w-full h-1.5 bg-slate-200 rounded-full appearance-none cursor-pointer accent-indigo-600"
    />

    <div class="flex justify-between text-[10px] text-slate-400 mt-1.5 font-medium">
      <span>{{ ZOOM_CONFIG.MIN }}</span>
      <span>{{ ZOOM_CONFIG.MAX }}</span>
    </div>
  </div>
</template>
