<?php
/**
 * Plugin Name: Sweet Map
 * Version: 1.0
 * Description: Interactive Leaflet map with a visual marker editor. Multiple maps, address search, popups with images and links. Shortcode [sweet_map].
 * Text Domain: map
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('MLM_VUE_VERSION', '1.0');
define('MLM_VUE_URL', plugin_dir_url(__FILE__));
define('MLM_VUE_PATH', plugin_dir_path(__FILE__));

add_action('init', function() {
  load_plugin_textdomain('map', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

add_action('init', function () {
  register_block_type('sweet-map/map', [
    'render_callback' => 'mlm_render_block',
    'attributes' => [
      'mapId' => ['type' => 'string', 'default' => 'default'],
    ],
  ]);
});


/**
 * Обработка создания новой карты (до вывода HTML)
 */
add_action('admin_init', function () {
  if (($_GET['page'] ?? '') !== 'mlm-settings-page' || !current_user_can('manage_options')) return;

  // Создание новой карты
  if (isset($_GET['create_map'], $_GET['new_map_id'])) {
    $new_id = sanitize_key($_GET['new_map_id']);
    if (!$new_id) return;
    $map_ids = get_option('mlm_map_ids', ['default']);
    if (!in_array($new_id, $map_ids)) {
      $map_ids[] = $new_id;
      update_option('mlm_map_ids', $map_ids);
    }
    wp_redirect(admin_url('admin.php?page=mlm-settings-page&map_id=' . urlencode($new_id)));
    exit;
  }

  // Удаление карты
  if (isset($_GET['delete_map'], $_GET['del_map_id'])) {
    $del_id = sanitize_key($_GET['del_map_id']);
    if (!$del_id || $del_id === 'default') return;
    $s = mlm_suffix($del_id);
    delete_option('mlm_coords'      . $s);
    delete_option('mlm_map_zoom'    . $s);
    delete_option('mlm_map_style'   . $s);
    delete_option('mlm_map_title'   . $s);
    delete_option('mlm_map_height'  . $s);
    delete_option('mlm_show_search' . $s);
    $map_ids = get_option('mlm_map_ids', ['default']);
    $map_ids = array_values(array_filter($map_ids, fn($id) => $id !== $del_id));
    update_option('mlm_map_ids', $map_ids);
    wp_redirect(admin_url('admin.php?page=mlm-settings-page&map_id=default'));
    exit;
  }
});

add_action('admin_menu', function () {
  add_menu_page(
    __('Sweet Map', 'map'),
    __('Sweet Map', 'map'),
    'manage_options',
    'mlm-settings-page',
    'mlm_render_page',
    'dashicons-location',
    30
  );

  add_submenu_page(
    'mlm-settings-page',
    __('Maps', 'map'),
    __('Maps', 'map'),
    'manage_options',
    'mlm-settings-page',
    'mlm_render_page'
  );

  add_submenu_page(
    'mlm-settings-page',
    __('Guide', 'map'),
    __('Guide', 'map'),
    'manage_options',
    'mlm-docs-page',
    'mlm_render_docs'
  );
});

/**
 * Хелпер: суффикс опции по map_id
 */
function mlm_suffix($map_id) {
  return $map_id !== 'default' ? '_' . $map_id : '';
}

/**
 * Рендер Gutenberg-блока
 */
function mlm_render_block($attrs) {
  $map_id = sanitize_key($attrs['mapId'] ?? 'default');
  mlm_enqueue_assets($map_id);
  return '<div class="mlm-map-root" data-map-id="' . esc_attr($map_id) . '"></div>';
}

// Накопитель данных карт для вывода в футере
$GLOBALS['mlm_map_data'] = [];

/**
 * Подключение ассетов
 */
