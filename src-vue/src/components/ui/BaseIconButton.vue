<script setup lang="ts">
interface Props {
  title?: string;
  variant?: 'danger' | 'default';
}

withDefaults(defineProps<Props>(), {
  variant: 'default'
});

const variantClasses = {
  default: 'text-slate-400 hover:text-slate-600',
  danger: 'text-red-400 hover:text-red-600'
};
</script>

<template>
  <button
    type="button"
    :title="title"
    :class="[
      'opacity-0 group-hover:opacity-100 transition-all p-1 mb-0.5 disabled:opacity-30',
      variantClasses[variant]
    ]"
  >
    <slot />
  </button>
</template>
