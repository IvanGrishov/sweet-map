<?php
if (!defined('ABSPATH')) exit;

add_action('admin_enqueue_scripts', function ($hook) {
  if ($hook === 'sweet-map_page_swmap-docs-page') {
    wp_enqueue_style('swmap-guide', SWMAP_VUE_URL . 'assets/guide.css', [], SWMAP_VUE_VERSION);
  }
});

function swmap_render_docs() {
  $kses_strong = ['strong' => []];
  $kses_kbd    = ['kbd' => ['class' => []]];
  ?>
  <div class="wrap swmap-guide">
    <h1 class="swmap-guide__title"><?php esc_html_e('Guide', 'sweet-map'); ?></h1>

    <div class="swmap-guide__body">

      <h2 class="swmap-guide__section-title">🚀 <?php esc_html_e('Getting started', 'sweet-map'); ?></h2>
      <p class="swmap-guide__text"><?php esc_html_e('Insert the shortcode on any page, post or widget:', 'sweet-map'); ?></p>
      <pre class="swmap-guide__pre">[sweet_map]</pre>

      <h2 class="swmap-guide__section-title">🧩 <?php esc_html_e('Gutenberg block', 'sweet-map'); ?></h2>
      <p class="swmap-guide__text"><?php echo wp_kses(__('In the block editor, click <strong>+</strong>, search "Sweet Map" and add the block. Select a map from the sidebar panel on the right — the full interactive map will appear on your page.', 'sweet-map'), $kses_strong); ?></p>

      <h2 class="swmap-guide__section-title">🗺️ <?php esc_html_e('Multiple maps', 'sweet-map'); ?></h2>
      <p class="swmap-guide__text"><?php esc_html_e('Each map has its own markers and settings. Create maps in the Maps section and use unique IDs in shortcodes:', 'sweet-map'); ?></p>
      <pre class="swmap-guide__pre">[sweet_map id="offices"]
[sweet_map id="stores"]</pre>

      <h2 class="swmap-guide__section-title swmap-guide__section-title--spaced">📍 <?php esc_html_e('Marker features', 'sweet-map'); ?></h2>
      <ul class="swmap-guide__list">
        <li><?php echo wp_kses(__('<strong>Title</strong> — shown as the popup heading', 'sweet-map'), $kses_strong); ?></li>
        <li><?php echo wp_kses(__('<strong>Description</strong> — additional text in the popup', 'sweet-map'), $kses_strong); ?></li>
        <li><?php echo wp_kses(__('<strong>Image</strong> — photo displayed in the popup', 'sweet-map'), $kses_strong); ?></li>
        <li><?php echo wp_kses(__('<strong>Link</strong> — "More" button linking to any URL', 'sweet-map'), $kses_strong); ?></li>
        <li><?php echo wp_kses(__('<strong>Color</strong> — custom pin color', 'sweet-map'), $kses_strong); ?></li>
        <li><?php echo wp_kses(__('<strong>Icon</strong> — upload your own PNG/SVG image as the marker', 'sweet-map'), $kses_strong); ?></li>
      </ul>

      <h2 class="swmap-guide__section-title swmap-guide__section-title--spaced">⚙️ <?php esc_html_e('Map settings', 'sweet-map'); ?></h2>
      <ul class="swmap-guide__list">
        <li><?php echo wp_kses(__('<strong>Default zoom</strong> — initial zoom level (1–18)', 'sweet-map'), $kses_strong); ?></li>
        <li><?php echo wp_kses(__('<strong>Map height</strong> — height of the map block in pixels', 'sweet-map'), $kses_strong); ?></li>
        <li><?php echo wp_kses(__('<strong>Map style</strong> — OpenStreetMap or Satellite', 'sweet-map'), $kses_strong); ?></li>
        <li><?php echo wp_kses(__('<strong>Show address search</strong> — hide the search bar for site visitors', 'sweet-map'), $kses_strong); ?></li>
      </ul>

      <h2 class="swmap-guide__section-title swmap-guide__section-title--spaced">✏️ <?php esc_html_e('How to add a marker', 'sweet-map'); ?></h2>
      <ol class="swmap-guide__list">
        <li><?php esc_html_e('Click anywhere on the map — a draft marker appears', 'sweet-map'); ?></li>
        <li><?php esc_html_e('Fill in the title and other fields in the editor panel', 'sweet-map'); ?></li>
        <li><?php esc_html_e('Drag the pin to adjust position, or type coordinates directly', 'sweet-map'); ?></li>
        <li><?php echo wp_kses(__('Click <strong>Save</strong> (or press Enter) — the marker is saved to the database', 'sweet-map'), $kses_strong); ?></li>
      </ol>

      <h2 class="swmap-guide__section-title swmap-guide__section-title--spaced">⌨️ <?php esc_html_e('Keyboard shortcut', 'sweet-map'); ?></h2>
      <p class="swmap-guide__text--last"><?php
        /* translators: %s: HTML for the keyboard key, e.g. "<kbd>Enter</kbd>" */
        $fmt = __('Press %s while the marker editor is focused to save the current marker quickly.', 'sweet-map');
        printf(wp_kses($fmt, $kses_kbd), '<kbd class="swmap-guide__kbd">Enter</kbd>');
      ?></p>

    </div>
  </div>
  <?php
}
