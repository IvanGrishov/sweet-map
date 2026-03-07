<script setup lang="ts">
import { useToast } from '@/composables/useToast';

const { message } = useToast();
</script>

<template>
  <Transition name="toast">
    <div
      v-if="message"
      class="fixed bottom-6 right-6 z-[9999] flex items-center gap-2.5 px-4 py-3 bg-slate-800 text-white text-sm font-medium rounded-xl shadow-xl"
    >
      <!-- Checkmark -->
      <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
      </svg>
      {{ message }}
    </div>
  </Transition>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.25s ease;
}
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(8px) scale(0.97);
}
</style>
