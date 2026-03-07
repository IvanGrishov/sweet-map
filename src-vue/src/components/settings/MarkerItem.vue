<script setup lang="ts">
import { MarkerData } from '@/types';

const props = defineProps<{
  marker: MarkerData;
  isActive: boolean;
}>();

const emit = defineEmits<{
  select: [id: string];
}>();
</script>

<template>
  <div
    class="flex items-center gap-2.5 px-3 py-2 rounded-lg cursor-pointer transition-all duration-150"
    :class="[
      isActive
        ? 'bg-indigo-50 border border-indigo-200 text-indigo-900'
        : 'bg-transparent border border-transparent text-slate-700 hover:bg-slate-100 hover:border-slate-200'
    ]"
    @click="emit('select', props.marker.id)"
  >
    <!-- Icon or color dot -->
    <img
      v-if="props.marker.icon"
      :src="props.marker.icon"
      class="w-4 h-4 object-contain shrink-0 rounded"
    />
    <div
      v-else
      class="w-2.5 h-2.5 rounded-full shrink-0 ring-1 ring-white ring-offset-1"
      :style="{ backgroundColor: props.marker.color || '#4f46e5' }"
    />

    <!-- Title -->
    <span
      class="flex-1 text-sm font-medium truncate"
      :class="isActive ? 'text-indigo-800' : 'text-slate-700'"
    >
      {{ props.marker.title || '—' }}
    </span>

    <!-- Coords -->
    <span class="text-[11px] font-mono shrink-0" :class="isActive ? 'text-indigo-400' : 'text-slate-400'">
      {{ Number(props.marker.lat).toFixed(3) }}, {{ Number(props.marker.lng).toFixed(3) }}
    </span>
  </div>
</template>
