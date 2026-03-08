# 🗺️ Sweet Map — WordPress Plugin

Interactive Leaflet map with a visual marker editor. Multiple maps, address search, popups with images and links. No API keys, no registration, completely free.

**Shortcode:** `[sweet_map]` · `[sweet_map id="offices"]`

---

## ✨ Features

- 📍 Visual marker editor — click the map to place a marker, drag to reposition
- 🗂️ Multiple maps — create unlimited maps with unique IDs
- 💬 Rich popups — title, description, photo, link button
- 🎨 Custom markers — pin color or custom PNG/SVG icon
- 🔍 Address search — powered by Nominatim, no API key required
- 🔵 Marker clustering — nearby markers group automatically
- 🧩 Gutenberg block — add maps via the block editor
- 🛰️ Map styles — OpenStreetMap or Satellite view
- 📌 Geolocation — "Find my location" button
- 🌍 Multilingual — English and Russian included

## 🛠️ Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP (WordPress Plugin API, REST API) |
| Frontend | Vue 3 + TypeScript |
| Map | Leaflet 1.9.4 |
| Build | Vite 8 |
| Styles | Tailwind CSS 4 |
| i18n | vue-i18n 9 |

## 🚀 Development

```bash
cd src-vue
yarn install
yarn dev       # dev server at :5173
yarn build     # build to assets/dist/
```

## 📄 License

GPL v2 or later
