<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const model = defineModel<string>();
const { t } = useI18n();
</script>

<template>
  <div class="flex flex-col gap-1.5">
    <span class="text-sm font-semibold uppercase tracking-widest text-slate-700">
      {{ t('admin.link_url') }}
    </span>
    <div class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-2.5 focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100 focus-within:bg-white transition-all">
      <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
      </svg>
      <input
        v-model="model"
        type="url"
        :placeholder="t('admin.link_url_placeholder')"
        class="flex-1 py-2 bg-transparent text-sm text-slate-700 placeholder:text-slate-500 outline-none min-w-0"
      />
      <a
        v-if="model"
        :href="model"
        target="_blank"
        rel="noopener"
        class="text-indigo-400 hover:text-indigo-600 transition-colors shrink-0"
        :title="t('admin.open_link')"
      >
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
          <polyline points="15 3 21 3 21 9"/>
          <line x1="10" y1="14" x2="21" y2="3"/>
        </svg>
      </a>
    </div>
  </div>
</template>
