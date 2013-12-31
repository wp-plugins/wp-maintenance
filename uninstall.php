<?php

/**
 * Désinstallation du plugin WP Maintenance
 */
function wpm_uninstall() {  
    if(get_option('wp_maintenance_settings')) { delete_option('wp_maintenance_settings'); }
    if(get_option('wp_maintenance_version')) { delete_option('wp_maintenance_version'); }
    if(get_option('wp_maintenance_style')) {  delete_option('wp_maintenance_style'); }
    if(get_option('wp_maintenance_limit')) {  delete_option('wp_maintenance_limit'); }
    if(get_option('wp_maintenance_active')) {  delete_option('wp_maintenance_active'); }

}
register_deactivation_hook(__FILE__, 'wpm_uninstall');
