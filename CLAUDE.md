# Sweet Map — WordPress Plugin

## О проекте

WordPress-плагин, добавляющий интерактивную карту Leaflet с Vue 3 в качестве интерфейса администратора. Позволяет управлять маркерами через визуальный редактор и встраивать карту на сайт через шорткод `[sweet_map]`.

## Стек

| Слой | Технология |
|------|-----------|
| Backend | PHP (WordPress Plugin API, REST API) |
| Frontend | Vue 3 + TypeScript |
| Карта | Leaflet 1.9.4 |
| Сборка | Vite 8 |
| Стили | Tailwind CSS 4 |
| i18n | vue-i18n 9 |

## Структура

```
sweet-map/
├── sweet-map.php             # PHP: точка входа плагина, REST API, шорткод
├── assets/dist/              # Скомпилированный JS/CSS (не редактировать вручную)
│   ├── index.js
│   └── index.css
├── languages/                # WordPress .po/.mo переводы
└── src-vue/                  # Исходник Vue-приложения
    ├── vite.config.js         # Выход: ../assets/dist/, без хэшей в именах
    ├── src/
    │   ├── main.js            # Инициализация Vue + i18n, монтирование в #mlm-map-admin-root
    │   ├── App.vue            # Корневой компонент (лейаут sidebar + карта)
    │   ├── composables/
    │   │   └── useMarkers.ts  # Центральное состояние: маркеры, зум, стиль карты
    │   ├── components/
    │   │   ├── LMap.vue           # Leaflet-карта: рендер, drag, click, sync
    │   │   ├── AdminSettings.vue  # Сайдбар: список маркеров, сохранение
    │   │   ├── MarkerItem.vue     # Редактор одного маркера
    │   │   ├── MapZoomControl.vue # Слайдер зума
    │   │   ├── MapStyleSelect.vue # Выбор стиля карты
    │   │   └── ui/                # Базовые UI-компоненты и SVG-иконки
    │   ├── constants/index.ts  # URL тайловых слоёв (OSM, Satellite)
    │   ├── types/
    │   │   ├── index.ts        # TypeScript-интерфейсы (Marker и др.)
    │   │   └── global.d.ts     # window.wpData типы
    │   └── locales/
    │       ├── en.json
    │       └── ru.json
    └── package.json
```

## Команды

```bash
# Разработка (из src-vue/)
npm run dev      # Vite dev-сервер :5173

# Сборка в assets/dist/
npm run build

# Линтинг
npm run lint
```

## Поток данных

1. PHP загружает скомпилированный `assets/dist/index.js` на странице `/wp-admin`
2. Данные передаются через `wp_localize_script()` как `window.wpData` (REST URL, nonce, маркеры, зум, стиль, locale)
3. Vue монтируется в `#mlm-map-admin-root`
4. Изменения сохраняются через `fetch POST /mlm/v1/save-markers` → `update_option()`

## Ключевые решения

- **`useMarkers.ts`** — единственный источник истины для состояния (маркеры, зум, стиль)
- **Нет Vue Router / Pinia** — composable достаточно для масштаба проекта
- **Без хэшей в именах файлов** (Vite config) — PHP хардкодит пути к `index.js` / `index.css`
- **Locale** определяется автоматически из `window.wpData.locale`
- **Безопасность**: REST API защищён nonce + capability check (`manage_options`)

## Стиль кода

- Компоненты на `<script setup>` + TypeScript
- CSS: Tailwind utility-классы, минимум кастомных стилей
- Именование: компоненты PascalCase, composables `useXxx`
- i18n: все строки через `$t('key')` / `t('key')`, не хардкодить текст
