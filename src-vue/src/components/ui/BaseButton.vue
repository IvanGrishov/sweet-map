<script setup lang="ts">
interface Props {
  type?: 'button' | 'submit' | 'reset';
  variant?: 'primary' | 'ghost' | 'danger' | 'success';
}

withDefaults(defineProps<Props>(), {
  type: 'button',
  variant: 'primary'
});

const variantClasses = {
  primary:
    'bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 hover:border-indigo-300',
  ghost:
    'bg-white text-slate-500 border border-slate-200 hover:bg-slate-50 hover:text-slate-700',
  danger: 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 hover:border-red-300',
  success: 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'
};
</script>

<template>
  <button
    :type="type"
    :class="[
      'flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all shadow-none disabled:opacity-50 disabled:cursor-not-allowed',
      variantClasses[variant]
    ]"
  >
    <slot />
  </button>
</template>
