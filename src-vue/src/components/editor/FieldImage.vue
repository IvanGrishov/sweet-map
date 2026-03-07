<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const model = defineModel<string>();
const { t } = useI18n();

const fileInput = ref<HTMLInputElement | null>(null);

const onFileChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;
  const sizeKB = Math.round(file.size / 1024);
  if (sizeKB > 1024) alert(t('admin.image_size_warning', { size: sizeKB }));
  const reader = new FileReader();
  reader.onload = () => { model.value = reader.result as string; };
  reader.readAsDataURL(file);
  (e.target as HTMLInputElement).value = '';
};
</script>

<template>
  <div class="flex flex-col gap-1.5">
    <span class="text-sm font-semibold uppercase tracking-widest text-slate-700">
      {{ t('admin.image') }}
    </span>

    <!-- Preview -->
    <div v-if="model" class="relative rounded-lg overflow-hidden border border-slate-200 bg-slate-50">
      <img :src="model" class="w-full h-28 object-cover block" />
      <button
        type="button"
        class="absolute top-1.5 right-1.5 bg-white/90 hover:bg-white text-slate-500 hover:text-red-500 rounded-md p-1 transition-colors shadow-sm"
        @click="model = ''"
      >
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
      </button>
      <button
        type="button"
        class="absolute bottom-1.5 right-1.5 bg-white/90 hover:bg-white text-indigo-500 hover:text-indigo-700 rounded-md px-2 py-0.5 text-xs font-medium transition-colors shadow-sm"
        @click="fileInput?.click()"
      >
        {{ t('admin.replace_image') }}
      </button>
    </div>

    <!-- Upload button -->
    <button
      v-else
      type="button"
      class="flex items-center justify-center gap-2 w-full py-3 rounded-lg border-2 border-dashed border-slate-300 hover:border-indigo-400 hover:bg-indigo-50 text-slate-500 hover:text-indigo-600 text-sm font-medium transition-all"
      @click="fileInput?.click()"
    >
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="17 8 12 3 7 8"/>
        <line x1="12" y1="3" x2="12" y2="15"/>
      </svg>
      {{ t('admin.upload_image') }}
    </button>

    <input
      ref="fileInput"
      type="file"
      accept=".jpg,.jpeg,.png,.webp"
      class="hidden"
      @change="onFileChange"
    />
  </div>
</template>
