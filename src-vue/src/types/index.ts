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
  image?: string;    // URL of popup image
  link?: string;     // external link shown in popup
  showPopup?: boolean; // false = popup disabled for this marker
}

export interface WpData {
  rest_url: string;
  nonce: string;
  map_id: string;
  can_edit: boolean | number | string;
  coords: MarkerData[];
  zoom: number;
  mapStyle: string;
  mapHeight: number;
  showSearch: boolean;
  locale?: string;
}
