<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

$swmap_map_ids = get_option('swmap_map_ids', ['default']);

foreach ($swmap_map_ids as $swmap_map_id) {
  $swmap_s = $swmap_map_id !== 'default' ? '_' . $swmap_map_id : '';
  delete_option('swmap_coords'      . $swmap_s);
  delete_option('swmap_map_zoom'    . $swmap_s);
  delete_option('swmap_map_style'   . $swmap_s);
  delete_option('swmap_map_title'   . $swmap_s);
  delete_option('swmap_map_height'  . $swmap_s);
  delete_option('swmap_show_search' . $swmap_s);
}

delete_option('swmap_map_ids');
