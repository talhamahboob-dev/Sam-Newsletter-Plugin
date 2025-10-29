<?php
/**
 * Admin Dashboard Page
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . SAM_NEWSLETTER_TABLE;

// Get all subscribers
$subscribers = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
$total_subscribers = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
?>

<div class="wrap sam-newsletter-admin">
    <h1><?php _e('Newsletter Subscribers', 'sam-newsletter'); ?></h1>
    
    <div class="sam-newsletter-stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo esc_html($total_subscribers); ?></div>
            <div class="stat-label"><?php _e('Total Subscribers', 'sam-newsletter'); ?></div>
        </div>
    </div>
    
    <div class="sam-newsletter-table-wrapper">
        <div class="sam-newsletter-controls">
            <div class="search-box">
                <input 
                    type="text" 
                    id="sam-newsletter-search" 
                    placeholder="<?php esc_attr_e('Search by name or email...', 'sam-newsletter'); ?>"
                />
                <button type="button" id="sam-newsletter-search-btn" class="button">
                    <?php _e('Search', 'sam-newsletter'); ?>
                </button>
                <button type="button" id="sam-newsletter-reset-btn" class="button">
                    <?php _e('Reset', 'sam-newsletter'); ?>
                </button>
            </div>
        </div>
        
        <div id="sam-newsletter-results">
            <?php if (empty($subscribers)): ?>
                <div class="no-subscribers">
                    <p><?php _e('No subscribers yet. Start collecting emails by adding the SAM Newsletter block to your pages!', 'sam-newsletter'); ?></p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col" class="column-id"><?php _e('ID', 'sam-newsletter'); ?></th>
                            <th scope="col" class="column-name"><?php _e('Name', 'sam-newsletter'); ?></th>
                            <th scope="col" class="column-email"><?php _e('Email', 'sam-newsletter'); ?></th>
                            <th scope="col" class="column-date"><?php _e('Subscribed Date', 'sam-newsletter'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="sam-newsletter-tbody">
                        <?php foreach ($subscribers as $subscriber): ?>
                            <tr>
                                <td class="column-id"><?php echo esc_html($subscriber->id); ?></td>
                                <td class="column-name"><?php echo esc_html($subscriber->name); ?></td>
                                <td class="column-email">
                                    <a href="mailto:<?php echo esc_attr($subscriber->email); ?>">
                                        <?php echo esc_html($subscriber->email); ?>
                                    </a>
                                </td>
                                <td class="column-date">
                                    <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($subscriber->created_at))); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div id="sam-newsletter-loading" style="display: none;">
            <p><?php _e('Loading...', 'sam-newsletter'); ?></p>
        </div>
    </div>
</div>