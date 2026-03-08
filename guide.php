<?php
if (!defined('ABSPATH')) exit;

add_action('admin_enqueue_scripts', function ($hook) {
  if ($hook === 'sweet-map_page_mlm-docs-page') {
    wp_enqueue_style('mlm-guide', MLM_VUE_URL . 'assets/guide.css', [], MLM_VUE_VERSION);
  }
});

function mlm_render_docs() {
  ?>
  <div class="wrap mlm-guide">
    <h1 class="mlm-guide__title"><?= __('Guide', 'sweet-map') ?></h1>

    <div class="mlm-guide__body">

      <h2 class="mlm-guide__section-title">🚀 <?= __('Getting started', 'sweet-map') ?></h2>
      <p class="mlm-guide__text"><?= __('Insert the shortcode on any page, post or widget:', 'sweet-map') ?></p>
      <pre class="mlm-guide__pre">[sweet_map]</pre>

      <h2 class="mlm-guide__section-title">🧩 <?= __('Gutenberg block', 'sweet-map') ?></h2>
      <p class="mlm-guide__text"><?= __('In the block editor, click <strong>+</strong>, search "Sweet Map" and add the block. Select a map from the sidebar panel on the right — the full interactive map will appear on your page.', 'sweet-map') ?></p>

      <h2 class="mlm-guide__section-title">🗺️ <?= __('Multiple maps', 'sweet-map') ?></h2>
      <p class="mlm-guide__text"><?= __('Each map has its own markers and settings. Create maps in the Maps section and use unique IDs in shortcodes:', 'sweet-map') ?></p>
      <pre class="mlm-guide__pre">[sweet_map id="offices"]
[sweet_map id="stores"]</pre>

      <h2 class="mlm-guide__section-title mlm-guide__section-title--spaced">📍 <?= __('Marker features', 'sweet-map') ?></h2>
      <ul class="mlm-guide__list">
        <li><?= __('<strong>Title</strong> — shown as the popup heading', 'sweet-map') ?></li>
        <li><?= __('<strong>Description</strong> — additional text in the popup', 'sweet-map') ?></li>
        <li><?= __('<strong>Image</strong> — photo displayed in the popup', 'sweet-map') ?></li>
        <li><?= __('<strong>Link</strong> — "More" button linking to any URL', 'sweet-map') ?></li>
        <li><?= __('<strong>Color</strong> — custom pin color', 'sweet-map') ?></li>
        <li><?= __('<strong>Icon</strong> — upload your own PNG/SVG image as the marker', 'sweet-map') ?></li>
      </ul>

      <h2 class="mlm-guide__section-title mlm-guide__section-title--spaced">⚙️ <?= __('Map settings', 'sweet-map') ?></h2>
      <ul class="mlm-guide__list">
        <li><?= __('<strong>Default zoom</strong> — initial zoom level (1–18)', 'sweet-map') ?></li>
        <li><?= __('<strong>Map height</strong> — height of the map block in pixels', 'sweet-map') ?></li>
        <li><?= __('<strong>Map style</strong> — OpenStreetMap or Satellite', 'sweet-map') ?></li>
        <li><?= __('<strong>Show address search</strong> — hide the search bar for site visitors', 'sweet-map') ?></li>
      </ul>

      <h2 class="mlm-guide__section-title mlm-guide__section-title--spaced">✏️ <?= __('How to add a marker', 'sweet-map') ?></h2>
      <ol class="mlm-guide__list">
        <li><?= __('Click anywhere on the map — a draft marker appears', 'sweet-map') ?></li>
        <li><?= __('Fill in the title and other fields in the editor panel', 'sweet-map') ?></li>
        <li><?= __('Drag the pin to adjust position, or type coordinates directly', 'sweet-map') ?></li>
        <li><?= __('Click <strong>Save</strong> (or press Enter) — the marker is saved to the database', 'sweet-map') ?></li>
      </ol>

      <h2 class="mlm-guide__section-title mlm-guide__section-title--spaced">⌨️ <?= __('Keyboard shortcut', 'sweet-map') ?></h2>
      <p class="mlm-guide__text--last"><?= sprintf(
        __('Press %s while the marker editor is focused to save the current marker quickly.', 'sweet-map'),
        '<kbd class="mlm-guide__kbd">Enter</kbd>'
      ) ?></p>

    </div>
  </div>
  <?php
}
