<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import BaseIconButton from '@/components/ui/BaseIconButton.vue';
import IconTrash from '@/components/ui/icons/IconTrash.vue';
import { MarkerData } from '@/types';

const { t } = useI18n();
const marker = defineModel<MarkerData>({ required: true });

defineEmits<{
  remove: [id: string];
  select: [marker: MarkerData];
}>();

const fileInput = ref<HTMLInputElement | null>(null);

const onFileChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;

  const sizeKB = Math.round(file.size / 1024);
  if (sizeKB > 500) {
    alert(t('admin.icon_size_warning', { size: sizeKB }));
  }

  const reader = new FileReader();
  reader.onload = () => {
    marker.value.icon = reader.result as string;
  };
  reader.readAsDataURL(file);

  // Reset input so the same file can be re-selected
  (e.target as HTMLInputElement).value = '';
};

const removeIcon = () => {
  marker.value.icon = undefined;
};
</script>

<template>
  <div
    class="group p-4 rounded-xl border border-slate-100 bg-slate-50 transition-all hover:border-indigo-200 hover:shadow-sm cursor-pointer"
    @click="$emit('select', marker)"
  >
    <div class="flex flex-col gap-2">
      <div class="flex justify-between items-center">
        <input
          v-model="marker.title"
          type="text"
          :placeholder="$t('admin.place_name')"
          class="bg-transparent font-bold text-slate-700 border-none p-0 focus:ring-0 placeholder:text-slate-400 text-sm outline-none"
        />
        <BaseIconButton variant="danger" title="Удалить точку" @click="$emit('remove', marker.id)">
          <IconTrash class="w-5 h-5" />
        </BaseIconButton>
      </div>
      <div class="flex justify-between gap-2">
        <div class="flex flex-col gap-1 flex-1">
          <div class="flex items-center gap-1.5">
            <span class="text-[0.625rem] font-bold text-slate-400 w-6 uppercase">Lat</span>
            <input
              v-model="marker.lat"
              type="text"
              class="bg-white px-1.5 py-0.5 rounded border border-slate-100 text-[0.75rem] font-mono text-slate-600 w-full focus:border-indigo-300 focus:outline-none transition-colors"
            />
          </div>
          <div class="flex items-center gap-1.5">
            <span class="text-[0.625rem] font-bold text-slate-400 w-6 uppercase">Lng</span>
            <input
              v-model="marker.lng"
              type="text"
              class="bg-white px-1.5 py-0.5 rounded border border-slate-100 text-[0.75rem] font-mono text-slate-600 w-full focus:border-indigo-300 focus:outline-none transition-colors"
            />
          </div>
        </div>
      </div>

      <!-- Marker appearance -->
      <div class="pt-1 border-t border-slate-200" @click.stop>
        <p class="text-[0.625rem] font-bold text-slate-400 uppercase mb-1.5">
          {{ $t('admin.marker_appearance') }}
        </p>

        <!-- Custom icon -->
        <div v-if="marker.icon" class="flex items-center gap-2">
          <img
            :src="marker.icon"
            class="w-8 h-8 object-contain rounded border border-slate-200 bg-white"
          />
          <button
            type="button"
            class="text-[0.7rem] text-red-500 hover:text-red-700 font-medium"
            @click="removeIcon"
          >
            {{ $t('admin.remove_icon') }}
          </button>
        </div>

        <!-- Color picker (hidden when icon is set) + upload button -->
        <div v-else class="flex items-center gap-3">
          <label class="flex items-center gap-1.5 cursor-pointer">
            <span class="text-[0.7rem] text-slate-500">{{ $t('admin.pin_color') }}</span>
            <input
              :value="marker.color || '#4f46e5'"
              type="color"
              class="w-6 h-6 rounded cursor-pointer border border-slate-200"
              @input="marker.color = ($event.target as HTMLInputElement).value"
            />
          </label>
        </div>

        <!-- Upload button always visible -->
        <div class="mt-1.5">
          <button
            type="button"
            class="text-[0.7rem] text-indigo-600 hover:text-indigo-800 font-medium"
            @click="fileInput?.click()"
          >
            {{ $t('admin.upload_icon') }}
          </button>
          <input
            ref="fileInput"
            type="file"
            accept=".svg,.png,.jpg,.jpeg"
            class="hidden"
            @change="onFileChange"
          />
        </div>
      </div>
    </div>
  </div>
</template>
