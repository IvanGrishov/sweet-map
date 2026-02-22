/// <reference types="vite/client" />

import type { MarkerData } from './index';

// Расширяем глобальный объект Window
declare global {
  interface Window {
    wpData?: {
      rest_url: string;
      nonce: string;
      is_admin: boolean;
      can_edit: boolean;
      coords: MarkerData[];
    };
  }
}

// Это нужно, чтобы TS воспринимал файл как модуль
export {};
