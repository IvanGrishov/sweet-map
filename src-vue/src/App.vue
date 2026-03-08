<script setup lang="ts">
import { inject, provide, computed } from 'vue';
import AdminSettings from './components/settings/AdminSettings.vue';
import LMap from './components/map/LMap.vue';
import MapSearch from './components/map/MapSearch.vue';
import ToastNotification from './components/ui/ToastNotification.vue';
import { createMarkersStore, MARKERS_STORE_KEY } from '@/composables/useMarkers';
import type { WpData } from '@/types';

const isDev = import.meta.env.DEV;

const wpData = inject<WpData>('wpData') ?? {
  rest_url: '',
  nonce: '',
  map_id: 'default',
  can_edit: isDev,
  coords: [],
  zoom: 10,
  mapStyle: 'osm',
  mapHeight: 500,
  showSearch: true,
  locale: 'en',
};

const store = createMarkersStore(wpData);
provide(MARKERS_STORE_KEY, store);

const canEdit = computed(() => {
  if (isDev) return true;
  return String(wpData.can_edit) === '1' || String(wpData.can_edit) === 'true';
});

const { mapStyle, mapHeight, showSearch } = store;
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
