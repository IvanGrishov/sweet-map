import { ref, readonly } from 'vue';
import type { MarkerData } from '@/types';

// Инициализируем из данных WP или создаем пустой массив
const initialMarkers =
  window.wpData?.coords && window.wpData.coords.length > 0 ? window.wpData.coords : [];

const markers = ref<MarkerData[]>(initialMarkers);
const isSaving = ref(false);

// Если при загрузке нет ни одной точки, добавляем одну дефолтную сразу
if (markers.value.length === 0) {
  markers.value.push({
    id: crypto.randomUUID(),
    lat: 55.7512,
    lng: 37.6184,
    title: 'Начальная точка'
  });
}

export function useMarkers() {
  const addMarker = () => {
    const newMarker: MarkerData = {
      id: crypto.randomUUID(),
      // Берем координаты последней точки или центр Москвы
      lat: markers.value.length > 0 ? markers.value[markers.value.length - 1].lat : 55.7512,
      lng: markers.value.length > 0 ? markers.value[markers.value.length - 1].lng : 37.6184,
      title: 'Новая точка'
    };
    markers.value.push(newMarker);
  };

  const removeMarker = (id: string) => {
    markers.value = markers.value.filter((m) => m.id !== id);
  };

  const saveMarkers = async () => {
    if (!window.wpData?.rest_url || !window.wpData?.nonce) {
      console.warn('Режим разработки: данные сохраняются локально');
      return;
    }

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

      if (!response.ok) throw new Error('Ошибка сохранения');
      console.log('Сохранено в WordPress');
    } catch (error) {
      console.error(error);
      alert('Ошибка при сохранении');
    } finally {
      isSaving.value = false;
    }
  };

  return {
    markers,
    isSaving: readonly(isSaving),
    addMarker,
    removeMarker,
    saveMarkers
  };
}
