/**
 * Основной интерфейс маркера на карте
 */
export interface MarkerData {
  id: string;
  lat: string | number;
  lng: string | number;
  title: string;
}
