<script setup lang="ts">
import { watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useMarkers } from '@/composables/useMarkers';
import { useToast } from '@/composables/useToast';

const { t } = useI18n();
const { zoom, saveMarkers } = useMarkers();
const toast = useToast();

const ZOOM_CONFIG = { MIN: 1, MAX: 18, STEP: 1 };

let saveTimer: ReturnType<typeof setTimeout> | null = null;

watch(zoom, () => {
  if (saveTimer) clearTimeout(saveTimer);
  saveTimer = setTimeout(async () => {
    await saveMarkers();
    toast.show(t('admin.zoom_saved'));
  }, 700);
});
</script>

<template>
  <div class="pt-5 border-t border-slate-200">
    <div class="flex justify-between items-center mb-3">
      <span class="text-sm font-semibold uppercase tracking-widest text-slate-700">
        {{ t('admin.map_zoom') }}
      </span>
      <span class="text-base font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-md tabular-nums">
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

    <div class="flex justify-between text-sm text-slate-500 mt-2 font-medium">
      <span>{{ ZOOM_CONFIG.MIN }}</span>
      <span>{{ ZOOM_CONFIG.MAX }}</span>
    </div>
  </div>
</template>
