<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const model = defineModel<string>();
</script>

<template>
  <textarea
    v-model="model"
    :placeholder="t('admin.description_placeholder')"
    rows="2"
    class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-600 placeholder:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white focus:outline-none resize-none transition-all"
  />
</template>
