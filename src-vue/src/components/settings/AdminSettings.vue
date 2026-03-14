<script setup lang="ts">
import { useMarkers } from '@/composables/useMarkers';
import MarkerItem from '@/components/settings/MarkerItem.vue';
import MarkerEditor from '@/components/settings/MarkerEditor.vue';
import { nextTick, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import MapZoomControl from '@/components/settings/MapZoomControl.vue';
import MapStyleSelect from '@/components/settings/MapStyleSelect.vue';
import MapHeightControl from '@/components/settings/MapHeightControl.vue';

const { t } = useI18n();

const { markers, activeMarkerId, openEditMarker, mapStyle } = useMarkers();

const scrollContainer = ref<HTMLElement | null>(null);

watch(activeMarkerId, async (newId) => {
  if (!newId || !scrollContainer.value) return;
  await nextTick();
  const el = scrollContainer.value.querySelector(`[data-id="${newId}"]`) as HTMLElement;
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
});
</script>

<template>
  <div class="swmap-sidebar w-full md:w-96 flex flex-col gap-0 relative font-sans">
    <!-- Editor -->
    <div>
      <MarkerEditor />
    </div>

    <!-- Locations section -->
    <div class="mt-5 flex flex-col gap-3">
      <!-- Section header -->
      <div class="flex items-center justify-between">
        <h3 class="text-slate-800 text-lg font-black tracking-tight leading-tight">
          {{ t('admin.locations') }}
        </h3>
        <div class="text-sm font-semibold text-slate-600">
          {{ t('admin.total') }} {{ markers.length }}
        </div>
      </div>

      <!-- Marker list -->
      <div
        ref="scrollContainer"
        class="overflow-y-auto custom-scrollbar flex flex-col gap-1 max-h-80"
      >
        <MarkerItem
          v-for="marker in [...markers].reverse()"
          :key="marker.id"
          :marker="marker"
          :is-active="activeMarkerId === marker.id"
          :data-id="marker.id"
          @select="openEditMarker"
        />

        <div v-if="markers.length === 0" class="text-center text-sm text-slate-600 italic py-6">
          {{ t('admin.no_markers') }}
        </div>
      </div>

      <!-- Map settings -->
      <MapZoomControl />
      <MapHeightControl />
      <MapStyleSelect v-model="mapStyle" />

    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #94a3b8;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #64748b;
}
</style>
