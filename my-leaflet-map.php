<?php
/**
 * Plugin Name: My Leaflet Map Vue
 * Version: 2.6
 * Description: Leaflet map with Vue 3 and Tailwind v4 admin interface.
 */

if (!defined('ABSPATH')) exit;

define('MLM_VUE_VERSION', '2.6');
define('MLM_VUE_URL', plugin_dir_url(__FILE__));
define('MLM_VUE_PATH', plugin_dir_path(__FILE__));

add_action('init', function() {
  load_plugin_textdomain('map', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

add_action('admin_menu', function () {
  add_menu_page(
    __('Vue Map', 'map'),
    __('Vue Map', 'map'),
    'manage_options',
    'mlm-settings-page',
    'mlm_render_page',
    'dashicons-location',
    30
  );
});

/**
 * Хелпер: суффикс опции по map_id
 */
function mlm_suffix($map_id) {
  return $map_id !== 'default' ? '_' . $map_id : '';
}

/**
 * Подключение ассетов
 */
function mlm_enqueue_assets($map_id = 'default') {
  $dist_url  = MLM_VUE_URL . 'assets/dist/';
  $dist_path = MLM_VUE_PATH . 'assets/dist/';
  $s         = mlm_suffix($map_id);

  $css_file = file_exists($dist_path . 'index.css') ? 'index.css' : 'style.css';
  if (file_exists($dist_path . $css_file)) {
    wp_enqueue_style('mlm-vue-style', $dist_url . $css_file, array(), MLM_VUE_VERSION);
  }

  wp_enqueue_script('mlm-vue-app', $dist_url . 'index.js', array(), MLM_VUE_VERSION, true);

  wp_localize_script('mlm-vue-app', 'wpData', array(
    'rest_url'  => esc_url_raw(rest_url('mlm/v1')),
    'nonce'     => wp_create_nonce('wp_rest'),
    'map_id'    => $map_id,
    'coords'    => get_option('mlm_coords'     . $s, array()),
    'locale'    => get_locale(),
    'can_edit'  => is_admin() && current_user_can('manage_options'),
    'zoom'      => (int) get_option('mlm_map_zoom'   . $s, 13),
    'mapStyle'  => get_option('mlm_map_style'  . $s, 'osm'),
    'mapTitle'  => get_option('mlm_map_title'  . $s, ''),
    'mapHeight' => (int) get_option('mlm_map_height' . $s, 500),
  ));
}

/**
 * Загрузка только на нужной странице админки
 */
add_action('admin_enqueue_scripts', function ($hook) {
  if ($hook === 'toplevel_page_mlm-settings-page') {
    $map_id = sanitize_key($_GET['map_id'] ?? 'default');
    mlm_enqueue_assets($map_id);
  }
});

/**
 * Поддержка модулей для Vite JS
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
  if ('mlm-vue-app' !== $handle) return $tag;
  return '<script type="module" src="' . esc_url($src) . '"></script>';
}, 10, 3);

/**
 * Шорткод: [my_map id="default"]
 */
add_shortcode('my_map', function ($atts) {
  $atts   = shortcode_atts(['id' => 'default'], $atts, 'my_map');
  $map_id = sanitize_key($atts['id']);
  mlm_enqueue_assets($map_id);
  return '<div id="mlm-map-admin-root"></div>';
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

  if (isset($params['map_style']))  update_option('mlm_map_style'  . $s, sanitize_text_field($params['map_style']));
  if (isset($params['map_title']))  update_option('mlm_map_title'  . $s, sanitize_text_field($params['map_title']));
  if (isset($params['map_height'])) update_option('mlm_map_height' . $s, intval($params['map_height']));

  // Регистрируем map_id в общем списке
  $map_ids = get_option('mlm_map_ids', ['default']);
  if (!in_array($map_id, $map_ids)) {
    $map_ids[] = $map_id;
    update_option('mlm_map_ids', $map_ids);
  }

  return new WP_REST_Response(array('success' => true), 200);
}

/**
 * Рендер страницы админки
 */
function mlm_render_page() {
  $map_id  = sanitize_key($_GET['map_id'] ?? 'default');
  $map_ids = get_option('mlm_map_ids', ['default']);

  // Добавить новый ID в список при GET-запросе с create_map
  if (!empty($_GET['create_map']) && !empty($_GET['new_map_id'])) {
    $new_id = sanitize_key($_GET['new_map_id']);
    if ($new_id && !in_array($new_id, $map_ids)) {
      $map_ids[] = $new_id;
      update_option('mlm_map_ids', $map_ids);
    }
    wp_redirect(admin_url('admin.php?page=mlm-settings-page&map_id=' . urlencode($new_id)));
    exit;
  }
  ?>
  <div class="wrap">
    <h1 style="margin-bottom:12px"><?= __('Vue Map', 'map') ?></h1>

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap">
      <!-- Переключатель карт -->
      <form method="GET" style="display:flex;align-items:center;gap:8px">
        <input type="hidden" name="page" value="mlm-settings-page">
        <label style="font-weight:600;font-size:13px"><?= __('Карта:', 'map') ?></label>
        <select name="map_id" class="regular-text" style="height:30px" onchange="this.form.submit()">
          <?php foreach ($map_ids as $id): ?>
            <option value="<?= esc_attr($id) ?>" <?= $id === $map_id ? 'selected' : '' ?>>
              <?= esc_html($id) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <code style="background:#f0f0f0;padding:3px 8px;border-radius:4px;font-size:12px">
          [my_map id="<?= esc_attr($map_id) ?>"]
        </code>
      </form>

      <!-- Создать новую карту -->
      <form method="GET" style="display:flex;align-items:center;gap:6px">
        <input type="hidden" name="page" value="mlm-settings-page">
        <input type="hidden" name="create_map" value="1">
        <input type="text" name="new_map_id" placeholder="my-new-map"
          class="regular-text" style="height:28px;width:140px" pattern="[a-z0-9\-]+" title="Только a-z, 0-9, дефис">
        <button type="submit" class="button button-secondary">+ Новая карта</button>
      </form>
    </div>

    <div id="mlm-map-admin-root">
      <p>Загрузка карты...</p>
    </div>
  </div>
  <?php
}