function mlm_enqueue_assets($map_id = 'default') {
  $dist_url  = MLM_VUE_URL . 'assets/dist/';
  $dist_path = MLM_VUE_PATH . 'assets/dist/';
  $s         = mlm_suffix($map_id);

  if (!wp_script_is('mlm-vue-app', 'enqueued')) {
    $css_file = file_exists($dist_path . 'index.css') ? 'index.css' : 'style.css';
    if (file_exists($dist_path . $css_file)) {
      wp_enqueue_style('mlm-vue-style', $dist_url . $css_file, array(), MLM_VUE_VERSION);
    }
    wp_enqueue_script('mlm-vue-app', $dist_url . 'index.js', array(), MLM_VUE_VERSION, true);
  }

  $GLOBALS['mlm_map_data'][$map_id] = array(
    'rest_url'   => esc_url_raw(rest_url('mlm/v1')),
    'nonce'      => wp_create_nonce('wp_rest'),
    'map_id'     => $map_id,
    'coords'     => get_option('mlm_coords'      . $s, array()),
    'locale'     => get_locale(),
    'can_edit'   => is_admin() && current_user_can('manage_options'),
    'zoom'       => (int) get_option('mlm_map_zoom'   . $s, 13),
    'mapStyle'   => get_option('mlm_map_style'  . $s, 'osm'),
    'mapHeight'  => (int) get_option('mlm_map_height'  . $s, 500),
    'showSearch' => (bool) get_option('mlm_show_search' . $s, true),
  );
}

/**
 * Вывод данных всех карт в футере одним тегом <script>
 */
function mlm_output_map_data() {
  if (empty($GLOBALS['mlm_map_data'])) return;
  echo '<script>window.sweetMapData=' . wp_json_encode($GLOBALS['mlm_map_data']) . ';</script>' . "\n";
}
add_action('wp_footer',    'mlm_output_map_data', 5);
add_action('admin_footer', 'mlm_output_map_data', 5);

/**
 * Загрузка только на нужной странице админки
 */
add_action('admin_enqueue_scripts', function ($hook) {
  if ($hook === 'toplevel_page_mlm-settings-page') {
    $map_id = sanitize_key($_GET['map_id'] ?? 'default');
    mlm_enqueue_assets($map_id);
    wp_enqueue_style('mlm-admin', MLM_VUE_URL . 'assets/admin.css', [], MLM_VUE_VERSION);
  }
});

/**
 * Ассеты для блочного редактора (Gutenberg)
 */
add_action('enqueue_block_editor_assets', function () {
  $maps = get_option('mlm_map_ids', ['default']);
  wp_enqueue_script(
    'sweet-map-block',
    MLM_VUE_URL . 'blocks/index.js',
    ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'],
    MLM_VUE_VERSION,
    true
  );
  wp_localize_script('sweet-map-block', 'sweetMapBlockData', [
    'maps' => $maps,
  ]);
});

/**
 * Поддержка модулей для Vite JS
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
  if ('mlm-vue-app' !== $handle) return $tag;
  return '<script type="module" src="' . esc_url($src) . '"></script>';
}, 10, 3);

/**
 * Шорткод: [sweet_map id="default"]
 */
add_shortcode('sweet_map', function ($atts) {
  $atts   = shortcode_atts(['id' => 'default'], $atts, 'sweet_map');
  $map_id = sanitize_key($atts['id']);
  mlm_enqueue_assets($map_id);
  return '<div class="mlm-map-root" data-map-id="' . esc_attr($map_id) . '"></div>';
});

/**
 * REST API
 */
add_action('rest_api_init', function () {
  register_rest_route('mlm/v1', '/save-markers', array(
    'methods'             => 'POST',
    'callback'            => 'mlm_handle_save_markers',
    'permission_callback' => function () {
      return current_user_can('manage_options');
    }
  ));
});

/**
 * Обработчик сохранения
 */
function mlm_handle_save_markers($request) {
  $params  = $request->get_json_params();
  $map_id  = sanitize_key($params['map_id'] ?? 'default');
  $s       = mlm_suffix($map_id);
  $markers = $params['markers'] ?? array();
  $zoom    = $params['zoom'] ?? 13;

  if (!is_array($markers)) {
    return new WP_Error('invalid_data', 'Данные должны быть массивом', array('status' => 400));
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
    );
  }

  update_option('mlm_coords'     . $s, $sanitized);
  update_option('mlm_map_zoom'   . $s, intval($zoom));

  if (isset($params['map_style']))   update_option('mlm_map_style'   . $s, sanitize_text_field($params['map_style']));
  if (isset($params['map_height']))  update_option('mlm_map_height'  . $s, intval($params['map_height']));
  if (isset($params['show_search'])) update_option('mlm_show_search' . $s, (bool) $params['show_search']);

  // Регистрируем map_id в общем списке
  $map_ids = get_option('mlm_map_ids', ['default']);
  if (!in_array($map_id, $map_ids)) {
    $map_ids[] = $map_id;
    update_option('mlm_map_ids', $map_ids);
  }

  return new WP_REST_Response(array('success' => true), 200);
}

