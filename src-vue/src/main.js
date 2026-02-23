import { createApp } from 'vue';
import { createI18n } from 'vue-i18n';
import './assets/main.css';
import App from './App.vue';

import ru from './locales/ru.json';
import en from './locales/en.json';

const wpLocale = window.wpData?.locale?.split('_')[0] || 'ru';

const i18n = createI18n({
  legacy: false,
  locale: wpLocale,
  fallbackLocale: 'en',
  messages: {
    ru,
    en
  }
});

const app = createApp(App);

app.use(i18n);
app.mount('#mlm-map-admin-root');
