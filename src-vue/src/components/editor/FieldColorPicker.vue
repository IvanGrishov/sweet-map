<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const model = defineModel<string>();
</script>

<template>
  <label class="flex items-center gap-2 cursor-pointer">
    <input
      :value="model || '#4f46e5'"
      type="color"
      class="w-7 h-7 rounded-md cursor-pointer border border-slate-200 bg-white p-0.5"
      @input="model = ($event.target as HTMLInputElement).value"
    />
    <span class="text-xs text-slate-500 font-medium">{{ t('admin.pin_color') }}</span>
  </label>
</template>