require_once MLM_VUE_PATH . 'guide.php';

/**
 * Рендер страницы админки
 */
function mlm_render_page() {
  $map_id  = sanitize_key($_GET['map_id'] ?? 'default');
  $map_ids = get_option('mlm_map_ids', ['default']);

  ?>
  <div class="wrap">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
      <h1 style="margin:0;padding:0;line-height:1.3"><?= __('Sweet Map', 'map') ?></h1>
      <button
        id="mlm-toggle-toolbar"
        onclick="
          var t = document.getElementById('mlm-toolbar');
          var open = t.style.display !== 'none';
          t.style.display = open ? 'none' : 'flex';
          this.textContent = open ? '⚙️ <?= esc_js(__('Maps', 'map')) ?>' : '✕ <?= esc_js(__('Maps', 'map')) ?>';
          localStorage.setItem('mlm_toolbar_open', open ? '0' : '1');
        "
        style="display:inline-flex;align-items:center;gap:6px;height:36px;padding:0 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;font-weight:600;color:#64748b;cursor:pointer;transition:all .15s"
        onmouseover="this.style.background='#f1f5f9';this.style.borderColor='#94a3b8'"
        onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0'"
      >⚙️ <?= __('Maps', 'map') ?></button>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          var open = localStorage.getItem('mlm_toolbar_open') === '1';
          var t = document.getElementById('mlm-toolbar');
          var btn = document.getElementById('mlm-toggle-toolbar');
          if (open) {
            t.style.display = 'flex';
            btn.textContent = '✕ <?= esc_js(__('Maps', 'map')) ?>';
          }
        });
      </script>
    </div>

    <div id="mlm-toolbar" class="mlm-toolbar" style="display:none">
      <!-- Переключатель карт -->
      <form method="GET" class="mlm-toolbar__group">
        <input type="hidden" name="page" value="mlm-settings-page">
        <span class="mlm-toolbar__label"><?= __('Map:', 'map') ?></span>
        <select name="map_id" class="mlm-toolbar__select" onchange="this.form.submit()">
          <?php foreach ($map_ids as $id): ?>
            <option value="<?= esc_attr($id) ?>" <?= $id === $map_id ? 'selected' : '' ?>>
              <?= esc_html($id) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <span class="mlm-toolbar__shortcode">[sweet_map id="<?= esc_attr($map_id) ?>"]</span>
      </form>

      <div class="mlm-toolbar__divider"></div>

      <!-- Создать новую карту -->
      <form method="GET" class="mlm-toolbar__group">
        <input type="hidden" name="page" value="mlm-settings-page">
        <input type="hidden" name="create_map" value="1">
        <input type="text" name="new_map_id" placeholder="new-map-id"
          class="mlm-toolbar__input" pattern="[a-z0-9\-]+" title="Только a-z, 0-9, дефис">
        <button type="submit" class="mlm-toolbar__btn-add">+ <?= __('New map', 'map') ?></button>
      </form>

      <!-- Удалить карту (только не-default) -->
      <?php if ($map_id !== 'default'): ?>
        <div class="mlm-toolbar__divider"></div>
        <a
          href="<?= esc_url(admin_url('admin.php?page=mlm-settings-page&delete_map=1&del_map_id=' . urlencode($map_id))) ?>"
          class="mlm-toolbar__btn-delete"
          onclick="return confirm('<?= esc_attr(sprintf(__('Delete map "%s" and all its markers? This cannot be undone.', 'map'), $map_id)) ?>')"
        ><?= __('Delete map', 'map') ?></a>
      <?php endif; ?>
    </div>

    <div class="mlm-map-root" data-map-id="<?= esc_attr($map_id) ?>">
      <p>Загрузка карты...</p>
    </div>
  </div>
  <?php
}
