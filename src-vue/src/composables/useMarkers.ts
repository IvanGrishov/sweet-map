import { ref, readonly } from 'vue';
import type { MarkerData } from '@/types';
import { useI18n } from 'vue-i18n';
import { useToast } from '@/composables/useToast';

const initialData = window.wpData?.coords || [];
const markers = ref<MarkerData[]>(initialData);
const isSaving = ref(false);
const activeMarkerId = ref<string | null>(null);
const mapCenterTrigger = ref<{ lat: string; lng: string } | null>(null);
const zoom = ref(window.wpData?.zoom || 10);
const mapStyle = ref(window.wpData?.mapStyle || 'osm');
const mapTitle = ref(window.wpData?.mapTitle || '');
const mapHeight = ref(window.wpData?.mapHeight || 500);

const _initDraft = (): MarkerData => {
  const base = (window.wpData?.coords as MarkerData[] | undefined)?.at(-1);
  return {
    id: crypto.randomUUID(),
    lat: base ? (Number(base.lat) + 0.002).toFixed(6) : '55.7512',
    lng: base ? (Number(base.lng) + 0.002).toFixed(6) : '37.6184',
    title: ''
  };
};

const draftMarker = ref<MarkerData>(_initDraft());
const draftIsNew = ref(true);

export function useMarkers() {
  const { t } = useI18n();
  const toast = useToast();

  const removeMarker = (id: string) => {
    markers.value = markers.value.filter((m) => m.id !== id);
  };

  const centerOnMarker = (marker: MarkerData) => {
    activeMarkerId.value = marker.id;
    mapCenterTrigger.value = {
      lat: String(marker.lat),
      lng: String(marker.lng)
    };
  };

  const saveMarkers = async () => {
    if (!window.wpData?.rest_url) return;

    isSaving.value = true;
    try {
      const response = await fetch(`${window.wpData.rest_url}/save-markers`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': window.wpData.nonce
        },
        body: JSON.stringify({
          markers: markers.value,
          zoom: Number(zoom.value),
          map_style: mapStyle.value,
          map_title: mapTitle.value,
          map_height: Number(mapHeight.value)
        })
      });
      if (!response.ok) throw new Error();
    } catch {
      alert(t('admin.error_save'));
    } finally {
      isSaving.value = false;
    }
  };

  function openNewMarker() {
    const base = markers.value.at(-1);
    draftMarker.value = {
      id: crypto.randomUUID(),
      lat: base ? (Number(base.lat) + 0.002).toFixed(6) : '55.7512',
      lng: base ? (Number(base.lng) + 0.002).toFixed(6) : '37.6184',
      title: ''
    };
    draftIsNew.value = true;
    activeMarkerId.value = null;
  }

  function openEditMarker(id: string) {
    const found = markers.value.find((m) => m.id === id);
    if (!found) return;
    draftMarker.value = { ...found };
    draftIsNew.value = false;
    centerOnMarker(found);
  }

  function cancelDraft() {
    openNewMarker();
  }

  async function saveDraft() {
    const snapshot = { ...draftMarker.value };
    if (draftIsNew.value) {
      markers.value.push(snapshot);
    } else {
      const idx = markers.value.findIndex((m) => m.id === snapshot.id);
      if (idx !== -1) markers.value[idx] = snapshot;
    }
    openNewMarker();
    await saveMarkers();
    toast.show(t('admin.marker_saved'));
  }

  async function deleteDraftMarker() {
    if (draftIsNew.value) return;
    const id = draftMarker.value.id;
    removeMarker(id);
    if (activeMarkerId.value === id) activeMarkerId.value = null;
    openNewMarker();
    await saveMarkers();
    toast.show(t('admin.marker_deleted'));
  }

  return {
    markers,
    isSaving: readonly(isSaving),
    removeMarker,
    saveMarkers,
    activeMarkerId,
    mapCenterTrigger,
    centerOnMarker,
    zoom,
    mapStyle,
    mapTitle,
    mapHeight,
    draftMarker,
    draftIsNew,
    openNewMarker,
    openEditMarker,
    cancelDraft,
    saveDraft,
    deleteDraftMarker
  };
}
