<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';
import IconArrowDown from '@/components/ui/icons/IconArrowDown.vue';
import { useMarkers } from '@/composables/useMarkers';
import { useToast } from '@/composables/useToast';

const { t } = useI18n();
const { saveMarkers } = useMarkers();
const toast = useToast();

const model = defineModel({ type: String, required: true });
const isOpen = ref(false);
const wrapper = ref(null);

const mapOptions = [
  { value: 'osm', label: 'admin.style_osm' },
  { value: 'satellite', label: 'admin.style_satellite' }
];

const selectOption = async (val) => {
  model.value = val;
  isOpen.value = false;
  await saveMarkers();
  toast.show(t('admin.style_saved'));
};

const handleClickOutside = (event) => {
  if (wrapper.value && !wrapper.value.contains(event.target)) {
    isOpen.value = false;
  }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
  <div ref="wrapper" class="pt-4 border-t border-slate-100">
    <label class="text-[11px] font-semibold uppercase tracking-widest text-slate-400 block mb-2">
      {{ $t('admin.map_style') }}
    </label>

    <div class="relative">
      <div
        class="w-full pl-3 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 cursor-pointer hover:border-slate-300 transition-all flex items-center justify-between"
        :class="{ 'ring-2 ring-indigo-100 border-indigo-400 bg-white': isOpen }"
        @click="isOpen = !isOpen"
      >
        <span>{{ $t(mapOptions.find((o) => o.value === model)?.label) }}</span>
        <div class="absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
          <IconArrowDown
            class="w-5 h-5 transition-transform duration-200"
            :class="{ 'rotate-180': isOpen }"
          />
        </div>
      </div>

      <div
        v-if="isOpen"
        class="absolute z-50 w-full mt-1.5 bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden py-1"
      >
        <div
          v-for="option in mapOptions"
          :key="option.value"
          class="px-3 py-2 text-sm font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 cursor-pointer transition-colors"
          :class="{ 'text-indigo-700 bg-indigo-50': model === option.value }"
          @click="selectOption(option.value)"
        >
          {{ $t(option.label) }}
        </div>
      </div>
    </div>
  </div>
</template>
