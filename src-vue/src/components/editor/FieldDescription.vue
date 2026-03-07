<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const model = defineModel<string>();
</script>

<template>
  <textarea
    v-model="model"
    :placeholder="t('admin.description_placeholder')"
    rows="3"
    class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-white text-sm text-slate-600 placeholder:text-slate-500 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none resize-none transition-all"
  />
</template>
