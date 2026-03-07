/// <reference types="vite/client" />

import type { MarkerData } from './index';

declare global {
  interface Window {
    wpData?: {
      rest_url: string;
      nonce: string;
      map_id: string;
      is_admin: boolean;
      can_edit: boolean | number | string;
      coords: MarkerData[];
      zoom: number;
      mapStyle: string;
      mapHeight: number;
      showSearch: boolean;
    };
  }
}

// Это нужно, чтобы TS воспринимал файл как модуль
export {};
