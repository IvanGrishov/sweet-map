<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const model = defineModel<string>();
const { t } = useI18n();
</script>

<template>
  <div class="flex flex-col gap-1.5">
    <span class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">
      {{ t('admin.image_url') }}
    </span>

    <!-- Preview -->
    <div v-if="model" class="relative rounded-lg overflow-hidden border border-slate-200 bg-slate-50">
      <img :src="model" class="w-full h-24 object-cover" @error="model = ''" />
      <button
        class="absolute top-1.5 right-1.5 bg-white/90 hover:bg-white text-slate-500 hover:text-red-500 rounded-md p-0.5 transition-colors"
        @click="model = ''"
      >
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <input
      :value="model"
      type="url"
      :placeholder="t('admin.image_url_placeholder')"
      class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs text-slate-600 placeholder:text-slate-300 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white focus:outline-none transition-all"
      @input="model = ($event.target as HTMLInputElement).value"
    />
  </div>
</template>
