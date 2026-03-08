import { ref, readonly, computed, inject } from 'vue';
import type { MarkerData, WpData } from '@/types';
import { useI18n } from 'vue-i18n';
import { useToast } from '@/composables/useToast';

export const MARKERS_STORE_KEY = Symbol('markersStore');

export function createMarkersStore(data: WpData) {
  const markers = ref<MarkerData[]>(data.coords || []);
  const isSaving = ref(false);
  const activeMarkerId = ref<string | null>(null);
  const mapCenterTrigger = ref<{ lat: string; lng: string } | null>(null);
  const mapFlyTrigger = ref<{ lat: number; lng: number; zoom?: number } | null>(null);
  const zoom = ref(data.zoom || 10);
  const mapStyle = ref(data.mapStyle || 'osm');
  const mapHeight = ref(data.mapHeight || 640);
  const showSearch = ref<boolean>(data.showSearch ?? true);
  const mapId = data.map_id || 'default';

  const _initDraft = (): MarkerData => {
    const base = (data.coords as MarkerData[] | undefined)?.at(-1);
    return {
      id: crypto.randomUUID(),
      lat: base ? (Number(base.lat)).toFixed(6) : '55.7512',
      lng: base ? (Number(base.lng) + 0.08).toFixed(6) : '37.6184',
      title: ''
    };
  };

  const draftMarker = ref<MarkerData>(_initDraft());
  const draftIsNew = ref(true);
  const draftSnapshot = ref(JSON.stringify(draftMarker.value));
  const isDirty = computed(() => JSON.stringify(draftMarker.value) !== draftSnapshot.value);

  function removeMarker(id: string) {
    markers.value = markers.value.filter((m) => m.id !== id);
  }

  function centerOnMarker(marker: MarkerData) {
    activeMarkerId.value = marker.id;
    mapCenterTrigger.value = {
      lat: String(marker.lat),
      lng: String(marker.lng)
    };
  }

  async function saveMarkers() {
    if (!data.rest_url) return;

    isSaving.value = true;
    try {
      const response = await fetch(`${data.rest_url}/save-markers`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': data.nonce
        },
        body: JSON.stringify({
          map_id: mapId,
          markers: markers.value,
          zoom: Number(zoom.value),
          map_style: mapStyle.value,
          map_height: Number(mapHeight.value),
          show_search: showSearch.value,
        })
      });
      if (!response.ok) throw new Error();
    } catch {
      // toast handled in saveDraft/deleteDraftMarker
    } finally {
      isSaving.value = false;
    }
  }

  function openNewMarker() {
    const base = markers.value.at(-1);
    draftMarker.value = {
      id: crypto.randomUUID(),
      lat: base ? (Number(base.lat)).toFixed(6) : '55.7512',
      lng: base ? (Number(base.lng) + 0.08).toFixed(6) : '37.6184',
      title: ''
    };
    draftIsNew.value = true;
    activeMarkerId.value = null;
    draftSnapshot.value = JSON.stringify(draftMarker.value);
  }

  function openEditMarker(id: string) {
    const found = markers.value.find((m) => m.id === id);
    if (!found) return;
    draftMarker.value = { ...found };
    draftIsNew.value = false;
    draftSnapshot.value = JSON.stringify(draftMarker.value);
    centerOnMarker(found);
  }

  function cancelDraft() {
    openNewMarker();
  }

  return {
    markers,
    isSaving: readonly(isSaving),
    removeMarker,
    saveMarkers,
    activeMarkerId,
    mapCenterTrigger,
    centerOnMarker,
    mapFlyTrigger,
    zoom,
    mapStyle,
    mapHeight,
    showSearch,
    draftMarker,
    draftIsNew,
    isDirty,
    openNewMarker,
    openEditMarker,
    cancelDraft,
  };
}

export function useMarkers() {
  const store = inject(MARKERS_STORE_KEY) as ReturnType<typeof createMarkersStore>;
  const { t } = useI18n();
  const toast = useToast();

  async function saveDraft() {
    const snapshot = { ...store.draftMarker.value };
    if (store.draftIsNew.value) {
      store.markers.value.push(snapshot);
    } else {
      const idx = store.markers.value.findIndex((m) => m.id === snapshot.id);
      if (idx !== -1) store.markers.value[idx] = snapshot;
    }
    store.openNewMarker();
    await store.saveMarkers();
    toast.show(t('admin.marker_saved'));
  }

  async function deleteDraftMarker() {
    if (store.draftIsNew.value) return;
    const id = store.draftMarker.value.id;
    store.removeMarker(id);
    if (store.activeMarkerId.value === id) store.activeMarkerId.value = null;
    store.openNewMarker();
    await store.saveMarkers();
    toast.show(t('admin.marker_deleted'));
  }

  return {
    ...store,
    saveDraft,
    deleteDraftMarker,
  };
}
