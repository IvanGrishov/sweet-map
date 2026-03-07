<script setup lang="ts">
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useMarkers } from '@/composables/useMarkers';

const props = defineProps<{ canEdit?: boolean }>();

const { t } = useI18n();
const { mapFlyTrigger, draftMarker, openNewMarker } = useMarkers();

interface NominatimResult {
  place_id: number;
  display_name: string;
  lat: string;
  lon: string;
}

const query = ref('');
const results = ref<NominatimResult[]>([]);
const isLoading = ref(false);
const showDropdown = ref(false);
const selectedResult = ref<NominatimResult | null>(null);

let debounceTimer: ReturnType<typeof setTimeout> | null = null;

const search = async () => {
  const q = query.value.trim();
  if (!q) { results.value = []; showDropdown.value = false; return; }

  isLoading.value = true;
  showDropdown.value = false;
  try {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=5`;
    const res = await fetch(url, {
      headers: { 'Accept-Language': navigator.language || 'ru' }
    });
    results.value = await res.json();
    showDropdown.value = results.value.length > 0;
  } catch {
    results.value = [];
  } finally {
    isLoading.value = false;
  }
};

const onInput = () => {
  selectedResult.value = null;
  if (debounceTimer) clearTimeout(debounceTimer);
  if (!query.value.trim()) { results.value = []; showDropdown.value = false; return; }
  debounceTimer = setTimeout(search, 500);
};

const select = (item: NominatimResult) => {
  query.value = item.display_name;
  showDropdown.value = false;
  selectedResult.value = item;
  mapFlyTrigger.value = { lat: Number(item.lat), lng: Number(item.lon), zoom: 15 };
};

const clearAll = () => {
  query.value = '';
  results.value = [];
  showDropdown.value = false;
  selectedResult.value = null;
};

// Short title: first segment before comma
const shortTitle = (displayName: string) => displayName.split(',')[0].trim();

const addMarker = () => {
  if (!selectedResult.value) return;
  openNewMarker();
  draftMarker.value.lat = Number(selectedResult.value.lat).toFixed(6);
  draftMarker.value.lng = Number(selectedResult.value.lon).toFixed(6);
  draftMarker.value.title = shortTitle(selectedResult.value.display_name);
  selectedResult.value = null;
};

const onBlur = () => {
  setTimeout(() => { showDropdown.value = false; }, 150);
};
</script>

<template>
  <div class="mlm-search-wrap flex flex-col gap-1.5">
    <!-- Search input -->
    <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-3 py-2 shadow-sm focus-within:border-indigo-400 focus-within:ring-1 focus-within:ring-indigo-100 transition-all relative">
      <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>

      <input
        v-model="query"
        type="text"
        :placeholder="t('admin.search_placeholder')"
        class="mlm-search-input flex-1 min-w-0"
        @input="onInput"
        @keydown.enter.prevent="search"
        @keydown.escape="showDropdown = false"
        @blur="onBlur"
      />

      <svg v-if="isLoading" class="w-4 h-4 text-indigo-400 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
      </svg>
      <button
        v-else-if="query"
        class="text-slate-300 hover:text-slate-500 transition-colors shrink-0"
        @mousedown.prevent="clearAll"
      >
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
      </button>

      <!-- Dropdown -->
      <Transition name="dropdown">
        <ul
          v-if="showDropdown"
          class="mlm-search-drop absolute top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-50 overflow-hidden"
        >
          <li
            v-for="item in results"
            :key="item.place_id"
            class="flex items-start gap-3 px-4 py-3 cursor-pointer hover:bg-indigo-50 transition-colors text-base text-slate-700 border-b border-slate-100 last:border-0"
            @mousedown.prevent="select(item)"
          >
            <svg class="w-4 h-4 mt-0.5 text-indigo-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
            <span class="line-clamp-2 leading-snug">{{ item.display_name }}</span>
          </li>
        </ul>
      </Transition>
    </div>

    <!-- Action strip — shown after address selected (admin only) -->
    <Transition name="strip">
      <div
        v-if="selectedResult && props.canEdit"
        class="flex items-center gap-2 bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-2.5"
      >
        <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
        </svg>
        <span class="flex-1 text-sm text-indigo-700 truncate">{{ shortTitle(selectedResult.display_name) }}</span>
        <button
          class="text-sm font-semibold text-white bg-indigo-500 hover:bg-indigo-600 transition-colors px-3 py-1.5 rounded-lg shrink-0"
          @click="addMarker"
        >
          + {{ t('admin.add_marker') }}
        </button>
        <button
          class="text-indigo-400 hover:text-indigo-600 transition-colors shrink-0"
          @click="selectedResult = null"
        >
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path d="M18 6 6 18M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.15s, transform 0.15s;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

.strip-enter-active,
.strip-leave-active {
  transition: opacity 0.2s, transform 0.2s;
}
.strip-enter-from,
.strip-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
