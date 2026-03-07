<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useMarkers } from '@/composables/useMarkers';
import PrimaryButton from '@/components/ui/PrimaryButton.vue';
import BaseButton from '@/components/ui/BaseButton.vue';
import BaseIconButton from '@/components/ui/BaseIconButton.vue';
import IconTrash from '@/components/ui/icons/IconTrash.vue';
import IconSave from '@/components/ui/icons/IconSave.vue';
import FieldTitle from '@/components/editor/FieldTitle.vue';
import FieldDescription from '@/components/editor/FieldDescription.vue';
import FieldColorPicker from '@/components/editor/FieldColorPicker.vue';
import FieldIconUpload from '@/components/editor/FieldIconUpload.vue';
import FieldImage from '@/components/editor/FieldImage.vue';
import FieldLink from '@/components/editor/FieldLink.vue';

const { t } = useI18n();
const { draftMarker, draftIsNew, isSaving, isDirty, saveDraft, cancelDraft, deleteDraftMarker } =
  useMarkers();

const draft = computed(() => draftMarker.value);

const isShaking = ref(false);
watch(isDirty, (val, oldVal) => {
  if (val && !oldVal) {
    isShaking.value = true;
    setTimeout(() => { isShaking.value = false; }, 600);
  }
});
</script>

<template>
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex flex-col gap-3">
    <!-- Mode label -->
    <p class="text-[11px] font-semibold uppercase tracking-widest text-indigo-500 m-0 leading-none">
      {{ draftIsNew ? t('admin.add_marker_title') : t('admin.edit_marker_title') }}
    </p>

    <FieldTitle v-model="draft.title" />

    <FieldDescription v-model="draft.description" />

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

    <FieldImage v-model="draft.image" />

    <FieldLink v-model="draft.link" />

    <!-- Appearance -->
    <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-3">
      <FieldColorPicker v-if="!draft.icon" v-model="draft.color" />
      <FieldIconUpload v-model="draft.icon" />
    </div>

    <!-- Actions -->
    <div class="flex gap-2 pt-0.5">
      <PrimaryButton
        :loading="isSaving"
        :disabled="!isDirty"
        :class="['flex-1', isShaking ? 'btn-shake' : '']"
        @click="saveDraft"
      >
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

<style scoped>
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  15%       { transform: translateX(-4px); }
  35%       { transform: translateX(4px); }
  55%       { transform: translateX(-3px); }
  75%       { transform: translateX(3px); }
}
.btn-shake {
  animation: shake 0.5s ease-in-out;
}
</style>
