<script setup lang="ts">
interface Props {
  type?: 'button' | 'submit' | 'reset';
  variant?: 'primary' | 'danger' | 'success';
}

withDefaults(defineProps<Props>(), {
  type: 'button',
  variant: 'primary'
});

// Определяем классы в зависимости от выбранного варианта
const variantClasses = {
  primary: 'bg-slate-800 hover:bg-slate-700 text-white',
  danger: 'bg-red-600 hover:bg-red-500 text-white',
  success: 'bg-emerald-600 hover:bg-emerald-500 text-white'
};
</script>

<template>
  <button
    :type="type"
    :class="[
      'flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed',
      variantClasses[variant]
    ]"
  >
    <slot />
  </button>
</template>
