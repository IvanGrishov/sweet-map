<script setup lang="ts">
import { useMarkers } from '@/composables/useMarkers';

const { markers, isSaving, addMarker, removeMarker, saveMarkers } = useMarkers();
</script>

<template>
  <div>
    <button @click="addMarker">Добавить</button>
    <div v-for="marker in markers" :key="marker.id">
      {{ marker.title }}
      <button @click="removeMarker(marker.id)">Удалить</button>
    </div>
    <button :disabled="isSaving" @click="saveMarkers">
      {{ isSaving ? 'Сохраняем...' : 'Сохранить всё' }}
    </button>
  </div>
</template>
