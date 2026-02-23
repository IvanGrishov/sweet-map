<script setup lang="ts">
import { useMarkers } from '@/composables/useMarkers';
import BaseButton from '@/components/ui/BaseButton.vue';
import IconPlus from '@/components/ui/icons/IconPlus.vue';
import PrimaryButton from '@/components/ui/PrimaryButton.vue';
import IconSave from '@/components/ui/icons/IconSave.vue';
import MarkerItem from '@/components/MarkerItem.vue';
import { MarkerData } from '@/types';
import { nextTick, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import DevBadge from '@/components/ui/DevBadge.vue';
import MapZoomControl from '@/components/MapZoomControl.vue';

const { t } = useI18n();

const {
  markers,
  isSaving,
  addMarker,
  removeMarker,
  saveMarkers,
  centerOnMarker,
  activeMarkerId,
  mapStyle
} = useMarkers();

defineEmits<{
  select: [marker: MarkerData];
}>();

const handleSelect = (marker: MarkerData) => {
  centerOnMarker(marker);
};

const isDev = !window.wpData;

const scrollContainer = ref<HTMLElement | null>(null);

watch(activeMarkerId, async (newId) => {
  if (!newId || !scrollContainer.value) return;

  await nextTick();

  // Ищем элемент по селектору атрибута [data-id="..."]
  const activeElement = scrollContainer.value.querySelector(`[data-id="${newId}"]`) as HTMLElement;

  if (activeElement) {
    activeElement.scrollIntoView({
      behavior: 'smooth',
      block: 'center' // Центрируем строго посередине контейнера
    });
  }
});
</script>

<template>
  <div
    class="mlm-sidebar flex flex-col gap-4 w-80 p-6 rounded-2xl bg-white border border-slate-200 shadow-xl"
  >
    <div class="flex items-center justify-between mb-1">
      <div class="flex flex-col">
        <h3 class="m-0 text-slate-800 text-xl font-black tracking-tight leading-tight">
          {{ t('admin.locations') }}
        </h3>
        <span class="text-[0.625rem] text-slate-400 font-bold uppercase tracking-widest">
          {{ t('admin.total') }} {{ markers.length }}
        </span>
      </div>

      <BaseButton @click="addMarker">
        <IconPlus />
        {{ t('admin.add_marker') }}
      </BaseButton>
    </div>

    <div ref="scrollContainer" class="flex-1 overflow-y-auto custom-scrollbar -m-2 p-2 space-y-4">
      <MarkerItem
        v-for="(marker, index) in markers"
        :key="marker.id"
        v-model="markers[index]"
        :data-id="marker.id"
        :class="[
          'transition-all duration-300 rounded-2xl cursor-pointer relative',
          activeMarkerId === marker.id
            ? 'bg-white border border-indigo-900 ring-2 ring-indigo-500 scale-[1.01] z-10'
            : 'bg-slate-50 border border-transparent opacity-70 hover:opacity-100 hover:bg-white hover:border-slate-200'
        ]"
        @remove="removeMarker"
        @select="handleSelect"
      />

      <div v-if="markers.length === 0" class="text-center text-sm text-slate-400 italic py-8">
        {{ t('admin.no_markers') }}
      </div>
    </div>

    <MapZoomControl />

    <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm mt-4">
      <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest block mb-3">
        {{ t('admin.map_style') }}
      </label>

      <div class="relative">
        <select
          v-model="mapStyle"
          class="w-full pl-3 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer hover:bg-slate-100/50"
          style="
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2220%22%20height%3D%2220%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2364748b%22%20stroke-width%3D%222.5%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22m6%209%206%206%206-6%22/%3E%3C/svg%3E');
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
          "
        >
          <option value="osm">{{ t('admin.style_osm') }}</option>
          <option value="satellite">{{ t('admin.style_satellite') }}</option>
        </select>
      </div>
    </div>

    <PrimaryButton :loading="isSaving" @click="saveMarkers">
      <template #icon>
        <IconSave />
      </template>
      {{ t('admin.save') }}
    </PrimaryButton>

    <DevBadge v-if="isDev" />
  </div>
</template>

<style scoped>
/* В обычном CSS блоке пишем в px */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
</style>
