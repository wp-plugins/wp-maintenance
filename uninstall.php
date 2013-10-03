<?php

/**
 * Désinstallation du plugin WP Maintenance
 */
function wpm_uninstall() {  
    if(get_option('wp_maintenance_settings')) { delete_option('wp_maintenance_settings'); }
    if(get_option('wp_maintenance_version')) { delete_option('wp_maintenance_version'); }
    if(get_option('wp_maintenance_style')) {  delete_option('wp_maintenance_style'); }
}
register_deactivation_hook(__FILE__, 'wpm_uninstall');
