<script setup lang="ts">
import { computed } from 'vue';
import AdminSettings from './components/settings/AdminSettings.vue';
import LMap from './components/map/LMap.vue';
import { useMarkers } from '@/composables/useMarkers';

const isDev = import.meta.env.DEV;

const canEdit = computed(() => {
  if (isDev) return true;

  const val = window.wpData?.can_edit;

  return String(val) === '1' || String(val) === 'true';
});

const { mapStyle } = useMarkers();
</script>

<template>
  <div
    class="mlm-plugin-root flex flex-col md:flex-row gap-5 p-4 pt-8 font-sans min-h-150 relative"
  >
    <AdminSettings v-if="canEdit" />
    <LMap :draggable="canEdit" :map-style="mapStyle" />
  </div>
</template>
