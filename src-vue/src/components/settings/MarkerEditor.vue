<script setup lang="ts">
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useMarkers } from '@/composables/useMarkers';
import PrimaryButton from '@/components/ui/PrimaryButton.vue';
import BaseButton from '@/components/ui/BaseButton.vue';
import BaseIconButton from '@/components/ui/BaseIconButton.vue';
import IconTrash from '@/components/ui/icons/IconTrash.vue';
import IconSave from '@/components/ui/icons/IconSave.vue';

const { t } = useI18n();
const { draftMarker, draftIsNew, isSaving, saveDraft, cancelDraft, deleteDraftMarker } =
  useMarkers();

const draft = computed(() => draftMarker.value);

const fileInput = ref<HTMLInputElement | null>(null);

const onFileChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;
  const sizeKB = Math.round(file.size / 1024);
  if (sizeKB > 500) alert(t('admin.icon_size_warning', { size: sizeKB }));
  const reader = new FileReader();
  reader.onload = () => {
    draftMarker.value.icon = reader.result as string;
  };
  reader.readAsDataURL(file);
  (e.target as HTMLInputElement).value = '';
};

const removeIcon = () => {
  draftMarker.value.icon = undefined;
};
</script>

<template>
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex flex-col gap-3">
    <!-- Mode label -->
    <p class="text-[11px] font-semibold uppercase tracking-widest text-indigo-500 m-0 leading-none">
      {{ draftIsNew ? t('admin.add_marker_title') : t('admin.edit_marker_title') }}
    </p>

    <!-- Title -->
    <input
      v-model="draft.title"
      type="text"
      :placeholder="t('admin.place_name')"
      class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white focus:outline-none transition-all"
    />

    <!-- Description -->
    <textarea
      v-model="draft.description"
      :placeholder="t('admin.description_placeholder')"
      rows="2"
      class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-sm text-slate-600 placeholder:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white focus:outline-none resize-none transition-all"
    />

    <!-- Lat / Lng -->
    <div class="grid grid-cols-2 gap-2">
      <div class="flex items-center gap-1.5">
        <span class="text-[11px] font-bold text-slate-400 w-7 uppercase tracking-wider shrink-0">Lat</span>
        <input
          v-model="draft.lat"
          type="text"
          class="w-full px-2 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-mono text-slate-600 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white focus:outline-none transition-all"
        />
      </div>
      <div class="flex items-center gap-1.5">
        <span class="text-[11px] font-bold text-slate-400 w-7 uppercase tracking-wider shrink-0">Lng</span>
        <input
          v-model="draft.lng"
          type="text"
          class="w-full px-2 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-mono text-slate-600 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white focus:outline-none transition-all"
        />
      </div>
    </div>

    <!-- Appearance -->
    <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-3">
      <!-- Icon preview -->
      <div v-if="draft.icon" class="flex items-center gap-2">
        <img :src="draft.icon" class="w-7 h-7 object-contain rounded-md border border-slate-200 bg-white" />
        <button
          type="button"
          class="text-xs text-red-400 hover:text-red-600 font-medium transition-colors"
          @click="removeIcon"
        >
          {{ t('admin.remove_icon') }}
        </button>
      </div>

      <!-- Color picker -->
      <label v-else class="flex items-center gap-2 cursor-pointer">
        <input
          :value="draft.color || '#4f46e5'"
          type="color"
          class="w-7 h-7 rounded-md cursor-pointer border border-slate-200 bg-white p-0.5"
          @input="draft.color = ($event.target as HTMLInputElement).value"
        />
        <span class="text-xs text-slate-500 font-medium">{{ t('admin.pin_color') }}</span>
      </label>

      <!-- Upload -->
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
    </div>

    <!-- Actions -->
    <div class="flex gap-2 pt-0.5">
      <PrimaryButton :loading="isSaving" class="flex-1" @click="saveDraft">
        <template #icon>
          <IconSave />
        </template>
        {{ t('admin.save') }}
      </PrimaryButton>

      <BaseIconButton
        v-if="!draftIsNew"
        variant="danger"
        :title="t('admin.delete')"
        @click="deleteDraftMarker"
      >
        <IconTrash class="w-4 h-4" />
      </BaseIconButton>

      <BaseButton variant="ghost" @click="cancelDraft">
        {{ t('admin.cancel') }}
      </BaseButton>
    </div>
  </div>
</template>
