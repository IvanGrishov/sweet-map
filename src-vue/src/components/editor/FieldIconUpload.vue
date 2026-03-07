<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const model = defineModel<string | undefined>();

const fileInput = ref<HTMLInputElement | null>(null);

const onFileChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;
  const sizeKB = Math.round(file.size / 1024);
  if (sizeKB > 500) alert(t('admin.icon_size_warning', { size: sizeKB }));
  const reader = new FileReader();
  reader.onload = () => {
    model.value = reader.result as string;
  };
  reader.readAsDataURL(file);
  (e.target as HTMLInputElement).value = '';
};

const remove = () => {
  model.value = undefined;
};
</script>

<template>
  <!-- Icon exists: preview + remove -->
  <div v-if="model" class="flex items-center gap-2">
    <img :src="model" class="w-7 h-7 object-contain rounded-md border border-slate-200 bg-white" />
    <button
      type="button"
      class="text-xs text-red-400 hover:text-red-600 font-medium transition-colors"
      @click="remove"
    >
      {{ t('admin.remove_icon') }}
    </button>
  </div>

  <!-- No icon: upload trigger -->
  <template v-else>
    <button
      type="button"
      class="text-xs text-indigo-500 hover:text-indigo-700 font-medium transition-colors"
      @click="fileInput?.click()"
    >
      {{ t('admin.upload_icon') }}
    </button>
    <input
      ref="fileInput"
      type="file"
      accept=".svg,.png,.jpg,.jpeg"
      class="hidden"
      @change="onFileChange"
    />
  </template>
</template>
