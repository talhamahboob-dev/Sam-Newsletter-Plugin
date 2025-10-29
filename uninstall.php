<?php
/**
 * Fired when the plugin is uninstalled
 * Cleans up database tables and options
 */

// Exit if accessed directly or not uninstalling
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Drop the custom table
global $wpdb;
$table_name = $wpdb->prefix . 'sam_newsletter';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Remove plugin options
delete_option('sam_newsletter_db_version');

// For multisite installations
if (is_multisite()) {
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    
    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        
        $table_name = $wpdb->prefix . 'sam_newsletter';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
        delete_option('sam_newsletter_db_version');
        
        restore_current_blog();
    }
}