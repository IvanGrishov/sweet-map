=== Sweet Map ===
Contributors: your-wp-username
Tags: map, leaflet, interactive map, markers, address search
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

🗺️ Interactive map with a visual marker editor. No API keys, no registration, completely free. Gutenberg block + shortcode.

== Description ==

**Sweet Map** adds a beautiful interactive map to your WordPress site — with a visual marker editor, address search, and rich popups. No coding required.

Built on [Leaflet.js](https://leafletjs.com/) — the leading open-source map library trusted by millions of websites worldwide.

= 🆓 100% Free — No API Keys, No Registration =

Unlike Google Maps or Mapbox, Sweet Map works completely out of the box:

* ✅ **No Google account** needed
* ✅ **No API key** to generate or manage
* ✅ **No billing** — no credit card, no usage limits
* ✅ **No registration** on any third-party service
* ✅ **No monthly fees** — ever

Just install, activate, and start adding markers.

= ✨ Features =

* 📍 **Visual marker editor** — click the map to place a marker, drag to reposition
* 🗂️ **Multiple maps** — create unlimited maps, each with its own markers and settings
* 💬 **Rich popups** — title, description, photo, and a link button in every marker
* 🎨 **Custom markers** — choose a pin color or upload your own PNG/SVG icon
* 🔍 **Address search** — find any location instantly, no API key required
* 🔵 **Marker clustering** — nearby markers group automatically at low zoom levels
* 🧩 **Gutenberg block** — add maps via the block editor with a visual map selector
* 📋 **Shortcode** — `[sweet_map]` or `[sweet_map id="offices"]`
* 🛰️ **Map styles** — OpenStreetMap or Satellite view
* 📌 **Geolocation** — "Find my location" button on the map
* 🌍 **Multilingual** — English and Russian included

= 🚀 Add a map in seconds =

**Option 1 — Gutenberg block:**
Click **+** in the page editor → search "Sweet Map" → select the block → pick a map from the sidebar. Done.

**Option 2 — Shortcode:**
`[sweet_map]`
`[sweet_map id="offices"]`

= 🔒 External services =

This plugin uses open, free services to display maps and search addresses:

* **OpenStreetMap** (default map tiles) — [openstreetmap.org](https://www.openstreetmap.org/). [Copyright & terms](https://www.openstreetmap.org/copyright).
* **Stadia Maps** (satellite tiles) — [stadiamaps.com](https://stadiamaps.com/). [Terms](https://stadiamaps.com/terms-of-service/). [Privacy policy](https://stadiamaps.com/privacy-policy/).
* **Nominatim** (address search) — [nominatim.openstreetmap.org](https://nominatim.openstreetmap.org/). [Privacy policy](https://osmfoundation.org/wiki/Privacy_Policy).

Map tiles load when the map is displayed on the page. Address search sends a query only when the visitor types in the search box. The plugin does not store any personal data.

== Installation ==

1. Upload the `sweet-map` folder to `/wp-content/plugins/`
2. Activate the plugin via **Plugins** in WordPress admin
3. Go to **Sweet Map** in the admin sidebar to create and edit maps
4. Add `[sweet_map]` to any page or post, or use the Gutenberg block

== Frequently Asked Questions ==

= Do I need a Google Maps API key? =

No. Sweet Map is built on Leaflet.js and OpenStreetMap — completely free, no API key, no Google account, no billing.

= How do I add a map to a page? =

Two ways: use the Gutenberg block (click **+**, search "Sweet Map") or paste the shortcode `[sweet_map]` anywhere on the page.

= Can I have multiple different maps? =

Yes. Create as many maps as you need in the Sweet Map admin panel. Each map gets a unique ID. Use `[sweet_map id="offices"]` to embed a specific map.

= Can visitors see the address search bar? =

You control this in the map settings. Show or hide the search bar for your site visitors independently per map.

= Can visitors add or edit markers? =

No. Only site Administrators can add, edit, or delete markers. Visitors can only view the map and search addresses.

= Does it work with page builders? =

Yes — paste the shortcode `[sweet_map]` in any page builder that supports shortcodes.

== Screenshots ==

1. Map editor with sidebar — add and edit markers visually
2. Marker popup with photo, description and link button
3. Gutenberg block in the page editor — map selector in sidebar
4. Address search with location suggestions
5. Map settings — zoom, height, style, search visibility

== Changelog ==

= 1.0 =
* Initial release
