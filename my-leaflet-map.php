<?php
/**
 * Plugin Name: My Leaflet Map Vue
 * Version: 2.3
 */

if (!defined('ABSPATH')) exit;

add_action('admin_menu', function () {
  add_menu_page('Карта Vue', 'Карта Vue', 'manage_options', 'mlm-settings-page', 'mlm_render_page', 'dashicons-location');
});

function mlm_enqueue_assets() {
  $dist_url = plugin_dir_url(__FILE__) . 'assets/dist/';
  $dist_path = plugin_dir_path(__FILE__) . 'assets/dist/';

  $css_file = file_exists($dist_path . 'style.css') ? 'style.css' : 'index.css';
  wp_enqueue_style('mlm-vue-style', $dist_url . $css_file, array(), '1.3');
  wp_enqueue_script('mlm-vue-app', $dist_url . 'index.js', array(), '1.3', true);

  // ВАЖНО: Гарантируем, что coords — это всегда массив для Vue
  $coords = get_option('mlm_coords');
  if (empty($coords) || !is_array($coords)) {
    // Тестовая точка по умолчанию (например, Москва)
    $coords = array(
      array(
        'id'    => 'test-1',
        'lat'   => '55.7512',
        'lng'   => '37.6184',
        'title' => 'Тестовая точка',
      )
    );
  }

  wp_localize_script('mlm-vue-app', 'wpData', array(
    'rest_url' => esc_url_raw(rest_url('mlm/v1')),
    'nonce'    => wp_create_nonce('wp_rest'),
    'coords'   => $coords,
    'is_admin' => is_admin()
  ));
}

add_action('admin_enqueue_scripts', function ($hook) {
  if ($hook === 'toplevel_page_mlm-settings-page') {
    mlm_enqueue_assets();
  }
});

add_shortcode('my_map', function () {
  mlm_enqueue_assets();
  return '<div id="mlm-map-admin-root"></div>';
});

add_action('rest_api_init', function () {
  register_rest_route('mlm/v1', '/save', array(
    'methods' => 'POST',
    'callback' => function ($request) {
      $params = $request->get_json_params();
      $markers = isset($params['markers']) ? $params['markers'] : array();

      $sanitized = array();
      if (is_array($markers)) {
        foreach ($markers as $m) {
          $sanitized[] = array(
            'id'    => sanitize_text_field($m['id'] ?? ''),
            'lat'   => sanitize_text_field($m['lat'] ?? ''),
            'lng'   => sanitize_text_field($m['lng'] ?? ''),
            'title' => sanitize_text_field($m['title'] ?? ''),
          );
        }
      }
      update_option('mlm_coords', $sanitized);
      return array('success' => true);
    },
    'permission_callback' => function () {
      return current_user_can('manage_options');
    }
  ));
});

add_filter('script_loader_tag', function ($tag, $handle, $src) {
  if ('mlm-vue-app' !== $handle) return $tag;
  return '<script type="module" src="' . esc_url($src) . '"></script>';
}, 10, 3);

function mlm_render_page() {
  echo '<div class="wrap"><h1>Настройки карты</h1><div id="mlm-map-admin-root"></div></div>';
}
