<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

$map_ids = get_option('swmap_map_ids', ['default']);

foreach ($map_ids as $map_id) {
  $s = $map_id !== 'default' ? '_' . $map_id : '';
  delete_option('swmap_coords'      . $s);
  delete_option('swmap_map_zoom'    . $s);
  delete_option('swmap_map_style'   . $s);
  delete_option('swmap_map_title'   . $s);
  delete_option('swmap_map_height'  . $s);
  delete_option('swmap_show_search' . $s);
}

delete_option('swmap_map_ids');
