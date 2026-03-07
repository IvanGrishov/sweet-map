<?php
/**
 * Plugin Name: My Leaflet Map Vue
 * Version: 2.5
 * Description: Leaflet map with Vue 3 and Tailwind v4 admin interface.
 */

if (!defined('ABSPATH')) exit;

// Определяем константы для удобства
define('MLM_VUE_VERSION', '2.5');
define('MLM_VUE_URL', plugin_dir_url(__FILE__));
define('MLM_VUE_PATH', plugin_dir_path(__FILE__));

/**
 * Регистрация страницы в админке
 */

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
 * Подключение ассетов
 */
function mlm_enqueue_assets() {
  $dist_url = MLM_VUE_URL . 'assets/dist/';
  $dist_path = MLM_VUE_PATH . 'assets/dist/';

  $css_file = file_exists($dist_path . 'index.css') ? 'index.css' : 'style.css';
  if (file_exists($dist_path . $css_file)) {
    wp_enqueue_style('mlm-vue-style', $dist_url . $css_file, array(), MLM_VUE_VERSION);
  }

  wp_enqueue_script('mlm-vue-app', $dist_url . 'index.js', array(), MLM_VUE_VERSION, true);

  wp_localize_script('mlm-vue-app', 'wpData', array(
    'rest_url' => esc_url_raw(rest_url('mlm/v1')),
    'nonce'    => wp_create_nonce('wp_rest'),
    'coords'   => get_option('mlm_coords', array()),
    'locale'   => get_locale(),
    'can_edit' => is_admin() && current_user_can('manage_options'),
    'zoom' => (int) get_option('mlm_map_zoom', 13),
    'mapStyle' => get_option('mlm_map_style', 'osm'),
    'mapTitle' => get_option('mlm_map_title', ''),
  ));
}

/**
 * Загрузка только на нужной странице админки
 */
add_action('admin_enqueue_scripts', function ($hook) {
  if ($hook === 'toplevel_page_mlm-settings-page') {
    mlm_enqueue_assets();
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
 * Шорткод для вывода карты на фронтенде
 */
add_shortcode('my_map', function () {
  mlm_enqueue_assets();
  return '<div id="mlm-map-admin-root"></div>';
});

/**
 * REST API Маршруты
 */
add_action('rest_api_init', function () {
  // Маршрут должен совпадать с тем, что в useMarkers.ts: /mlm/v1/save-markers
  register_rest_route('mlm/v1', '/save-markers', array(
    'methods' => 'POST',
    'callback' => 'mlm_handle_save_markers',
    'permission_callback' => function () {
      return current_user_can('manage_options');
    }
  ));
});

/**
 * Обработчик сохранения данных
 */
function mlm_handle_save_markers($request) {
  $params = $request->get_json_params();
  $markers = $params['markers'] ?? array();
  // Достаем зум из параметров (дефолт 13, если вдруг не пришел)
  $zoom = $params['zoom'] ?? 13;

  if (!is_array($markers)) {
    return new WP_Error('invalid_data', 'Данные должны быть массивом', array('status' => 400));
  }

  $sanitized_markers = array();
  foreach ($markers as $marker) {
    $sanitized_markers[] = array(
      'id'          => sanitize_text_field($marker['id'] ?? ''),
      'lat'         => sanitize_text_field($marker['lat'] ?? ''),
      'lng'         => sanitize_text_field($marker['lng'] ?? ''),
      'title'       => sanitize_text_field($marker['title'] ?? ''),
      'description' => sanitize_textarea_field($marker['description'] ?? ''),
      'color'       => sanitize_hex_color($marker['color'] ?? '') ?? '',
      'icon'        => sanitize_text_field($marker['icon'] ?? ''),
    );
  }

  // Сохраняем маркеры
  update_option('mlm_coords', $sanitized_markers);

  // СОХРАНЯЕМ ЗУМ (приводим к числу для безопасности)
  update_option('mlm_map_zoom', intval($zoom));

  if (isset($params['map_style'])) {
    update_option('mlm_map_style', sanitize_text_field($params['map_style']));
  }

  if (isset($params['map_title'])) {
    update_option('mlm_map_title', sanitize_text_field($params['map_title']));
  }

  return new WP_REST_Response(array(
    'success' => true,
    'message' => 'Настройки сохранены',
    'debug_zoom' => intval($zoom) // Можно вернуть для отладки в консоли
  ), 200);
}

/**
 * Рендер контейнера для Vue
 */
function mlm_render_page() {
  ?>
  <div class="wrap">
    <div id="mlm-map-admin-root">
      <p>Загрузка карты...</p>
    </div>
  </div>
  <?php
}
