import { ref, readonly } from 'vue';
import type { MarkerData } from '@/types';
import { useI18n } from 'vue-i18n';

// 1. Загружаем из WP или создаем пустой массив
const initialData = window.wpData?.coords || [];
const markers = ref<MarkerData[]>(initialData);
const isSaving = ref(false);
const activeMarkerId = ref<string | null>(null);
const mapCenterTrigger = ref<{ lat: string; lng: string } | null>(null);

let isInitialized = false;

export function useMarkers() {
  const { t } = useI18n();

  // 2. ГАРАНТИРУЕМ наличие точки по умолчанию, если список пуст
  const initDefaultPoint = () => {
    // Проверяем и массив, и флаг
    if (!isInitialized && markers.value.length === 0) {
      markers.value.push({
        id: 'default-point',
        lat: '55.7512',
        lng: '37.6184',
        title: t('admin.initial_point')
      });

      isInitialized = true; // Помечаем, что закончили
    }
  };

  initDefaultPoint();

  const addMarker = (customData?: Partial<MarkerData>) => {
    const lastMarker = markers.value[markers.value.length - 1];

    // Приводим к числу сразу, чтобы математика была чистой
    const baseLat = lastMarker ? Number(lastMarker.lat) : 55.7512;
    const baseLng = lastMarker ? Number(lastMarker.lng) : 37.6184;

    const offset = markers.value.length > 0 ? 0.02 : 0;

    // Рассчитываем новые значения как числа
    const newLat = customData?.lat ? Number(customData.lat) : baseLat;
    const newLng = customData?.lng ? Number(customData.lng) : baseLng + offset;

    const newMarker: MarkerData = {
      id: crypto.randomUUID(),
      // .toFixed() теперь работает, так как newLat и newLng точно числа
      lat: newLat.toFixed(6),
      lng: newLng.toFixed(6),
      title: customData?.title || t('admin.marker_item', { index: markers.value.length + 1 })
    };

    markers.value.push(newMarker);
  };

  const removeMarker = (id: string) => {
    markers.value = markers.value.filter((m) => m.id !== id);
  };

  const centerOnMarker = (marker: MarkerData) => {
    activeMarkerId.value = marker.id;
    mapCenterTrigger.value = {
      lat: String(marker.lat),
      lng: String(marker.lng)
    };
    console.log('Trigger updated:', mapCenterTrigger.value); // Для отладки
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
        body: JSON.stringify({ markers: markers.value })
      });
      if (!response.ok) throw new Error();
    } catch {
      alert(t('admin.error_save'));
    } finally {
      isSaving.value = false;
    }
  };

  return {
    markers,
    isSaving: readonly(isSaving),
    addMarker,
    removeMarker,
    saveMarkers,
    activeMarkerId,
    mapCenterTrigger,
    centerOnMarker
  };
}
