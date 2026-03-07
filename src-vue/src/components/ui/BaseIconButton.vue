<script setup lang="ts">
interface Props {
  title?: string;
  variant?: 'danger' | 'default';
}

withDefaults(defineProps<Props>(), {
  variant: 'default'
});

const variantClasses = {
  default:
    'text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg p-1.5 transition-all',
  danger: 'text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg p-1.5 transition-all'
};
</script>

<template>
  <button type="button" :title="title" :class="variantClasses[variant]">
    <slot />
  </button>
</template>
