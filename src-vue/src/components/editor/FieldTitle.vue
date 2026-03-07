<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const model = defineModel<string>({ required: true });
</script>

<template>
  <input
    v-model="model"
    type="text"
    :placeholder="t('admin.place_name')"
    class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-white text-sm font-medium text-slate-800 placeholder:text-slate-500 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
  />
</template>
