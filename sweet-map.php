<?php
/**
 * Plugin Name: Sweet Map
 * Version: 1.0
 * Author: Ivan Grishov
 * Plugin URI: https://wordpress.org/plugins/sweet-map/
 * Description: Interactive Leaflet map with a visual marker editor. Multiple maps, address search, popups with images and links. Shortcode [sweet_map].
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Tested up to: 6.9
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sweet-map
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('SWMAP_VUE_VERSION', '1.0');
define('SWMAP_VUE_URL', plugin_dir_url(__FILE__));
define('SWMAP_VUE_PATH', plugin_dir_path(__FILE__));

add_action('init', function () {
  register_block_type('sweet-map/map', [
    'render_callback' => 'swmap_render_block',
    'attributes' => [
      'mapId' => ['type' => 'string', 'default' => 'default'],
    ],
  ]);
});


/**
 * Handle new map creation and deletion (before HTML output)
 */
add_action('admin_init', function () {
  if (sanitize_text_field(wp_unslash($_GET['page'] ?? '')) !== 'swmap-settings-page' || !current_user_can('manage_options')) return;

  // Create new map
  if (isset($_GET['create_map'], $_GET['new_map_id'])) {
    check_admin_referer('swmap_create_map');
    $new_id = sanitize_key($_GET['new_map_id']);
    if (!$new_id) return;
    $map_ids = get_option('swmap_map_ids', ['default']);
    if (!in_array($new_id, $map_ids)) {
      $map_ids[] = $new_id;
      update_option('swmap_map_ids', $map_ids);
    }
    wp_safe_redirect(admin_url('admin.php?page=swmap-settings-page&map_id=' . urlencode($new_id)));
    exit;
  }

  // Delete map
  if (isset($_GET['delete_map'], $_GET['del_map_id'])) {
    $del_id = sanitize_key($_GET['del_map_id']);
    if (!$del_id || $del_id === 'default') return;
    check_admin_referer('swmap_delete_map_' . $del_id);
    $s = swmap_suffix($del_id);
    delete_option('swmap_coords'      . $s);
    delete_option('swmap_map_zoom'    . $s);
    delete_option('swmap_map_style'   . $s);
    delete_option('swmap_map_title'   . $s);
    delete_option('swmap_map_height'  . $s);
    delete_option('swmap_show_search' . $s);
    $map_ids = get_option('swmap_map_ids', ['default']);
    $map_ids = array_values(array_filter($map_ids, fn($id) => $id !== $del_id));
    update_option('swmap_map_ids', $map_ids);
    wp_safe_redirect(admin_url('admin.php?page=swmap-settings-page&map_id=default'));
    exit;
  }
});

add_action('admin_menu', function () {
  add_menu_page(
    __('Sweet Map', 'sweet-map'),
    __('Sweet Map', 'sweet-map'),
    'manage_options',
    'swmap-settings-page',
    'swmap_render_page',
    'dashicons-location',
    30
  );

  add_submenu_page(
    'swmap-settings-page',
    __('Maps', 'sweet-map'),
    __('Maps', 'sweet-map'),
    'manage_options',
    'swmap-settings-page',
    'swmap_render_page'
  );

  add_submenu_page(
    'swmap-settings-page',
    __('Guide', 'sweet-map'),
    __('Guide', 'sweet-map'),
    'manage_options',
    'swmap-docs-page',
    'swmap_render_docs'
  );
});

/**
 * Helper: returns option suffix for a given map_id
 */
function swmap_suffix($map_id) {
  return $map_id !== 'default' ? '_' . $map_id : '';
}

/**
 * Render Gutenberg block
 */
function swmap_render_block($attrs) {
  $map_id = sanitize_key($attrs['mapId'] ?? 'default');
  swmap_enqueue_assets($map_id);
  return '<div class="swmap-map-root" data-map-id="' . esc_attr($map_id) . '"></div>';
}

/**
 * Enqueue plugin assets and pass map data as inline script
 */
function swmap_enqueue_assets($map_id = 'default') {
  $dist_url  = SWMAP_VUE_URL . 'assets/dist/';
  $dist_path = SWMAP_VUE_PATH . 'assets/dist/';
  $s         = swmap_suffix($map_id);

  if (!wp_script_is('swmap-vue-app', 'enqueued')) {
    $css_file = file_exists($dist_path . 'index.css') ? 'index.css' : 'style.css';
    if (file_exists($dist_path . $css_file)) {
      wp_enqueue_style('swmap-vue-style', $dist_url . $css_file, array(), SWMAP_VUE_VERSION);
    }
    wp_enqueue_script('swmap-vue-app', $dist_url . 'index.js', array(), SWMAP_VUE_VERSION, true);
  }

  $map_data = array(
    'rest_url'   => esc_url_raw(rest_url('swmap/v1')),
    'nonce'      => wp_create_nonce('wp_rest'),
    'map_id'     => $map_id,
    'coords'     => get_option('swmap_coords'      . $s, array()),
    'locale'     => get_locale(),
    'can_edit'   => is_admin() && current_user_can('manage_options'),
    'zoom'       => (int) get_option('swmap_map_zoom'   . $s, 13),
    'mapStyle'   => get_option('swmap_map_style'  . $s, 'osm'),
    'mapHeight'  => (int) get_option('swmap_map_height'  . $s, 640),
    'showSearch' => (bool) get_option('swmap_show_search' . $s, true),
  );

  wp_add_inline_script(
    'swmap-vue-app',
    'window.sweetMapData=Object.assign(window.sweetMapData||{},' . wp_json_encode(array($map_id => $map_data)) . ');',
    'before'
  );
}

