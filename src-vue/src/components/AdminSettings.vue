<script setup lang="ts">
import { useMarkers } from '@/composables/useMarkers';
import BaseButton from '@/components/ui/BaseButton.vue';
import BaseIconButton from '@/components/ui/BaseIconButton.vue';
import IconPlus from '@/components/ui/icons/IconPlus.vue';
import IconTrash from '@/components/ui/icons/IconTrash.vue';
import PrimaryButton from '@/components/ui/PrimaryButton.vue';
import IconSave from '@/components/ui/icons/IconSave.vue';

// Подключаем стор. Теперь не нужно прокидывать пропсы сверху!
const { markers, isSaving, addMarker, removeMarker, saveMarkers } = useMarkers();

// Добавляем проверку на dev-режим для плашки снизу
const isDev = !window.wpData;
</script>

<template>
  <div
    class="mlm-sidebar flex flex-col gap-4 w-80 p-6 rounded-2xl bg-white border border-slate-200 shadow-xl h-[600px]"
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
      <div
        v-for="marker in markers"
        :key="marker.id"
        class="group p-4 rounded-xl border border-slate-100 bg-slate-50 transition-all hover:border-indigo-200 hover:shadow-sm"
      >
        <div class="flex flex-col gap-2">
          <input
            v-model="marker.title"
            type="text"
            placeholder="Название места"
            class="bg-transparent font-bold text-slate-700 border-none p-0 focus:ring-0 placeholder:text-slate-400 text-sm"
          />

          <div class="flex items-end justify-between gap-2">
            <div class="flex flex-col gap-1 flex-1">
              <div class="flex items-center gap-1.5">
                <span class="text-[10px] font-bold text-slate-400 w-6">LAT</span>
                <input
                  v-model="marker.lat"
                  type="text"
                  class="bg-white px-1.5 py-0.5 rounded border border-slate-100 text-[12px] font-mono text-slate-600 w-full focus:border-indigo-300 focus:outline-none transition-colors"
                />
              </div>
              <div class="flex items-center gap-1.5">
                <span class="text-[10px] font-bold text-slate-400 w-6">LNG</span>
                <input
                  v-model="marker.lng"
                  type="text"
                  class="bg-white px-1.5 py-0.5 rounded border border-slate-100 text-[12px] font-mono text-slate-600 w-full focus:border-indigo-300 focus:outline-none transition-colors"
                />
              </div>
            </div>

            <BaseIconButton variant="danger" @click="() => removeMarker(marker.id)">
              <IconTrash />
            </BaseIconButton>
          </div>
        </div>
      </div>

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
