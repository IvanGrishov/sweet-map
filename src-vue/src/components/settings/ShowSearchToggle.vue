<script setup lang="ts">
import { watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useMarkers } from '@/composables/useMarkers';
import { useToast } from '@/composables/useToast';

const { t } = useI18n();
const { showSearch, saveMarkers } = useMarkers();
const toast = useToast();

watch(showSearch, async () => {
  await saveMarkers();
  toast.show(t('admin.search_visibility_saved'));
});
</script>

<template>
  <div class="pt-5 border-t border-slate-200">
    <div class="flex items-center justify-between gap-3">
      <span
        class="text-sm font-semibold uppercase tracking-widest text-slate-700 cursor-pointer select-none"
        @click="showSearch = !showSearch"
      >
        {{ t('admin.show_search') }}
      </span>

      <!-- Toggle switch — без label/checkbox во избежание двойного триггера -->
      <div
        class="relative w-9 h-5 rounded-full transition-colors duration-200 shrink-0 cursor-pointer"
        :class="showSearch ? 'bg-indigo-500' : 'bg-slate-200'"
        @click="showSearch = !showSearch"
      >
        <span
          class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"
          :class="showSearch ? 'translate-x-4' : 'translate-x-0'"
        />
      </div>
    </div>

    <p class="text-sm text-slate-600 mt-2 leading-snug">
      {{ showSearch ? t('admin.show_search_on') : t('admin.show_search_off') }}
    </p>
  </div>
</template>
