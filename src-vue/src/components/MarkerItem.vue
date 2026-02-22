<script setup lang="ts">
import BaseIconButton from '@/components/ui/BaseIconButton.vue';
import IconTrash from '@/components/ui/icons/IconTrash.vue';
import { MarkerData } from '@/types';

const marker = defineModel<MarkerData>({ required: true });

defineEmits<{
  remove: [id: string];
  select: [marker: MarkerData];
}>();
</script>

<template>
  <div
    class="group p-4 rounded-xl border border-slate-100 bg-slate-50 transition-all hover:border-indigo-200 hover:shadow-sm cursor-pointer"
    @click="$emit('select', marker)"
  >
    <div class="flex flex-col gap-2">
      <input
        v-model="marker.title"
        type="text"
        placeholder="Название места"
        class="bg-transparent font-bold text-slate-700 border-none p-0 focus:ring-0 placeholder:text-slate-400 text-sm outline-none"
      />

      <div class="flex items-end justify-between gap-2">
        <div class="flex flex-col gap-1 flex-1">
          <div class="flex items-center gap-1.5">
            <span class="text-[0.625rem] font-bold text-slate-400 w-6 uppercase">Lat</span>
            <input
              v-model="marker.lat"
              type="text"
              class="bg-white px-1.5 py-0.5 rounded border border-slate-100 text-[0.75rem] font-mono text-slate-600 w-full focus:border-indigo-300 focus:outline-none transition-colors"
            />
          </div>
          <div class="flex items-center gap-1.5">
            <span class="text-[0.625rem] font-bold text-slate-400 w-6 uppercase">Lng</span>
            <input
              v-model="marker.lng"
              type="text"
              class="bg-white px-1.5 py-0.5 rounded border border-slate-100 text-[0.75rem] font-mono text-slate-600 w-full focus:border-indigo-300 focus:outline-none transition-colors"
            />
          </div>
        </div>

        <BaseIconButton variant="danger" title="Удалить точку" @click="$emit('remove', marker.id)">
          <IconTrash />
        </BaseIconButton>
      </div>
    </div>
  </div>
</template>
