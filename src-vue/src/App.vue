<script setup lang="ts">
import { computed } from 'vue';
import AdminSettings from './components/AdminSettings.vue';
import LMap from './components/LMap.vue';

const isDev = import.meta.env.DEV;

const canEdit = computed(() => {
  // На локалхосте всегда разрешаем
  if (isDev) return true;

  const val = window.wpData?.can_edit;
  // Разрешаем только если это реальная единица (число или строка) или true
  return val === 1 || val === '1' || val === true;
});

console.log('Доступ к настройкам:', canEdit.value);
</script>

<template>
  <div class="mlm-plugin-root flex flex-col md:flex-row gap-5 p-4 font-sans min-h-150">
    <AdminSettings v-if="canEdit" />
    <LMap :draggable="canEdit" />
  </div>
</template>
