<script setup lang="ts">
interface Props {
  loading?: boolean;
  disabled?: boolean;
}

defineProps<Props>();

import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
  <button
    type="button"
    :disabled="loading || disabled"
    class="relative py-2.5 px-4 bg-indigo-600 text-white border-none rounded-lg text-sm font-semibold transition-all hover:bg-indigo-700 active:scale-95 disabled:bg-slate-200 disabled:text-slate-400 disabled:scale-100 shadow-sm flex items-center justify-center gap-2"
  >
    <template v-if="!loading">
      <slot name="icon" />
      <slot />
    </template>

    <template v-else>
      <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
      <span>{{ t('admin.saving') }}</span>
    </template>
  </button>
</template>
