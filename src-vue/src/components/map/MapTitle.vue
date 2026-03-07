<script setup lang="ts">
import { watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useMarkers } from '@/composables/useMarkers';
import { useToast } from '@/composables/useToast';

const props = defineProps<{ canEdit: boolean }>();

const { t } = useI18n();
const { mapTitle, saveMarkers } = useMarkers();
const toast = useToast();

let timer: ReturnType<typeof setTimeout> | null = null;

watch(mapTitle, () => {
  if (!props.canEdit) return;
  if (timer) clearTimeout(timer);
  timer = setTimeout(async () => {
    await saveMarkers();
    toast.show(t('admin.title_saved'));
  }, 700);
});
</script>

<template>
  <!-- Admin: inline editable input styled as heading -->
  <input
    v-if="canEdit"
    v-model="mapTitle"
    type="text"
    :placeholder="t('admin.map_title_placeholder')"
    class="mlm-title-input w-full"
  />

  <!-- Frontend: static heading -->
  <h2
    v-else-if="mapTitle"
    class="text-2xl font-bold text-slate-800 m-0"
  >
    {{ mapTitle }}
  </h2>
</template>
