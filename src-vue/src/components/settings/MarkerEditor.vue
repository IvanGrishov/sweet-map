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

const onEnter = (e: KeyboardEvent) => {
  if ((e.target as HTMLElement).tagName === 'TEXTAREA') return;
  if (!isDirty.value || isSaving.value) return;
  saveDraft();
};
</script>

<template>
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col gap-4" @keydown.enter="onEnter">
    <!-- Mode label -->
    <div class="text-xs font-semibold uppercase tracking-widest text-indigo-500 leading-none">
      {{ draftIsNew ? t('admin.add_marker_title') : t('admin.edit_marker_title') }}
    </div>

    <!-- Popup content group -->
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex flex-col gap-3.5">
      <div class="flex items-center gap-2 text-slate-600">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <span class="text-[11px] font-bold uppercase tracking-widest">{{ t('admin.popup_content') }}</span>
        <button
          type="button"
          class="ml-auto p-1 rounded transition-colors"
          :class="draft.showPopup === false ? 'text-slate-300 hover:text-slate-500' : 'text-slate-400 hover:text-slate-600'"
          :title="draft.showPopup === false ? t('admin.popup_show') : t('admin.popup_hide')"
          @click="draft.showPopup = draft.showPopup === false ? true : false"
        >
          <!-- eye-off when hidden -->
          <svg v-if="draft.showPopup === false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
            <line x1="1" y1="1" x2="23" y2="23"/>
          </svg>
          <!-- eye when visible -->
          <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>
      <template v-if="draft.showPopup !== false">
        <FieldTitle v-model="draft.title" />
        <FieldDescription v-model="draft.description" />
        <FieldImage v-model="draft.image" />
        <FieldLink v-model="draft.link" />
      </template>
    </div>

    <!-- Lat / Lng -->
    <div class="flex flex-col gap-2 pt-4 border-t border-slate-100">
      <div class="flex items-center gap-2 text-slate-600">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="12" cy="10" r="3"/><path d="M12 2C8.13 2 5 5.13 5 10c0 5.25 7 12 7 12s7-6.75 7-12c0-4.87-3.13-8-7-8z"/>
        </svg>
        <span class="text-[11px] font-bold uppercase tracking-widest">{{ t('admin.position') }}</span>
      </div>
      <div class="grid grid-cols-2 gap-2.5">
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold text-slate-600 w-7 uppercase tracking-wider shrink-0">Lat</span>
          <input
            v-model="draft.lat"
            type="text"
            class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs font-mono text-slate-600 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white focus:outline-none transition-all"
          />
        </div>
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold text-slate-600 w-7 uppercase tracking-wider shrink-0">Lng</span>
          <input
            v-model="draft.lng"
            type="text"
            class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-slate-50 text-xs font-mono text-slate-600 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white focus:outline-none transition-all"
          />
        </div>
      </div>
    </div>

    <!-- Appearance -->
    <div class="flex flex-col gap-2 pt-4 border-t border-slate-100">
      <div class="flex items-center gap-2 text-slate-600">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
        </svg>
        <span class="text-[11px] font-bold uppercase tracking-widest">{{ t('admin.marker_appearance') }}</span>
      </div>
      <div class="flex items-center justify-between gap-3">
        <FieldColorPicker v-if="!draft.icon" v-model="draft.color" />
        <FieldIconUpload v-model="draft.icon" />
      </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-2.5 pt-4 border-t border-slate-100">
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
