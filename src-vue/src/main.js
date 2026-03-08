import { createApp } from 'vue';
import { createI18n } from 'vue-i18n';
import './assets/main.css';
import App from './App.vue';

import ru from './locales/ru.json';
import en from './locales/en.json';

document.querySelectorAll('.mlm-map-root').forEach((el) => {
  const mapId = el.dataset.mapId;
  const wpData = window.sweetMapData?.[mapId]
    ?? (window.wpData?.map_id === mapId ? window.wpData : null);
  if (!wpData) return;

  const locale = wpData.locale?.split('_')[0] || 'en';

  const i18n = createI18n({
    legacy: false,
    locale,
    fallbackLocale: 'en',
    messages: { ru, en }
  });

  const app = createApp(App);
  app.provide('wpData', wpData);
  app.use(i18n);
  app.mount(el);
});
