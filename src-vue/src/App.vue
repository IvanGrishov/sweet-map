<script setup lang="ts">
import { computed } from 'vue';
import LMap from './components/LMap.vue';
import AdminSettings from './components/AdminSettings.vue';

const isAdmin = computed(() => !window.wpData || window.wpData?.is_admin);
</script>

<template>
  <div class="mlm-plugin-root flex flex-col md:flex-row gap-5 font-sans p-4">
    <AdminSettings v-if="isAdmin" />
    <LMap :draggable="isAdmin" />
  </div>
</template>
