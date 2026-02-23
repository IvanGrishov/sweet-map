<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import IconArrowDown from '@/components/ui/icons/IconArrowDown.vue';

const model = defineModel({ type: String, required: true });
const isOpen = ref(false);
const wrapper = ref(null);

// Твой конфиг
const mapOptions = [
  { value: 'osm', label: 'admin.style_osm' },
  { value: 'satellite', label: 'admin.style_satellite' }
];

const selectOption = (val) => {
  model.value = val;
  isOpen.value = false;
};

// Закрытие при клике мимо
const handleClickOutside = (event) => {
  if (wrapper.value && !wrapper.value.contains(event.target)) {
    isOpen.value = false;
  }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
  <div ref="wrapper" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm mt-4">
    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest block mb-3">
      {{ $t('admin.map_style') }}
    </label>

    <div class="relative">
      <div
        class="w-full pl-3 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 cursor-pointer hover:bg-slate-100/50 transition-all flex items-center justify-between"
        :class="{ 'ring-4 ring-indigo-500/10 border-indigo-500 bg-white': isOpen }"
        @click="isOpen = !isOpen"
      >
        <span>{{ $t(mapOptions.find((o) => o.value === model)?.label) }}</span>

        <div class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
          <IconArrowDown
            class="w-6 h-6 transition-transform duration-200"
            :class="{ 'rotate-180': isOpen }"
          />
        </div>
      </div>

      <div
        v-if="isOpen"
        class="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden py-1"
      >
        <div
          v-for="option in mapOptions"
          :key="option.value"
          class="px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition-colors"
          @click="selectOption(option.value)"
        >
          {{ $t(option.label) }}
        </div>
      </div>
    </div>
  </div>
</template>
