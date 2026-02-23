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

const { t } = useI18n();

const {
  markers,
  isSaving,
  addMarker,
  removeMarker,
  saveMarkers,
  centerOnMarker,
  activeMarkerId,
  zoom
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
    class="mlm-sidebar flex flex-col gap-4 w-80 p-6 rounded-2xl bg-white border border-slate-200 shadow-xl h-150"
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

    <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="flex justify-between items-center mb-4">
        <label class="text-[0.9rem] font-medium text-gray-700">
          {{ t('admin.map_zoom') }}: {{ zoom }}
        </label>
      </div>

      <input
        v-model="zoom"
        type="range"
        min="1"
        max="18"
        step="1"
        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600"
      />

      <div class="flex justify-between text-[0.75rem] text-gray-500 mt-2">
        <span>1</span>
        <span>18</span>
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
