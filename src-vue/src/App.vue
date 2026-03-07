<script setup lang="ts">
import { computed } from 'vue';
import AdminSettings from './components/settings/AdminSettings.vue';
import LMap from './components/map/LMap.vue';
import MapTitle from './components/map/MapTitle.vue';
import MapSearch from './components/map/MapSearch.vue';
import ToastNotification from './components/ui/ToastNotification.vue';
import { useMarkers } from '@/composables/useMarkers';

const isDev = import.meta.env.DEV;

const canEdit = computed(() => {
  if (isDev) return true;

  const val = window.wpData?.can_edit;

  return String(val) === '1' || String(val) === 'true';
});

const { mapStyle, mapHeight } = useMarkers();
</script>

<template>
  <div
    class="mlm-plugin-root flex flex-col md:flex-row gap-5 p-4 pt-8 font-sans min-h-150 relative"
  >
    <AdminSettings v-if="canEdit" />
    <div class="flex flex-col flex-1 gap-3 min-w-0">
      <div class="mlm-search-header flex flex-col gap-2">
        <MapTitle :can-edit="canEdit" />
        <MapSearch :can-edit="canEdit" />
      </div>
      <LMap :draggable="canEdit" :map-style="mapStyle" :map-height="mapHeight" />
    </div>
    <ToastNotification />
  </div>
</template>
