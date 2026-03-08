/// <reference types="vite/client" />

import type { WpData } from './index';

declare global {
  interface Window {
    wpData?: WpData;
    sweetMapData?: Record<string, WpData>;
  }
}

export {};
