<script setup lang="ts">
import { computed } from 'vue';
import AdminSettings from './components/settings/AdminSettings.vue';
import LMap from './components/map/LMap.vue';
import MapSearch from './components/map/MapSearch.vue';
import ToastNotification from './components/ui/ToastNotification.vue';
import { useMarkers } from '@/composables/useMarkers';

const isDev = import.meta.env.DEV;

const canEdit = computed(() => {
  if (isDev) return true;

  const val = window.wpData?.can_edit;

  return String(val) === '1' || String(val) === 'true';
});

const { mapStyle, mapHeight, showSearch } = useMarkers();

</script>

<template>
  <div
    class="mlm-plugin-root flex flex-col md:flex-row gap-5 relative items-start"
  >
    <AdminSettings v-if="canEdit" />
    <div class="flex flex-col flex-1 gap-3 min-w-0 md:sticky md:top-16">
      <div class="mlm-search-header flex flex-col gap-2">
        <MapSearch v-if="canEdit || showSearch" :can-edit="canEdit" />
      </div>
      <LMap :draggable="canEdit" :map-style="mapStyle" :map-height="mapHeight" />
    </div>
    <ToastNotification />
  </div>
</template>
