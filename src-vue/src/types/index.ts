/**
 * Основной интерфейс маркера на карте
 */
export interface MarkerData {
  id: string;
  lat: string | number;
  lng: string | number;
  title: string;
  description?: string;
  color?: string;  // hex, e.g. '#4f46e5'
  icon?: string;   // base64 data URL, replaces the pin completely
  image?: string;  // URL of popup image
  link?: string;   // external link shown in popup
}
