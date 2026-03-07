<script setup lang="ts">
import { useMarkers } from '@/composables/useMarkers';
import BaseButton from '@/components/ui/BaseButton.vue';
import IconPlus from '@/components/ui/icons/IconPlus.vue';
import PrimaryButton from '@/components/ui/PrimaryButton.vue';
import IconSave from '@/components/ui/icons/IconSave.vue';
import MarkerItem from '@/components/settings/MarkerItem.vue';
import { MarkerData } from '@/types';
import { nextTick, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import DevBadge from '@/components/ui/DevBadge.vue';
import MapZoomControl from '@/components/settings/MapZoomControl.vue';
import MapStyleSelect from '@/components/settings/MapStyleSelect.vue';

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
    class="mlm-sidebar flex flex-col gap-4 w-80 p-4 pt-0 rounded-2xl bg-white border border-slate-200 shadow-xl relative"
  >
    <div class="sticky top-11 z-20 -mx-4 py-4 bg-white border-b border-slate-300">
      <PrimaryButton :loading="isSaving" @click="saveMarkers">
        <template #icon>
          <IconSave />
        </template>
        {{ t('admin.save') }}
      </PrimaryButton>
    </div>
    <div class="pt-2">
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

      <div
        ref="scrollContainer"
        class="overflow-y-auto custom-scrollbar p-2 relative -left-2 max-h-110 flex flex-col gap-4 w-[calc(100%+16px)]"
      >
        <MarkerItem
          v-for="(marker, index) in markers"
          :key="marker.id"
          v-model="markers[index]"
          :data-id="marker.id"
          :class="[
            'transition-all duration-300 rounded-2xl cursor-pointer relative',
            activeMarkerId === marker.id
              ? 'bg-white border border-indigo-900 ring-2 ring-indigo-500 scale-[1.01] z-10'
              : 'bg-slate-100 border border-transparent opacity-70 hover:opacity-100 hover:bg-white hover:border-slate-200'
          ]"
          @remove="removeMarker"
          @select="handleSelect"
        />

        <div v-if="markers.length === 0" class="text-center text-sm text-slate-400 italic py-8">
          {{ t('admin.no_markers') }}
        </div>
      </div>

      <MapZoomControl />

      <MapStyleSelect v-model="mapStyle" />

      <DevBadge v-if="isDev" />
    </div>
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
