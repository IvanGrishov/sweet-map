<script setup lang="ts">
import { computed } from 'vue';
import AdminSettings from './components/AdminSettings.vue';
import LMap from './components/LMap.vue';

const isDev = import.meta.env.DEV;

const canEdit = computed(() => {
  if (isDev) return true;

  const val = window.wpData?.can_edit;

  return String(val) === '1' || String(val) === 'true';
});
</script>

<template>
  <div class="mlm-plugin-root flex flex-col md:flex-row gap-5 p-4 font-sans min-h-150">
    <AdminSettings v-if="canEdit" />
    <LMap :draggable="canEdit" />
  </div>
</template>
