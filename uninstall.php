<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

$map_ids = get_option('mlm_map_ids', ['default']);

foreach ($map_ids as $map_id) {
  $s = $map_id !== 'default' ? '_' . $map_id : '';
  delete_option('mlm_coords'      . $s);
  delete_option('mlm_map_zoom'    . $s);
  delete_option('mlm_map_style'   . $s);
  delete_option('mlm_map_height'  . $s);
  delete_option('mlm_show_search' . $s);
}

delete_option('mlm_map_ids');