/**
 * Enqueue assets only on the plugin admin page
 */
add_action('admin_enqueue_scripts', function ($hook) {
  if ($hook === 'toplevel_page_swmap-settings-page') {
    $map_id = 'default';
    if (isset($_GET['map_id'], $_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'swmap_view')) {
      $map_id = sanitize_key(wp_unslash($_GET['map_id']));
    }
    swmap_enqueue_assets($map_id);
    wp_enqueue_style('swmap-admin', SWMAP_VUE_URL . 'assets/admin.css', [], SWMAP_VUE_VERSION);
    $maps_label = esc_js(__('Maps', 'sweet-map'));
    wp_add_inline_script('swmap-vue-app', "
      document.addEventListener('DOMContentLoaded', function() {
        var open = localStorage.getItem('swmap_toolbar_open') === '1';
        var t = document.getElementById('swmap-toolbar');
        var btn = document.getElementById('swmap-toggle-toolbar');
        if (open && t && btn) {
          t.style.display = 'flex';
          btn.textContent = '\u2715 " . $maps_label . "';
        }
      });
    ");
  }
});

/**
 * Enqueue assets for the block editor (Gutenberg)
 */
add_action('enqueue_block_editor_assets', function () {
  $maps = get_option('swmap_map_ids', ['default']);
  wp_enqueue_script(
    'sweet-map-block',
    SWMAP_VUE_URL . 'blocks/index.js',
    ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
    SWMAP_VUE_VERSION,
    true
  );
  wp_localize_script('sweet-map-block', 'swmapBlockData', [
    'maps' => $maps,
  ]);
});

/**
 * Add type="module" to the Vue app script tag (required for Vite ESM output)
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
  if ('swmap-vue-app' !== $handle) return $tag;
  // Add type="module" only to the src-based script tag, preserving inline before/after scripts
  return str_replace(
    "src='" . esc_url($src) . "'",
    "type='module' src='" . esc_url($src) . "'",
    $tag
  );
}, 10, 3);

/**
 * Shortcode: [sweet_map id="default"]
 */
add_shortcode('sweet_map', function ($atts) {
  $atts   = shortcode_atts(['id' => 'default'], $atts, 'sweet_map');
  $map_id = sanitize_key($atts['id']);
  swmap_enqueue_assets($map_id);
  return '<div class="swmap-map-root" data-map-id="' . esc_attr($map_id) . '"></div>';
});

/**
 * REST API
 */
add_action('rest_api_init', function () {
  register_rest_route('swmap/v1', '/save-markers', array(
    'methods'             => 'POST',
    'callback'            => 'swmap_handle_save_markers',
    'permission_callback' => function () {
      return current_user_can('manage_options');
    }
  ));
});

/**
 * REST API callback: save markers and map settings
 */
function swmap_handle_save_markers($request) {
  $params  = $request->get_json_params();
  $map_id  = sanitize_key($params['map_id'] ?? 'default');
  $s       = swmap_suffix($map_id);
  $markers = $params['markers'] ?? array();
  $zoom    = $params['zoom'] ?? 13;

  if (!is_array($markers)) {
    return new WP_Error('invalid_data', __('Markers must be an array.', 'sweet-map'), array('status' => 400));
  }

  $sanitized = array();
  foreach ($markers as $m) {
    $sanitized[] = array(
      'id'          => sanitize_text_field($m['id']          ?? ''),
      'lat'         => sanitize_text_field($m['lat']         ?? ''),
      'lng'         => sanitize_text_field($m['lng']         ?? ''),
      'title'       => sanitize_text_field($m['title']       ?? ''),
      'description' => sanitize_textarea_field($m['description'] ?? ''),
      'color'       => sanitize_hex_color($m['color'] ?? '') ?? '',
      'icon'        => sanitize_text_field($m['icon']  ?? ''),
      'image'       => sanitize_text_field($m['image'] ?? ''),
      'link'        => esc_url_raw($m['link'] ?? ''),
      'showPopup'   => isset($m['showPopup']) ? (bool)$m['showPopup'] : true,
    );
  }

  update_option('swmap_coords'     . $s, $sanitized,                                          false);
  update_option('swmap_map_zoom'   . $s, intval($zoom),                                        false);

  if (isset($params['map_style']))   update_option('swmap_map_style'   . $s, sanitize_text_field($params['map_style']), false);
  if (isset($params['map_height']))  update_option('swmap_map_height'  . $s, intval($params['map_height']),             false);
  if (isset($params['show_search'])) update_option('swmap_show_search' . $s, (bool) $params['show_search'],             false);

  // Register map_id in the global list if not already there
  $map_ids = get_option('swmap_map_ids', ['default']);
  if (!in_array($map_id, $map_ids)) {
    $map_ids[] = $map_id;
    update_option('swmap_map_ids', $map_ids);
  }

  return new WP_REST_Response(array('success' => true), 200);
}

require_once SWMAP_VUE_PATH . 'guide.php';

/**
 * Render admin settings page
 */
function swmap_render_page() {
  $map_id = 'default';
  if (isset($_GET['map_id'], $_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'swmap_view')) {
    $map_id = sanitize_key(wp_unslash($_GET['map_id']));
  }
  $map_ids = get_option('swmap_map_ids', ['default']);
  ?>
  <div class="wrap">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
      <h1 style="margin:0;padding:0;line-height:1.3"><?php esc_html_e('Sweet Map', 'sweet-map'); ?></h1>
      <button
        id="swmap-toggle-toolbar"
        onclick="
          var t = document.getElementById('swmap-toolbar');
          var open = t.style.display !== 'none';
          t.style.display = open ? 'none' : 'flex';
          this.textContent = open ? '&#x2699;&#xFE0F; <?php echo esc_js(__('Maps', 'sweet-map')); ?>' : '&#x2715; <?php echo esc_js(__('Maps', 'sweet-map')); ?>';
          localStorage.setItem('swmap_toolbar_open', open ? '0' : '1');
        "
        style="display:inline-flex;align-items:center;gap:6px;height:36px;padding:0 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s"
        onmouseover="this.style.background='#f1f5f9';this.style.borderColor='#94a3b8'"
        onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'"
      >&#x2699;&#xFE0F; <?php esc_html_e('Maps', 'sweet-map'); ?></button>
    </div>

    <div id="swmap-toolbar" class="swmap-toolbar" style="display:none">
      <!-- Map switcher -->
      <form method="GET" class="swmap-toolbar__group">
        <input type="hidden" name="page" value="swmap-settings-page">
        <?php wp_nonce_field('swmap_view'); ?>
        <span class="swmap-toolbar__label"><?php esc_html_e('Map:', 'sweet-map'); ?></span>
        <select name="map_id" class="swmap-toolbar__select" onchange="this.form.submit()">
          <?php foreach ($map_ids as $id) : ?>
            <option value="<?php echo esc_attr($id); ?>" <?php echo selected($id, $map_id, false); ?>>
              <?php echo esc_html($id); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <span class="swmap-toolbar__shortcode">[sweet_map id="<?php echo esc_attr($map_id); ?>"]</span>
      </form>

      <div class="swmap-toolbar__divider"></div>

      <!-- Create new map -->
      <form method="GET" class="swmap-toolbar__group">
        <input type="hidden" name="page" value="swmap-settings-page">
        <input type="hidden" name="create_map" value="1">
        <?php wp_nonce_field('swmap_create_map'); ?>
        <input type="text" name="new_map_id" placeholder="new-map-id"
          class="swmap-toolbar__input" pattern="[a-z0-9\-]+"
          title="<?php echo esc_attr(__('Only a-z, 0-9, hyphen', 'sweet-map')); ?>">
        <button type="submit" class="swmap-toolbar__btn-add">+ <?php esc_html_e('New map', 'sweet-map'); ?></button>
      </form>

      <!-- Delete map (non-default only) -->
      <?php if ($map_id !== 'default') : ?>
        <div class="swmap-toolbar__divider"></div>
        <a
          href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=swmap-settings-page&delete_map=1&del_map_id=' . urlencode($map_id)), 'swmap_delete_map_' . $map_id)); ?>"
          class="swmap-toolbar__btn-delete"
          onclick="return confirm('<?php
            /* translators: %s: map ID */
            echo esc_attr(sprintf(__('Delete map "%s" and all its markers? This cannot be undone.', 'sweet-map'), $map_id));
          ?>')"
        ><?php esc_html_e('Delete map', 'sweet-map'); ?></a>
      <?php endif; ?>
    </div>

    <div class="swmap-map-root" data-map-id="<?php echo esc_attr($map_id); ?>">
      <p><?php esc_html_e('Loading map…', 'sweet-map'); ?></p>
    </div>
  </div>
  <?php
}
