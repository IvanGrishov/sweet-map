<script setup lang="ts">
interface Props {
  loading?: boolean;
  disabled?: boolean;
}

defineProps<Props>();
</script>

<template>
  <button
    type="button"
    :disabled="loading || disabled"
    class="relative w-full py-4 px-4 bg-indigo-600 text-white border-none rounded-xl font-bold transition-all hover:bg-indigo-700 active:scale-95 disabled:bg-slate-300 disabled:scale-100 shadow-lg shadow-indigo-100 flex items-center justify-center gap-2"
  >
    <template v-if="!loading">
      <slot name="icon" />
      <slot />
    </template>

    <template v-else>
      <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
      <span>Запись...</span>
    </template>
  </button>
</template>
