<script setup lang="ts">
import { useMarkers } from '@/composables/useMarkers';
import BaseButton from '@/components/ui/BaseButton.vue';
import IconPlus from '@/components/ui/icons/IconPlus.vue';
import PrimaryButton from '@/components/ui/PrimaryButton.vue';
import IconSave from '@/components/ui/icons/IconSave.vue';
import MarkerItem from '@/components/MarkerItem.vue';

const { markers, isSaving, addMarker, removeMarker, saveMarkers } = useMarkers();

const isDev = !window.wpData;
</script>

<template>
  <div
    class="mlm-sidebar flex flex-col gap-4 w-80 p-6 rounded-2xl bg-white border border-slate-200 shadow-xl h-150"
  >
    <div class="flex items-center justify-between mb-1">
      <div class="flex flex-col">
        <h3 class="m-0 text-slate-800 text-xl font-black tracking-tight leading-tight">Локации</h3>
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"
          >Всего: {{ markers.length }}</span
        >
      </div>

      <BaseButton @click="() => addMarker()">
        <IconPlus />
        Добавить
      </BaseButton>
    </div>

    <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar space-y-3">
      <MarkerItem
        v-for="(marker, index) in markers"
        :key="marker.id"
        v-model="markers[index]"
        @remove="removeMarker"
      />

      <div v-if="markers.length === 0" class="text-center text-sm text-slate-400 italic py-8">
        Нажмите кнопку «Добавить» или <br />
        кликните по карте
      </div>
    </div>

    <PrimaryButton :loading="isSaving" @click="saveMarkers">
      <template #icon>
        <IconSave />
      </template>
      Сохранить всё
    </PrimaryButton>

    <div
      v-if="isDev"
      class="text-[9px] tracking-widest font-black text-slate-300 text-center uppercase"
    >
      Development Mode
    </div>
  </div>
</template>

<style scoped>
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
