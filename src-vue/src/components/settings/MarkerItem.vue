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
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl cursor-pointer border transition-all duration-150"
    :class="[
      isActive
        ? 'bg-indigo-50 border-indigo-200 shadow-sm'
        : 'bg-white border-slate-200 hover:border-indigo-200 hover:bg-slate-50'
    ]"
    @click="emit('select', props.marker.id)"
  >
    <!-- Pin or custom icon -->
    <div class="shrink-0 flex items-center justify-center w-7 h-7">
      <img
        v-if="props.marker.icon"
        :src="props.marker.icon"
        class="w-6 h-6 object-contain"
      />
      <svg v-else width="18" height="26" viewBox="0 0 24 34" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M12 32C16 26 22 20.4183 22 14C22 7.37258 17.5228 2 12 2C6.47715 2 2 7.37258 2 14C2 20.4183 8 26 12 32Z"
          :fill="props.marker.color || '#4f46e5'"
          stroke="white"
          stroke-width="2"
        />
        <circle cx="12" cy="14" r="4" fill="white" />
      </svg>
    </div>

    <!-- Info -->
    <div class="flex-1 min-w-0">
      <p
        class="text-sm font-semibold truncate leading-tight"
        :class="isActive ? 'text-indigo-900' : 'text-slate-800'"
      >
        {{ props.marker.title || '—' }}
      </p>
      <p
        class="text-xs font-mono mt-0.5 leading-tight"
        :class="isActive ? 'text-indigo-400' : 'text-slate-400'"
      >
        {{ Number(props.marker.lat).toFixed(4) }}, {{ Number(props.marker.lng).toFixed(4) }}
      </p>
    </div>

    <!-- Chevron -->
    <svg
      class="w-4 h-4 shrink-0 transition-colors"
      :class="isActive ? 'text-indigo-400' : 'text-slate-300'"
      fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
    >
      <path d="M9 18l6-6-6-6"/>
    </svg>
  </div>
</template>
