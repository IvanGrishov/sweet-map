import { ref, readonly } from 'vue';
import type { MarkerData } from '@/types';

// Состояние выносим за пределы функции, чтобы оно было общим (как синглтон)
const markers = ref<MarkerData[]>(window.wpData?.coords || []);
const isSaving = ref(false);

export function useMarkers() {
  const addMarker = () => {
    const newMarker: MarkerData = {
      id: crypto.randomUUID(),
      lat: 55.75,
      lng: 37.61,
      title: 'Новый маркер'
    };
    markers.value.push(newMarker);
  };

  const removeMarker = (id: string) => {
    markers.value = markers.value.filter((m) => m.id !== id);
  };

  const saveMarkers = async () => {
    isSaving.value = true;
    try {
      // Тут будет твой fetch к WordPress REST API
      console.log('Сохраняем:', markers.value);
    } finally {
      isSaving.value = false;
    }
  };

  return {
    markers, // возвращаем как есть, чтобы была реактивность
    isSaving: readonly(isSaving), // извне менять нельзя, только смотреть
    addMarker,
    removeMarker,
    saveMarkers
  };
}
